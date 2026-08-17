<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDocumentRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\MembershipApplication;
use App\Services\SecureDocumentService;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    public function store(UploadDocumentRequest $r, MembershipApplication $application, SecureDocumentService $s): RedirectResponse
    {
        $this->authorize('update', $application);
        $s->store($r->user(), $application, DocumentType::findOrFail($r->integer('document_type_id')), $r->file('document'));

        return back()->with('status', 'Document uploaded securely.');
    }

    public function download(Document $document, SecureDocumentService $s)
    {
        $this->authorize('view', $document);

        return $s->download($document);
    }
}
