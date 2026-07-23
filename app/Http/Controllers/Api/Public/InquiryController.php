<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function contact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $message = ContactMessage::create($data);

        return response()->json([
            'message' => 'Contact message submitted successfully.',
            'data' => $message,
        ], 201);
    }

    public function quote(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
        ]);

        $quote = QuoteRequest::create($data);

        return response()->json([
            'message' => 'Quote request submitted successfully.',
            'data' => $quote,
        ], 201);
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
        ]);

        $subscriber = NewsletterSubscriber::create([
            'email' => $data['email'],
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Newsletter subscription successful.',
            'data' => $subscriber,
        ], 201);
    }
}
