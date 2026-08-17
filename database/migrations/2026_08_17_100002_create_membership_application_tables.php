<?php

use App\Enums\ApplicationQueryStatus;
use App\Enums\MembershipApplicationStatus;
use App\Enums\StudentEligibilityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_number')->unique();
            $table->string('status')->default(MembershipApplicationStatus::Draft->value)->index();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamp('declaration_accepted_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
        Schema::create('student_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('institution_name')->nullable();
            $table->string('grade')->nullable();
            $table->unsignedSmallInteger('academic_year')->nullable();
            $table->string('eligibility_status')->default(StudentEligibilityStatus::Pending->value)->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
        });
        Schema::create('application_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_application_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status')->index();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::create('application_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raised_by')->constrained('users')->restrictOnDelete();
            $table->text('message');
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('status')->default(ApplicationQueryStatus::Open->value)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_queries');
        Schema::dropIfExists('application_status_history');
        Schema::dropIfExists('student_eligibilities');
        Schema::dropIfExists('membership_applications');
    }
};
