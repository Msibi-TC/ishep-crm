<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureDocumentService
{
    public function store(User $owner, MembershipApplication $app, DocumentType $type, UploadedFile $file): Document
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $stored = Str::uuid().'.'.$ext;
        $path = $file->storeAs('membership-documents/'.$owner->id, $stored, 'local');

        return Document::create(['owner_user_id' => $owner->id, 'membership_application_id' => $app->id, 'document_type_id' => $type->id, 'storage_disk' => 'local', 'storage_path' => $path, 'original_name' => basename($file->getClientOriginalName()), 'stored_name' => $stored, 'mime_type' => $file->getMimeType(), 'size_bytes' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath()), 'uploaded_at' => now()]);
    }

    public function download(Document $document)
    {
        return Storage::disk($document->storage_disk)->download($document->storage_path, $document->original_name);
    }
}
