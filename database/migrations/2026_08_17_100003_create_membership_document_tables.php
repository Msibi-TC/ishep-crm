<?php

use App\Enums\DocumentReviewStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('allowed_mime_types');
            $table->unsignedInteger('maximum_size_kb')->default(5120);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_application_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->restrictOnDelete();
            $table->string('storage_disk');
            $table->string('storage_path')->unique();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size_bytes');
            $table->string('checksum', 64)->index();
            $table->string('review_status')->default(DocumentReviewStatus::Pending->value)->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('membership_application_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('membership_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('membership_number')->nullable()->unique();
            $table->string('status')->index();
            $table->date('start_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->text('suspended_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
        Schema::create('membership_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status')->index();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_status_history');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
    }
};
