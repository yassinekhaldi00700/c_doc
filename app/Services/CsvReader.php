<?php

namespace App\Services;

class CsvReader
{
    /**
     * Reads a CSV file into rows keyed by the trimmed, lowercased header
     * names from the first line, so column order in the uploaded file
     * doesn't matter. Blank lines are skipped.
     *
     * @return array<int, array<string, string>>
     */
    public static function rows(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $rows = [];
        $header = null;

        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null]) {
                continue;
            }

            if ($header === null) {
                $header = array_map(
                    fn ($cell) => strtolower(trim((string) preg_replace('/^\x{FEFF}/u', '', (string) $cell))),
                    $line
                );

                continue;
            }

            $row = [];
            foreach ($header as $i => $key) {
                if ($key !== '') {
                    $row[$key] = trim((string) ($line[$i] ?? ''));
                }
            }

            if (array_filter($row, fn ($value) => $value !== '') !== []) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }
}
