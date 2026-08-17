<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['identity_document', 'Identity Document'], ['company_registration', 'Company Registration'], ['grade_12_proof', 'Grade 12 Proof'],
            ['tertiary_student_card', 'Tertiary Student Card'], ['tertiary_registration', 'Tertiary Registration'], ['tertiary_application_proof', 'Tertiary Application Proof'],
            ['other_supporting_document', 'Other Supporting Document']] as [$code,$name]) {
            DocumentType::updateOrCreate(['code' => $code], ['name' => $name, 'allowed_mime_types' => ['application/pdf', 'image/jpeg', 'image/png'], 'maximum_size_kb' => 5120, 'active' => true]);
        }
    }
}
