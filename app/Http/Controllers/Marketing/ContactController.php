<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'cf-turnstile-response' => ['required', 'string'],
        ], [
            'cf-turnstile-response.required' => __('marketing.contact.turnstile_required'),
        ]);

        // Verify Turnstile token
        if (!$this->verifyTurnstile($validated['cf-turnstile-response'], $request->ip())) {
            return back()
                ->withInput()
                ->withErrors(['turnstile' => __('marketing.contact.turnstile_failed')]);
        }

        // Send email
        Mail::to(config('services.contact.email'))
            ->send(new ContactFormMail(
                name: $validated['name'],
                email: $validated['email'],
                subject: $validated['subject'],
                body: $validated['message'],
            ));

        return back()->with('success', __('marketing.contact.success'));
    }

    private function verifyTurnstile(string $token, ?string $ip): bool
    {
        $secretKey = config('services.turnstile.secret_key');

        if (empty($secretKey)) {
            // If no secret key configured, skip verification (for local dev)
            return true;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        return $response->successful() && $response->json('success') === true;
    }
}
