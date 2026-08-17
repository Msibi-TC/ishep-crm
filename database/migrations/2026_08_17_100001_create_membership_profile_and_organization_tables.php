<?php

use App\Enums\OrganizationStatus;
use App\Enums\ProfileStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('middle_names')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('profession_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('profile_status')->default(ProfileStatus::Incomplete->value)->index();
            $table->timestamps();
        });
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('registration_number')->nullable()->index();
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->text('physical_address')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->default(OrganizationStatus::Draft->value)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('member_profiles');
    }
};
