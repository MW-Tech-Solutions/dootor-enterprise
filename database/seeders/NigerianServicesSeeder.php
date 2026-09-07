<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class NigerianServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // 1. Identity & Financial Documentation
            [
                'name' => 'NIN Registration & Verification Services',
                'category' => 'Identity & Financial Documentation',
                'price' => 50.00,
                'service_fee' => 10.00,
                'processing_fee' => 5.00,
                'processing_days' => 3,
                'description' => 'Official Nigerian National Identification Number (NIN) enrollment, modification, and verification for local and diaspora applicants.',
                'status' => 'Active',
                'required_documents' => ['Passport Photograph', 'Proof of Address', 'Existing ID / Birth Document'],
                'custom_fields' => [
                    ['name' => 'full_name', 'label' => 'Full Legal Name', 'type' => 'text', 'required' => true],
                    ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'required' => true],
                    ['name' => 'state_of_origin', 'label' => 'State of Origin', 'type' => 'text', 'required' => true],
                    ['name' => 'nin_number', 'label' => 'Existing NIN (If Modifying)', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'name' => 'BVN Registration & Update Services',
                'category' => 'Identity & Financial Documentation',
                'price' => 45.00,
                'service_fee' => 10.00,
                'processing_fee' => 5.00,
                'processing_days' => 2,
                'description' => 'Bank Verification Number (BVN) processing, phone number/name updates, and verification for diaspora banking compliance.',
                'status' => 'Active',
                'required_documents' => ['Valid Passport / ID', 'NIN Slip', 'Bank Statement Excerpt'],
                'custom_fields' => [
                    ['name' => 'bvn_number', 'label' => 'BVN Number (If updating)', 'type' => 'text', 'required' => false],
                    ['name' => 'bank_name', 'label' => 'Associated Nigerian Bank', 'type' => 'text', 'required' => true],
                    ['name' => 'account_number', 'label' => 'Nigerian Bank Account Number', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'International Passport Processing & Renewal',
                'category' => 'Identity & Financial Documentation',
                'price' => 250.00,
                'service_fee' => 30.00,
                'processing_fee' => 20.00,
                'processing_days' => 14,
                'description' => 'Application processing, appointment scheduling, and fast-track clearance for Nigerian International Passports (Fresh & Renewal).',
                'status' => 'Active',
                'required_documents' => ['Passport Photograph (White BG)', 'Expired Passport / Data Page', 'NIN Verification Slip', 'Birth Certificate / NPC Attestation'],
                'custom_fields' => [
                    ['name' => 'passport_type', 'label' => 'Passport Booklet Type', 'type' => 'select', 'options' => ['32 Pages - 5 Years', '64 Pages - 5 Years', '64 Pages - 10 Years'], 'required' => true],
                    ['name' => 'nin', 'label' => 'NIN Number', 'type' => 'text', 'required' => true],
                    ['name' => 'application_type', 'label' => 'Application Type', 'type' => 'select', 'options' => ['Fresh Application', 'Renewal / Expired', 'Re-issue (Lost/Damaged)'], 'required' => true],
                ],
            ],

            // 2. Travel & Immigration Services
            [
                'name' => 'Emergency Travel Certificate (ETC)',
                'category' => 'Travel & Immigration Services',
                'price' => 180.00,
                'service_fee' => 25.00,
                'processing_fee' => 15.00,
                'processing_days' => 5,
                'description' => 'Issuance of Emergency Travel Certificates for Nigerians abroad needing urgent travel back to Nigeria without an active passport.',
                'status' => 'Active',
                'required_documents' => ['Passport Photograph', 'Expired Passport / Lost Report', 'Flight Itinerary', 'Proof of Emergency'],
                'custom_fields' => [
                    ['name' => 'departure_country', 'label' => 'Current Country of Residence', 'type' => 'text', 'required' => true],
                    ['name' => 'travel_date', 'label' => 'Intended Travel Date', 'type' => 'date', 'required' => true],
                    ['name' => 'reason_for_etc', 'label' => 'Reason for Emergency Travel', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'Single Entry Visa (SEV)',
                'category' => 'Travel & Immigration Services',
                'price' => 300.00,
                'service_fee' => 40.00,
                'processing_fee' => 25.00,
                'processing_days' => 7,
                'description' => 'Single Entry Visa application support and document submission for foreign nationals visiting Nigeria.',
                'status' => 'Active',
                'required_documents' => ['Applicant International Passport', 'Passport Photo', 'Letter of Invitation from Host', 'Host Passport / CERPAC', 'Hotel / Accomodation Proof'],
                'custom_fields' => [
                    ['name' => 'nationality', 'label' => 'Applicant Nationality', 'type' => 'text', 'required' => true],
                    ['name' => 'purpose_of_visit', 'label' => 'Purpose of Visit', 'type' => 'select', 'options' => ['Business', 'Tourism', 'Visiting Family/Friends', 'Conference'], 'required' => true],
                    ['name' => 'entry_date', 'label' => 'Expected Entry Date', 'type' => 'date', 'required' => true],
                ],
            ],
            [
                'name' => 'Multiple Entry Visa (MEV)',
                'category' => 'Travel & Immigration Services',
                'price' => 500.00,
                'service_fee' => 50.00,
                'processing_fee' => 30.00,
                'processing_days' => 10,
                'description' => 'Long-term Multiple Entry Visa clearance for frequent business travelers and investors visiting Nigeria.',
                'status' => 'Active',
                'required_documents' => ['International Passport (Valid 6+ months)', 'Passport Photo', 'Corporate Letter of Request', 'Company CAC Registration in Nigeria'],
                'custom_fields' => [
                    ['name' => 'duration', 'label' => 'Visa Duration Requested', 'type' => 'select', 'options' => ['6 Months', '1 Year', '2 Years', '5 Years'], 'required' => true],
                    ['name' => 'company_name', 'label' => 'Sponsoring Corporate Entity', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'Temporary Work Permit (TWP)',
                'category' => 'Travel & Immigration Services',
                'price' => 650.00,
                'service_fee' => 75.00,
                'processing_fee' => 50.00,
                'processing_days' => 12,
                'description' => 'Comptroller General of Immigration (CGI) Cable Approval for expatriates entering Nigeria for short-term specialized work assignment.',
                'status' => 'Active',
                'required_documents' => ['Expatriate Passport Data Page', 'CV / Resume', 'Professional Certificates', 'Nigerian Host Company CAC & FCCPC Clearance'],
                'custom_fields' => [
                    ['name' => 'job_title', 'label' => 'Expatriate Designation / Job Title', 'type' => 'text', 'required' => true],
                    ['name' => 'project_duration', 'label' => 'Project Duration in Days', 'type' => 'number', 'required' => true],
                ],
            ],
            [
                'name' => 'Subject to Regularization (STR) Visa Approval',
                'category' => 'Travel & Immigration Services',
                'price' => 850.00,
                'service_fee' => 100.00,
                'processing_fee' => 50.00,
                'processing_days' => 15,
                'description' => 'STR Visa dossier preparation and approval for expatriates taking up long-term employment under Expatriate Quota in Nigeria.',
                'status' => 'Active',
                'required_documents' => ['Expatriate Credentials', 'Credentials Evaluation', 'Letter of Employment Offer', 'Company Expatriate Quota Approval Slot'],
                'custom_fields' => [
                    ['name' => 'quota_reference', 'label' => 'Expatriate Quota Reference Number', 'type' => 'text', 'required' => true],
                    ['name' => 'quota_position', 'label' => 'Quota Position Title', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'eVisa Application & Approval Clearance',
                'category' => 'Travel & Immigration Services',
                'price' => 220.00,
                'service_fee' => 30.00,
                'processing_fee' => 20.00,
                'processing_days' => 3,
                'description' => 'Fast-track electronic eVisa application and pre-approval letter processing for international travelers.',
                'status' => 'Active',
                'required_documents' => ['Passport Data Page', 'Passport Photo', 'Return Airline Ticket', 'Hotel Booking'],
                'custom_fields' => [
                    ['name' => 'arrival_airport', 'label' => 'Port of Entry (Airport)', 'type' => 'select', 'options' => ['Murtala Muhammed (Lagos)', 'Nnamdi Azikiwe (Abuja)', 'Mallam Aminu Kano (Kano)', 'Port Harcourt (PHC)'], 'required' => true],
                ],
            ],

            // 3. Authentication & Official Documentation
            [
                'name' => "Driver's Licence Authentication & Clearance",
                'category' => 'Authentication & Official Documentation',
                'price' => 120.00,
                'service_fee' => 15.00,
                'processing_fee' => 10.00,
                'processing_days' => 5,
                'description' => "Verification, authentication letter, and FRSC extract for Nigerian Driver's Licences required for exchange abroad.",
                'status' => 'Active',
                'required_documents' => ["Original Driver's Licence (Front & Back)", 'Passport Photograph', 'International Passport Data Page'],
                'custom_fields' => [
                    ['name' => 'licence_number', 'label' => "Driver's Licence Number", 'type' => 'text', 'required' => true],
                    ['name' => 'expiry_date', 'label' => 'Licence Expiry Date', 'type' => 'date', 'required' => true],
                    ['name' => 'destination_country', 'label' => 'Country Requesting Authentication', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'Police Report / Character Clearance Certificate',
                'category' => 'Authentication & Official Documentation',
                'price' => 150.00,
                'service_fee' => 20.00,
                'processing_fee' => 15.00,
                'processing_days' => 5,
                'description' => 'Nigeria Police Force (NPF) Central Criminal Registry Character Clearance Certificate and Fingerprint Authentication.',
                'status' => 'Active',
                'required_documents' => ['International Passport Data Page', 'Passport Photograph (Blue BG)', 'Fingerprint Form'],
                'custom_fields' => [
                    ['name' => 'reason_for_certificate', 'label' => 'Purpose (e.g. Visa, Employment, Immigration)', 'type' => 'text', 'required' => true],
                    ['name' => 'previous_convictions', 'label' => 'Any Previous Convictions?', 'type' => 'select', 'options' => ['No', 'Yes'], 'required' => true],
                ],
            ],
            [
                'name' => 'Newspaper Publication of Change of Name',
                'category' => 'Authentication & Official Documentation',
                'price' => 75.00,
                'service_fee' => 10.00,
                'processing_fee' => 5.00,
                'processing_days' => 2,
                'description' => 'Official national daily newspaper publication for Change of Name, Correcting DOB, or Marriage Name Correction.',
                'status' => 'Active',
                'required_documents' => ['Sworn High Court Affidavit', 'Valid ID Card'],
                'custom_fields' => [
                    ['name' => 'former_name', 'label' => 'Former / Previous Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'new_name', 'label' => 'New / Correct Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'reason_for_change', 'label' => 'Reason for Change (e.g. Marriage, Correction)', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'Court Affidavit of Name / Age / Status',
                'category' => 'Authentication & Official Documentation',
                'price' => 60.00,
                'service_fee' => 10.00,
                'processing_fee' => 5.00,
                'processing_days' => 2,
                'description' => 'Sworn Judicial High Court Affidavit for Declaration of Age, Change of Name, Next of Kin, or Single Status.',
                'status' => 'Active',
                'required_documents' => ['Passport Photo', 'Existing ID Card / Birth Record'],
                'custom_fields' => [
                    ['name' => 'affidavit_type', 'label' => 'Type of Affidavit', 'type' => 'select', 'options' => ['Declaration of Age', 'Change of Name', 'Loss of Document', 'Next of Kin / Beneficiary', 'Single Status'], 'required' => true],
                    ['name' => 'deponent_name', 'label' => 'Deponent Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'statement_details', 'label' => 'Facts to Declare', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'LGA Certificate of Indigene / Indigenization',
                'category' => 'Authentication & Official Documentation',
                'price' => 80.00,
                'service_fee' => 10.00,
                'processing_fee' => 5.00,
                'processing_days' => 3,
                'description' => 'Official Local Government Area (LGA) Certificate of Origin / Indigene letter from state of origin.',
                'status' => 'Active',
                'required_documents' => ['Passport Photograph', 'Parental Identification / Village Letter', 'Birth Certificate'],
                'custom_fields' => [
                    ['name' => 'state_of_origin', 'label' => 'State of Origin', 'type' => 'text', 'required' => true],
                    ['name' => 'lga_name', 'label' => 'Local Government Area (LGA)', 'type' => 'text', 'required' => true],
                    ['name' => 'town_village', 'label' => 'Town / Autonomous Community', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'NPC Birth Certificate / Attestation of Birth',
                'category' => 'Authentication & Official Documentation',
                'price' => 90.00,
                'service_fee' => 10.00,
                'processing_fee' => 5.00,
                'processing_days' => 4,
                'description' => 'National Population Commission (NPC) Official Birth Certificate or Attestation of Birth for adults born before 1992.',
                'status' => 'Active',
                'required_documents' => ['Sworn Age Declaration Affidavit', 'Passport Photograph', 'Hospital Birth Record (if any)'],
                'custom_fields' => [
                    ['name' => 'applicant_dob', 'label' => 'Date of Birth', 'type' => 'date', 'required' => true],
                    ['name' => 'father_name', 'label' => "Father's Full Name", 'type' => 'text', 'required' => true],
                    ['name' => 'mother_maiden_name', 'label' => "Mother's Maiden Name", 'type' => 'text', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place / Hospital of Birth', 'type' => 'text', 'required' => true],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['name' => $serviceData['name']],
                $serviceData
            );
        }
    }
}
