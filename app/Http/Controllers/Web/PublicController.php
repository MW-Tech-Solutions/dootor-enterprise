<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactEnquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicController extends Controller
{
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
            ));

            return redirect()->to('/#contact')
                ->with('contact_success', 'Your message has been sent! We will get back to you shortly.');
        } catch (\Throwable $e) {
            return redirect()->to('/#contact')
                ->withInput()
                ->with('contact_error', 'Sorry, we could not send your message right now. Please try again later or contact us via WhatsApp.');
        }
    }
}

