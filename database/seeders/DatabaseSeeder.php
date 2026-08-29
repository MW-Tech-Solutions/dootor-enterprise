<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Umar',
                'last_name' => 'Maher',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'Approved',
            ]
        );

        SystemSetting::firstOrCreate([], [
            'platform_name' => 'DOOTOR ENTERPRISES',
            'company_name' => 'DOOTOR ENTERPRISES',
            'brand_template' => 'classic',
            'brand_theme' => 'light',
            'brand_color_palette' => 'default',
            'brand_primary_color' => '#004225',
            'brand_secondary_color' => '#d4af37',
            'brand_gradient_from' => '#004225',
            'brand_gradient_to' => '#d4af37',
            'default_currency' => 'USD',
            'payment_gateway' => 'paystack',
            'payments_enabled' => true,
            'payment_mode' => 'test',
        ]);

        $services = [
            ['name' => 'NIN', 'price' => 0, 'description' => 'Secure your National Identification Number (NIN) registration, modification, or verification.'],
            ['name' => 'BVN', 'price' => 0, 'description' => 'Link and update your Bank Verification Number (BVN) across your financial accounts.'],
            ['name' => 'Passport', 'price' => 0, 'description' => 'Fast-track processing for new international passport applications or renewals.'],
            ['name' => 'Emergency Travel Certificate (ETC)', 'price' => 0, 'description' => 'Expedite emergency travel certificate issuance for urgent international travel.'],
            ['name' => 'Driver\'s Licence Authentication', 'price' => 0, 'description' => 'Verify and authenticate driver\'s license letters and certification reports.'],
            ['name' => 'Visa: SEV, MEV, TWP, STR & eVisa', 'price' => 0, 'description' => 'Smooth processing support for Single Entry, Multi Entry, Temporary Work Permits, and eVisas.'],
            ['name' => 'Police Report', 'price' => 0, 'description' => 'Apply for official character clearance, loss of documents, or police reports.'],
            ['name' => 'Newspaper Publication', 'price' => 0, 'description' => 'Publish official changes of name, lost items, or announcements in national news.'],
            ['name' => 'Court Affidavit of Name and Age', 'price' => 0, 'description' => 'Draft and process legally binding sworn court affidavits for name, age, or declarations.'],
            ['name' => 'LGA Certificate of Indigenization', 'price' => 0, 'description' => 'Obtain official state/local government origin and indigene certificates.'],
            ['name' => 'Birth Certificate/Attestation by the National Population Commission (NPC)', 'price' => 0, 'description' => 'Acquire official birth registration records or attestation documents from the NPC.'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], [
                ...$service,
                'status' => 'Active',
            ]);
        }
    }
}
