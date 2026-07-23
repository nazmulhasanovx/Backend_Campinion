<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;

class AdminContentController extends Controller
{
    public function projects()
    {
        return ProjectResource::collection(
            Project::with(['category', 'images', 'timelines'])->latest()->get()
        );
    }

    public function services()
    {
        return ServiceResource::collection(Service::orderBy('sort_order')->get());
    }

    public function blog()
    {
        return BlogPostResource::collection(BlogPost::with('category')->latest('published_at')->get());
    }

    public function inquiries()
    {
        $contactMessages = ContactMessage::latest()->get()->map(fn ($message) => [
            'id' => 'contact-'.$message->id,
            'name' => $message->name,
            'email' => $message->email,
            'phone' => $message->phone,
            'type' => 'Contact Message',
            'subject' => $message->subject,
            'message' => $message->message,
            'status' => $message->status,
            'date' => $message->created_at?->toDateString(),
        ]);

        $quoteRequests = QuoteRequest::latest()->get()->map(fn ($quote) => [
            'id' => 'quote-'.$quote->id,
            'name' => $quote->name,
            'email' => $quote->email,
            'phone' => $quote->phone,
            'type' => 'Quote Request',
            'subject' => $quote->project_type ?: 'Quote request',
            'message' => $quote->message,
            'status' => $quote->status,
            'date' => $quote->created_at?->toDateString(),
        ]);

        return response()->json([
            'data' => $contactMessages
                ->concat($quoteRequests)
                ->sortByDesc('date')
                ->values(),
        ]);
    }

    public function media()
    {
        return response()->json(['data' => Media::latest()->get()]);
    }
}
