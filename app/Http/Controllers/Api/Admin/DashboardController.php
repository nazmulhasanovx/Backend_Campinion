<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'data' => [
                'totals' => [
                    'projects' => Project::count(),
                    'services' => Service::count(),
                    'blog_posts' => BlogPost::count(),
                    'contact_messages' => ContactMessage::count(),
                    'quote_requests' => QuoteRequest::count(),
                ],
                'recent_inquiries' => ContactMessage::latest()->take(5)->get(),
                'recent_quote_requests' => QuoteRequest::latest()->take(5)->get(),
                'project_progress' => Project::select(['title', 'slug', 'progress', 'status'])->latest()->take(6)->get(),
            ],
        ]);
    }
}
