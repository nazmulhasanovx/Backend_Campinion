<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\OfficeLocation;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;

class PublicContentController extends Controller
{
    public function home()
    {
        return response()->json([
            'data' => [
                'featured_projects' => ProjectResource::collection(
                    Project::with(['category', 'images', 'timelines'])->where('is_featured', true)->latest()->take(6)->get()
                ),
                'services' => ServiceResource::collection(Service::where('is_active', true)->orderBy('sort_order')->take(8)->get()),
                'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
                'latest_news' => BlogPostResource::collection(
                    BlogPost::with('category')->where('status', 'Published')->latest('published_at')->take(3)->get()
                ),
                'settings' => Setting::pluck('value', 'key'),
            ],
        ]);
    }

    public function about()
    {
        return response()->json([
            'data' => [
                'settings' => Setting::pluck('value', 'key'),
            ],
        ]);
    }

    public function services()
    {
        return ServiceResource::collection(Service::where('is_active', true)->orderBy('sort_order')->get());
    }

    public function service(string $slug)
    {
        return new ServiceResource(Service::where('slug', $slug)->where('is_active', true)->firstOrFail());
    }

    public function projects()
    {
        return ProjectResource::collection(Project::with(['category', 'images', 'timelines'])->latest()->get());
    }

    public function project(string $slug)
    {
        return new ProjectResource(Project::with(['category', 'images', 'timelines'])->where('slug', $slug)->firstOrFail());
    }

    public function blog()
    {
        return BlogPostResource::collection(BlogPost::with('category')->where('status', 'Published')->latest('published_at')->get());
    }

    public function blogPost(string $slug)
    {
        return new BlogPostResource(BlogPost::with('category')->where('slug', $slug)->where('status', 'Published')->firstOrFail());
    }

    public function faqs()
    {
        return response()->json(['data' => Faq::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function partners()
    {
        return response()->json(['data' => Partner::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function team()
    {
        return response()->json(['data' => TeamMember::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function gallery()
    {
        return response()->json(['data' => GalleryItem::with('project:id,title,slug')->orderBy('sort_order')->get()]);
    }

    public function settings()
    {
        return response()->json([
            'data' => [
                'settings' => Setting::pluck('value', 'key'),
                'office_locations' => OfficeLocation::orderBy('sort_order')->get(),
            ],
        ]);
    }
}
