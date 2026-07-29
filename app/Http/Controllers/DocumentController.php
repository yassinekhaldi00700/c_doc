<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Stream the document inline so it renders in the browser's native
     * PDF viewer instead of triggering a download.
     */
    public function preview(ApplicationDocument $document)
    {
        $this->authorize('view', $document);

        abort_unless(Storage::disk('local')->exists($document->disk_path), 404);

        return Storage::disk('local')->response(
            $document->disk_path,
            $document->original_name,
            ['Content-Type' => $document->mime_type ?: 'application/pdf'],
            'inline'
        );
    }
}
