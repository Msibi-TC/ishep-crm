<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('fee', 12, 2)->default(0);
            $table->string('billing_period');
            $table->boolean('is_student')->default(false)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_types');
        Schema::dropIfExists('professions');
        Schema::dropIfExists('provinces');
    }
};
