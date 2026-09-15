<?php

namespace App\Services;

use App\Models\ResearchSubject;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use RuntimeException;
use ZipArchive;

/** Edit only the variable paragraphs and rows; retain all other DOCX parts. */
class RecruitmentReportDocument
{
    private const NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    public function generate(ResearchSubject $subject, array $data): string
    {
        $template = base_path('PV-Recrutement-Canvas.docx');
        if (! is_file($template)) {
            throw new RuntimeException('The recruitment Word template is missing. Deploy PV-Recrutement-Canvas.docx with the application.');
        }
        $path = tempnam(sys_get_temp_dir(), 'recruitment-');
        if ($path === false) {
            throw new RuntimeException('Cannot create the recruitment document.');
        }
        $zip = new ZipArchive;
        try {
            if (! copy($template, $path) || $zip->open($path) !== true) {
                throw new RuntimeException('Cannot open the recruitment template.');
            }
            $xml = new DOMDocument;
            $xml->preserveWhiteSpace = true;
            if (! $xml->loadXML($zip->getFromName('word/document.xml'), LIBXML_NONET)) {
                throw new RuntimeException('Invalid recruitment template XML.');
            }
            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace('w', self::NS);
            $nodes = iterator_to_array($xpath->query('//w:body/*'));
            if (count($nodes) !== 107 || $nodes[23]->localName !== 'tbl' || $nodes[63]->localName !== 'tbl') {
                throw new RuntimeException('The recruitment template layout has changed; update its field mappings before exporting.');
            }
            $subject->load('professor');
            $applications = $subject->applications()->with('candidate.profile')->orderBy('id')->get()->keyBy('id');
            $this->text($xpath, $nodes[4], 'Intitulé du sujet de doctorat : « '.$subject->title.' »');
            $this->text($xpath, $nodes[8], $subject->description);
            $this->text($xpath, $nodes[11], $this->member(['name' => $subject->professor->name, 'email' => $subject->professor->email, 'institution' => 'UEMF']));
            $this->text($xpath, $nodes[14], filled($data['co_director_name'] ?? null) ? $this->member([
                'name' => $data['co_director_name'], 'email' => $data['co_director_email'], 'institution' => $data['co_director_institution'] ?? '',
            ]) : 'Non renseigné');

            // The jury/committee shown on the PV is the thesis director, then the
            // co-director (if any), then the 3 committee members chosen on the form —
            // never just the 3 chosen members on their own.
            $panel = [['name' => $subject->professor->name, 'email' => $subject->professor->email, 'institution' => 'UEMF']];
            if (filled($data['co_director_name'] ?? null)) {
                $panel[] = ['name' => $data['co_director_name'], 'email' => $data['co_director_email'], 'institution' => $data['co_director_institution'] ?? ''];
            }
            array_push($panel, ...$data['committee']);

            foreach ([32, 47] as $start) {
                for ($i = 0; $i < 5; $i++) {
                    $this->text($xpath, $nodes[$start + $i], isset($panel[$i]) ? $this->member($panel[$i]) : '');
                }
            }
            foreach ([67, 70, 73, 76, 79] as $i => $index) {
                $this->text($xpath, $nodes[$index], isset($panel[$i]) ? 'Pr. '.$panel[$i]['name'] : '');
            }

            $candidateRows = [];
            foreach ($applications->values() as $i => $application) {
                $profile = $application->candidate->profile;
                $candidateRows[] = [$i + 1, $profile?->last_name ?: $application->candidate->name, $profile?->first_name ?? '', $application->candidate->email];
            }
            $this->table($xpath, $nodes[23], $candidateRows);
            foreach (['shortlist' => 44, 'interviews' => 63] as $field => $index) {
                $rows = [];
                foreach ($data[$field] as $i => $result) {
                    $application = $applications->get($result['application_id']);
                    if (! $application) {
                        throw new RuntimeException('A selected application no longer belongs to this subject. Please review the report.');
                    }
                    $profile = $application->candidate->profile;
                    $rows[] = [$i + 1, $profile?->last_name ?: $application->candidate->name, $profile?->first_name ?? '', $result['score'], $i + 1];
                }
                $this->table($xpath, $nodes[$index], $rows);
            }
            $this->text($xpath, $nodes[105], 'Date : '.\Carbon\Carbon::parse($data['report_date'])->format('d/m/Y'));
            $this->updateHeaderReference($zip, $data);
            if (! $zip->addFromString('word/document.xml', $xml->saveXML()) || ! $zip->close()) {
                throw new RuntimeException('Cannot save the recruitment document.');
            }
            return $path;
        } catch (\Throwable $exception) {
            if ($zip->status === ZipArchive::ER_OK) {
                // Close before removing a temporary file on Windows.
                try { $zip->close(); } catch (\Throwable) { }
            }
            @unlink($path);
            throw $exception;
        }
    }

    /** The header shows "Réf. : CED-DDMMYY"; keep that date in step with the PV date instead of the template's dummy digits. */
    private function updateHeaderReference(ZipArchive $zip, array $data): void
    {
        $name = 'word/header1.xml';
        $xml = new DOMDocument;
        $xml->preserveWhiteSpace = true;
        if (! $xml->loadXML($zip->getFromName($name), LIBXML_NONET)) {
            throw new RuntimeException('Invalid recruitment template header XML.');
        }
        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace('w', self::NS);
        $texts = iterator_to_array($xpath->query('//w:t'));
        if (count($texts) !== 11 || $texts[4]->textContent !== 'CED-') {
            throw new RuntimeException('The recruitment template header layout has changed; update its reference number mapping before exporting.');
        }
        $digits = str_split(\Carbon\Carbon::parse($data['report_date'])->format('dmy'));
        foreach (array_slice($texts, 5, 6) as $i => $text) {
            $text->textContent = $digits[$i];
        }
        if (! $zip->addFromString($name, $xml->saveXML())) {
            throw new RuntimeException('Cannot save the recruitment document header.');
        }
    }

    private function member(array $member): string
    {
        return 'Pr. '.$member['name'].(filled($member['institution'] ?? null) ? ', '.$member['institution'] : '').', e-mail : '.$member['email'];
    }

    private function text(DOMXPath $xpath, DOMNode $node, string $value): void
    {
        $texts = iterator_to_array($xpath->query('.//w:t', $node));
        if ($texts === []) {
            $paragraph = $node->localName === 'p' ? $node : $xpath->query('.//w:p', $node)->item(0);
            $run = $node->ownerDocument->createElementNS(self::NS, 'w:r');
            $text = $node->ownerDocument->createElementNS(self::NS, 'w:t');
            $run->appendChild($text);
            $paragraph->appendChild($run);
            $texts[] = $text;
        }
        foreach ($texts as $i => $text) {
            $text->textContent = $i === 0 ? $value : '';
            $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
            $run = $text->parentNode;
            if ($run instanceof DOMElement && $run->localName === 'r') {
                $highlight = $xpath->query('w:rPr/w:highlight', $run)->item(0);
                $highlight?->parentNode->removeChild($highlight);
            }
        }
    }

    private function table(DOMXPath $xpath, DOMElement $table, array $values): void
    {
        $rows = iterator_to_array($xpath->query('./w:tr', $table));
        $prototype = $rows[1]->cloneNode(true);
        foreach (array_slice($rows, 1) as $row) {
            $table->removeChild($row);
        }
        foreach ($values as $cells) {
            $row = $prototype->cloneNode(true);
            foreach ($xpath->query('./w:tc', $row) as $index => $cell) {
                $this->text($xpath, $cell, (string) ($cells[$index] ?? ''));
            }
            $table->appendChild($row);
        }
    }
}
