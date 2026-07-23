<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\OfficeLocation;
use App\Models\Partner;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Services\Admin\AdminContentService;
use Illuminate\Http\Request;

class AdminContentController extends Controller
{
    public function __construct(private readonly AdminContentService $content)
    {
    }

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
        $data = $this->content->validatedProject($request);
        $project = Project::create($this->content->projectPayload($data));

        $this->content->syncProjectRelations($project, $data);

        return (new ProjectResource($project->load(['category', 'images', 'timelines'])))
            ->response()
            ->setStatusCode(201);
    }

    public function updateProject(Request $request, Project $project)
    {
        $data = $this->content->validatedProject($request, $project);
        $project->update($this->content->projectPayload($data));

        $this->content->syncProjectRelations($project, $data);

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
        $service = Service::create($this->content->validatedService($request));

        return (new ServiceResource($service))->response()->setStatusCode(201);
    }

    public function updateService(Request $request, Service $service)
    {
        $service->update($this->content->validatedService($request, $service));

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
        $post = BlogPost::create($this->content->blogPayload($this->content->validatedBlogPost($request)));

        return (new BlogPostResource($post->load('category')))->response()->setStatusCode(201);
    }

    public function updateBlogPost(Request $request, BlogPost $post)
    {
        $post->update($this->content->blogPayload($this->content->validatedBlogPost($request, $post)));

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

    public function storeMedia(Request $request)
    {
        return response()->json(['data' => $this->content->createMedia($request)], 201);
    }

    public function updateMedia(Request $request, Media $media)
    {
        $media->update($this->content->validatedMedia($request));

        return response()->json(['data' => $media]);
    }

    public function destroyMedia(Media $media)
    {
        $this->content->deleteMediaFile($media);
        $media->delete();

        return response()->json(['message' => 'Media deleted successfully.']);
    }

    public function settings()
    {
        return response()->json([
            'data' => [
                'settings' => Setting::pluck('value', 'key'),
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $this->content->validatedSettings($request);
        $this->content->saveSettings($data['settings']);

        return response()->json([
            'message' => 'Settings saved successfully.',
            'data' => [
                'settings' => Setting::pluck('value', 'key'),
            ],
        ]);
    }

    public function testimonials()
    {
        return response()->json(['data' => Testimonial::orderBy('sort_order')->latest()->get()]);
    }

    public function storeTestimonial(Request $request)
    {
        $testimonial = Testimonial::create($this->content->validatedTestimonial($request));

        return response()->json(['data' => $testimonial], 201);
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->content->validatedTestimonial($request));

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
        $partner = Partner::create($this->content->validatedPartner($request));

        return response()->json(['data' => $partner], 201);
    }

    public function updatePartner(Request $request, Partner $partner)
    {
        $partner->update($this->content->validatedPartner($request));

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
        $faq = Faq::create($this->content->validatedFaq($request));

        return response()->json(['data' => $faq], 201);
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $faq->update($this->content->validatedFaq($request));

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
        $office = OfficeLocation::create($this->content->validatedOffice($request));

        return response()->json(['data' => $office], 201);
    }

    public function updateOffice(Request $request, OfficeLocation $office)
    {
        $office->update($this->content->validatedOffice($request));

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
        $member = TeamMember::create($this->content->validatedTeamMember($request));

        return response()->json(['data' => $member], 201);
    }

    public function updateTeamMember(Request $request, TeamMember $member)
    {
        $member->update($this->content->validatedTeamMember($request));

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
        $item = GalleryItem::create($this->content->validatedGalleryItem($request));

        return response()->json(['data' => $item->load('project:id,title,slug')], 201);
    }

    public function updateGalleryItem(Request $request, GalleryItem $item)
    {
        $item->update($this->content->validatedGalleryItem($request));

        return response()->json(['data' => $item->load('project:id,title,slug')]);
    }

    public function destroyGalleryItem(GalleryItem $item)
    {
        $item->delete();

        return response()->json(['message' => 'Gallery item deleted successfully.']);
    }
}
