<?php

namespace App\Services;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Handles document uploads for any owner model exposing a `documents()`
 * relation (e.g. Application/ApplicationDocument, Profile/ProfileDocument).
 */
class DocumentUploadService
{
    protected string $disk = 'local';

    public function store(Model $owner, DocumentType $type, UploadedFile $file, string $pathPrefix): Model
    {
        $filename = sprintf('%s-%s.%s', $type->value, Str::uuid(), $file->getClientOriginalExtension());
        $path = $file->storeAs("{$pathPrefix}/{$owner->id}", $filename, $this->disk);

        return $owner->documents()->create([
            'type' => $type,
            'disk_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    /**
     * Replace any existing document(s) of this type on the owner with the
     * newly uploaded file. Used for single-file document slots that a
     * candidate can re-upload to correct a mistake.
     */
    public function replace(Model $owner, DocumentType $type, UploadedFile $file, string $pathPrefix): Model
    {
        $owner->documents()->where('type', $type)->get()->each(
            fn (Model $document) => $this->delete($document)
        );

        return $this->store($owner, $type, $file, $pathPrefix);
    }

    public function delete(Model $document): void
    {
        Storage::disk($this->disk)->delete($document->disk_path);
        $document->delete();
    }

    public function disk(): string
    {
        return $this->disk;
    }
}
