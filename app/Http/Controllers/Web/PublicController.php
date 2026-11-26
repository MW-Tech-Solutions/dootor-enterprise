<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactEnquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicController extends Controller
{
    public function welcome()
    {
        $settings = \App\Models\SystemSetting::firstOrCreate([]);
        $faqs = \App\Models\Faq::where('status', 'Active')->orderBy('sort_order', 'asc')->get();
        $primaryServices = \App\Models\Service::where('status', 'Active')
            ->whereNull('parent_id')
            ->with(['subServices' => function($q) {
                $q->where('status', 'Active')
                  ->orderByRaw('CASE WHEN priority IS NULL OR priority = 0 THEN 999999 ELSE priority END ASC')
                  ->orderBy('name', 'asc');
            }])
            ->orderByRaw('CASE WHEN priority IS NULL OR priority = 0 THEN 999999 ELSE priority END ASC')
            ->orderBy('name', 'asc')
            ->get();

        $reviews = \App\Models\Review::where('is_approved', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        return view('welcome', compact('settings', 'faqs', 'primaryServices', 'reviews'));
    }

    public function trackAnalytics(Request $request)
    {
        $validated = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'category'   => ['nullable', 'string', 'max:50'],
            'label'      => ['nullable', 'string', 'max:150'],
            'url'        => ['nullable', 'string', 'max:500'],
        ]);

        try {
            \App\Models\AnalyticsEvent::create([
                'event_type' => $validated['event_type'],
                'category'   => $validated['category'] ?? null,
                'label'      => $validated['label'] ?? null,
                'url'        => $validated['url'] ?? $request->header('referer'),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                'user_id'    => auth()->id(),
            ]);

            return response()->json(['status' => 'tracked']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'ignored'], 200);
        }
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => ['required', 'string', 'max:100'],
            'client_title'  => ['nullable', 'string', 'max:120'],
            'service_name'  => ['nullable', 'string', 'max:150'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['required', 'string', 'min:10', 'max:1200'],
        ]);

        \App\Models\Review::create([
            'user_id'       => auth()->id(),
            'client_name'   => $validated['client_name'],
            'client_title'  => $validated['client_title'] ?? 'Verified Client',
            'service_name'  => $validated['service_name'] ?? 'Administrative & Consular Service',
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'],
            'is_approved'   => true, // auto-approve so client immediately sees their submitted review
            'is_featured'   => true,
        ]);

        return redirect()->to('/#reviews')->with('review_success', 'Thank you! Your verified client review has been recorded.');
    }

    public function vendorStorefront(User $vendor)
    {
        abort_unless($vendor->role === 'vendor' && $vendor->status === 'Approved', 404);

        $services = $vendor->vendorServices()
            ->where('status', 'Active')
            ->latest()
            ->get();

        $storefront = $vendor->storefrontSetting()->firstOrCreate([], [
            'storefront_name' => $vendor->first_name . ' Hub',
            'theme' => 'light',
            'color_palette' => 'default',
        ]);

        return view('storefront', [
            'vendor' => $vendor,
            'services' => $services,
            'storefront' => $storefront,
        ]);
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'subject'    => ['required', 'string', 'max:200'],
            'message'    => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        // Find the super admin email — use first admin user's email as recipient
        $adminEmail = User::where('role', 'admin')->orderBy('id')->value('email')
                      ?? config('mail.from.address');

        try {
            Mail::to($adminEmail)->send(new ContactEnquiry(
                $validated['first_name'],
                $validated['last_name'],
                $validated['email'],
                $validated['subject'],
                $validated['message'],
                $validated['phone'] ?? null
            ));

            return redirect()->to('/#contact')
                ->with('contact_success', 'Your message has been sent! We will get back to you shortly.');
        } catch (\Throwable $e) {
            return redirect()->to('/#contact')
                ->withInput()
                ->with('contact_error', 'Sorry, we could not send your message right now. Please try again later or contact us via WhatsApp.');
        }
    }

    public function termsAndConditions()
    {
        $settings = \App\Models\SystemSetting::firstOrCreate([]);
        return view('policies.terms', compact('settings'));
    }

    public function refundPolicy()
    {
        $settings = \App\Models\SystemSetting::firstOrCreate([]);
        return view('policies.refund', compact('settings'));
    }

    public function privacyPolicy()
    {
        $settings = \App\Models\SystemSetting::firstOrCreate([]);
        return view('policies.privacy', compact('settings'));
    }

    /**
     * Generate dynamic XML sitemap for Google and search crawlers.
     */
    public function sitemap()
    {
        $settings = \App\Models\SystemSetting::first();
        $latestService = \App\Models\Service::latest('updated_at')->first();
        $latestUpdate = max(
            $settings?->updated_at?->toAtomString() ?? now()->toAtomString(),
            $latestService?->updated_at?->toAtomString() ?? now()->toAtomString()
        );

        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => $latestUpdate,
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('terms.conditions'),
                'lastmod' => $settings?->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('privacy.policy'),
                'lastmod' => $settings?->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('refund.policy'),
                'lastmod' => $settings?->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('register.client'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('register'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('login'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ],
        ];

        // Include verified approved vendors with active storefronts
        $vendors = User::where('role', 'vendor')
            ->where('status', 'Approved')
            ->has('vendorServices')
            ->get();

        foreach ($vendors as $vendor) {
            $urls[] = [
                'loc' => route('vendor.storefront', $vendor),
                'lastmod' => $vendor->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($urls as $entry) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "        <lastmod>" . $entry['lastmod'] . "</lastmod>\n";
            $xml .= "        <changefreq>" . $entry['changefreq'] . "</changefreq>\n";
            $xml .= "        <priority>" . $entry['priority'] . "</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex, follow',
        ]);
    }
}

