<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\OfficeLocation;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminContentController extends Controller
{
    public function projects()
    {
        return ProjectResource::collection(
            Project::with(['category', 'images', 'timelines'])->latest()->get()
        );
    }

    public function project(Project $project)
    {
        return new ProjectResource($project->load(['category', 'images', 'timelines']));
    }

    public function storeProject(Request $request)
    {
        $data = $this->validatedProject($request);
        $project = Project::create($this->projectPayload($data));

        $this->syncProjectRelations($project, $data);

        return (new ProjectResource($project->load(['category', 'images', 'timelines'])))
            ->response()
            ->setStatusCode(201);
    }

    public function updateProject(Request $request, Project $project)
    {
        $data = $this->validatedProject($request, $project);
        $project->update($this->projectPayload($data));

        $this->syncProjectRelations($project, $data);

        return new ProjectResource($project->load(['category', 'images', 'timelines']));
    }

    public function destroyProject(Project $project)
    {
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully.']);
    }

    public function services()
    {
        return ServiceResource::collection(Service::orderBy('sort_order')->get());
    }

    public function service(Service $service)
    {
        return new ServiceResource($service);
    }

    public function storeService(Request $request)
    {
        $service = Service::create($this->validatedService($request));

        return (new ServiceResource($service))->response()->setStatusCode(201);
    }

    public function updateService(Request $request, Service $service)
    {
        $service->update($this->validatedService($request, $service));

        return new ServiceResource($service);
    }

    public function destroyService(Service $service)
    {
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully.']);
    }

    public function blog()
    {
        return BlogPostResource::collection(BlogPost::with('category')->latest('published_at')->get());
    }

    public function blogPost(BlogPost $post)
    {
        return new BlogPostResource($post->load('category'));
    }

    public function storeBlogPost(Request $request)
    {
        $post = BlogPost::create($this->blogPayload($this->validatedBlogPost($request)));

        return (new BlogPostResource($post->load('category')))->response()->setStatusCode(201);
    }

    public function updateBlogPost(Request $request, BlogPost $post)
    {
        $post->update($this->blogPayload($this->validatedBlogPost($request, $post)));

        return new BlogPostResource($post->load('category'));
    }

    public function destroyBlogPost(BlogPost $post)
    {
        $post->delete();

        return response()->json(['message' => 'Blog post deleted successfully.']);
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

    public function settings()
    {
        return response()->json([
            'data' => [
                'settings' => \App\Models\Setting::pluck('value', 'key'),
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.company' => ['nullable', 'array'],
            'settings.company.name' => ['nullable', 'string', 'max:255'],
            'settings.company.tagline' => ['nullable', 'string', 'max:500'],
            'settings.company.email' => ['nullable', 'email', 'max:255'],
            'settings.company.phone' => ['nullable', 'string', 'max:255'],
            'settings.company.address' => ['nullable', 'string', 'max:500'],
            'settings.home_hero' => ['nullable', 'array'],
            'settings.home_hero.title' => ['nullable', 'string', 'max:255'],
            'settings.home_hero.subtitle' => ['nullable', 'string', 'max:1000'],
            'settings.home_hero.image' => ['nullable', 'string', 'max:2048'],
            'settings.home_hero.tabs' => ['nullable', 'array'],
            'settings.home_hero.tabs.*' => ['string', 'max:100'],
            'settings.seo_defaults' => ['nullable', 'array'],
            'settings.seo_defaults.title' => ['nullable', 'string', 'max:255'],
            'settings.seo_defaults.description' => ['nullable', 'string', 'max:1000'],
            'settings.map' => ['nullable', 'array'],
            'settings.map.provider' => ['nullable', 'string', 'max:255'],
            'settings.map.default_location' => ['nullable', 'string', 'max:255'],
            'settings.map.api_key' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json([
            'message' => 'Settings saved successfully.',
            'data' => [
                'settings' => \App\Models\Setting::pluck('value', 'key'),
            ],
        ]);
    }

    public function storeMedia(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'image', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');
        $title = ($data['title'] ?? null) ?: Str::headline(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        $media = Media::create([
            'title' => $title,
            'file_path' => $this->publicStorageUrl($request, $path),
            'alt_text' => $data['alt_text'] ?? $title,
            'category' => $data['category'] ?? 'Media',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json(['data' => $media], 201);
    }

    public function updateMedia(Request $request, Media $media)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $media->update($data);

        return response()->json(['data' => $media]);
    }

    public function destroyMedia(Media $media)
    {
        $path = parse_url($media->file_path, PHP_URL_PATH);

        if (is_string($path) && Str::startsWith($path, '/storage/')) {
            Storage::disk('public')->delete(Str::after($path, '/storage/'));
        }

        $media->delete();

        return response()->json(['message' => 'Media deleted successfully.']);
    }

    public function testimonials()
    {
        return response()->json(['data' => Testimonial::orderBy('sort_order')->latest()->get()]);
    }

    public function storeTestimonial(Request $request)
    {
        $testimonial = Testimonial::create($this->validatedTestimonial($request));

        return response()->json(['data' => $testimonial], 201);
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validatedTestimonial($request));

        return response()->json(['data' => $testimonial]);
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        $testimonial->delete();

        return response()->json(['message' => 'Testimonial deleted successfully.']);
    }

    public function partners()
    {
        return response()->json(['data' => Partner::orderBy('sort_order')->latest()->get()]);
    }

    public function storePartner(Request $request)
    {
        $partner = Partner::create($this->validatedPartner($request));

        return response()->json(['data' => $partner], 201);
    }

    public function updatePartner(Request $request, Partner $partner)
    {
        $partner->update($this->validatedPartner($request));

        return response()->json(['data' => $partner]);
    }

    public function destroyPartner(Partner $partner)
    {
        $partner->delete();

        return response()->json(['message' => 'Partner deleted successfully.']);
    }

    public function faqs()
    {
        return response()->json(['data' => Faq::orderBy('sort_order')->latest()->get()]);
    }

    public function storeFaq(Request $request)
    {
        $faq = Faq::create($this->validatedFaq($request));

        return response()->json(['data' => $faq], 201);
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $faq->update($this->validatedFaq($request));

        return response()->json(['data' => $faq]);
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();

        return response()->json(['message' => 'FAQ deleted successfully.']);
    }

    public function offices()
    {
        return response()->json(['data' => OfficeLocation::orderBy('sort_order')->latest()->get()]);
    }

    public function storeOffice(Request $request)
    {
        $office = OfficeLocation::create($this->validatedOffice($request));

        return response()->json(['data' => $office], 201);
    }

    public function updateOffice(Request $request, OfficeLocation $office)
    {
        $office->update($this->validatedOffice($request));

        return response()->json(['data' => $office]);
    }

    public function destroyOffice(OfficeLocation $office)
    {
        $office->delete();

        return response()->json(['message' => 'Office deleted successfully.']);
    }

    public function team()
    {
        return response()->json(['data' => TeamMember::orderBy('sort_order')->latest()->get()]);
    }

    public function storeTeamMember(Request $request)
    {
        $member = TeamMember::create($this->validatedTeamMember($request));

        return response()->json(['data' => $member], 201);
    }

    public function updateTeamMember(Request $request, TeamMember $member)
    {
        $member->update($this->validatedTeamMember($request));

        return response()->json(['data' => $member]);
    }

    public function destroyTeamMember(TeamMember $member)
    {
        $member->delete();

        return response()->json(['message' => 'Team member deleted successfully.']);
    }

    public function gallery()
    {
        return response()->json(['data' => GalleryItem::with('project:id,title,slug')->orderBy('sort_order')->latest()->get()]);
    }

    public function storeGalleryItem(Request $request)
    {
        $item = GalleryItem::create($this->validatedGalleryItem($request));

        return response()->json(['data' => $item->load('project:id,title,slug')], 201);
    }

    public function updateGalleryItem(Request $request, GalleryItem $item)
    {
        $item->update($this->validatedGalleryItem($request));

        return response()->json(['data' => $item->load('project:id,title,slug')]);
    }

    public function destroyGalleryItem(GalleryItem $item)
    {
        $item->delete();

        return response()->json(['message' => 'Gallery item deleted successfully.']);
    }

    private function validatedProject(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($project)],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Ongoing', 'Completed', 'Upcoming'])],
            'location' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'beds' => ['nullable', 'integer', 'min:0', 'max:255'],
            'baths' => ['nullable', 'integer', 'min:0', 'max:255'],
            'parking' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'area' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'summary' => ['required', 'string'],
            'description' => ['required', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string', 'max:2048'],
            'timeline' => ['nullable', 'array'],
            'timeline.*.title' => ['required_with:timeline', 'string', 'max:255'],
            'timeline.*.date' => ['required_with:timeline', 'string', 'max:255'],
            'timeline.*.description' => ['required_with:timeline', 'string'],
            'is_featured' => ['boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);
    }

    private function validatedTestimonial(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'quote' => ['required', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['is_active' => true, 'sort_order' => 0];
    }

    private function validatedPartner(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['is_active' => true, 'sort_order' => 0];
    }

    private function validatedFaq(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['is_active' => true, 'sort_order' => 0];
    }

    private function validatedOffice(Request $request): array
    {
        return $request->validate([
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'map_embed_url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['sort_order' => 0];
    }

    private function validatedTeamMember(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:2048'],
            'bio' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['social_links'] = $data['social_links'] ?? [];
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function validatedGalleryItem(Request $request): array
    {
        return $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'image' => ['required', 'string', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['sort_order' => 0];
    }

    private function projectPayload(array $data): array
    {
        $category = $this->projectCategory($data['category'] ?? null);

        return [
            'project_category_id' => $category?->id,
            'title' => $data['title'],
            'slug' => $data['slug'] ?: Str::slug($data['title']),
            'price' => $data['price'] ?? null,
            'location' => $data['location'],
            'status' => $data['status'],
            'client' => $data['client'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'progress' => $data['progress'],
            'beds' => $data['beds'] ?? null,
            'baths' => $data['baths'] ?? null,
            'parking' => $data['parking'] ?? null,
            'area' => $data['area'] ?? null,
            'featured_image' => $data['featured_image'] ?? null,
            'summary' => $data['summary'],
            'description' => $data['description'],
            'features' => $data['features'] ?? [],
            'is_featured' => $data['is_featured'] ?? false,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ];
    }

    private function syncProjectRelations(Project $project, array $data): void
    {
        $project->images()->delete();

        foreach (($data['gallery'] ?? []) as $index => $image) {
            $project->images()->create([
                'image' => $image,
                'alt_text' => $project->title,
                'sort_order' => $index,
            ]);
        }

        $project->timelines()->delete();

        foreach (($data['timeline'] ?? []) as $index => $item) {
            $project->timelines()->create([
                'title' => $item['title'],
                'date_label' => $item['date'],
                'description' => $item['description'],
                'sort_order' => $index,
            ]);
        }
    }

    private function validatedService(Request $request, ?Service $service = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($service)],
            'summary' => ['required', 'string'],
            'description' => ['required', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:2048'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['string', 'max:255'],
            'process' => ['nullable', 'array'],
            'process.*' => ['string', 'max:255'],
            'related_project_slugs' => ['nullable', 'array'],
            'related_project_slugs.*' => ['string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['benefits'] = $data['benefits'] ?? [];
        $data['process'] = $data['process'] ?? [];
        $data['related_project_slugs'] = $data['related_project_slugs'] ?? [];
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function validatedBlogPost(Request $request, ?BlogPost $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($post)],
            'category' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'read_time' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'array', 'min:1'],
            'content.*' => ['string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'status' => ['required', Rule::in(['Published', 'Draft'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);
    }

    private function blogPayload(array $data): array
    {
        $category = $this->blogCategory($data['category'] ?? null);

        return [
            'blog_category_id' => $category?->id,
            'title' => $data['title'],
            'slug' => $data['slug'] ?: Str::slug($data['title']),
            'author' => $data['author'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'read_time' => $data['read_time'] ?? null,
            'featured_image' => $data['featured_image'] ?? null,
            'excerpt' => $data['excerpt'],
            'content' => $data['content'],
            'tags' => $data['tags'] ?? [],
            'status' => $data['status'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ];
    }

    private function projectCategory(?string $name): ?ProjectCategory
    {
        if (!$name) {
            return null;
        }

        return ProjectCategory::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        );
    }

    private function blogCategory(?string $name): ?BlogCategory
    {
        if (!$name) {
            return null;
        }

        return BlogCategory::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        );
    }

    private function publicStorageUrl(Request $request, string $path): string
    {
        return rtrim($request->getSchemeAndHttpHost(), '/').'/storage/'.ltrim($path, '/');
    }
}
