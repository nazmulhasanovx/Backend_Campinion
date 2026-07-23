<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\OfficeLocation;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdminUser();
        $services = $this->seedServices();
        $projects = $this->seedProjects();
        $blogPosts = $this->seedBlogPosts();
        $this->seedHomeAndAboutSettings($services, $projects, $blogPosts);
        $this->seedTestimonials();
        $this->seedTeamMembers();
        $this->seedFaqs();
        $this->seedOffices();
        $this->seedPartners();
        $this->seedMedia();
        $this->seedInquiries();
    }

    private function seedAdminUser(): void
    {
        User::updateOrCreate([
            'email' => 'admin@campanion.com',
        ], [
            'name' => 'Campanion Admin',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);
    }

    private function seedServices(): array
    {
        $services = [
            [
                'title' => 'Building Construction',
                'slug' => 'building-construction',
                'summary' => 'End-to-end residential, commercial, and mixed-use construction delivery.',
                'description' => 'We manage construction with clear scheduling, cost visibility, quality control, and site supervision from foundation to handover.',
                'icon' => 'Building',
                'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Structured project planning', 'Reliable site supervision', 'Quality material control'],
                'process' => ['Site assessment', 'Budget and schedule planning', 'Construction execution', 'Final handover'],
                'related_project_slugs' => ['modern-townhouse', 'commercial-space-banani'],
            ],
            [
                'title' => 'Civil Engineering',
                'slug' => 'civil-engineering',
                'summary' => 'Technical planning and execution for durable infrastructure and building systems.',
                'description' => 'Our civil engineering team supports structural planning, infrastructure coordination, drainage, access, and long-term site performance.',
                'icon' => 'HardHat',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Technical feasibility', 'Durable planning', 'Safety-focused coordination'],
                'process' => ['Survey', 'Engineering plan', 'Authority coordination', 'Execution review'],
                'related_project_slugs' => ['industrial-logistics-hub'],
            ],
            [
                'title' => 'Architecture',
                'slug' => 'architecture',
                'summary' => 'Functional, modern architectural planning for homes, offices, and developments.',
                'description' => 'We design spaces that balance visual identity, natural light, user comfort, regulation, and construction practicality.',
                'icon' => 'DraftingCompass',
                'image' => 'https://images.unsplash.com/photo-1503387837-b154d5074bd2?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['User-centered layouts', 'Modern exterior language', 'Construction-ready drawings'],
                'process' => ['Concept design', 'Design development', 'Technical drawings', 'Site coordination'],
                'related_project_slugs' => ['modern-townhouse', 'penthouse-city-view'],
            ],
            [
                'title' => 'Interior Design',
                'slug' => 'interior-design',
                'summary' => 'Refined interiors for residential, office, hospitality, and commercial spaces.',
                'description' => 'Our interior process covers space planning, material selection, lighting, furniture direction, and execution supervision.',
                'icon' => 'Paintbrush',
                'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Material guidance', 'Furniture planning', 'Lighting and finish coordination'],
                'process' => ['Mood direction', 'Layout planning', 'Material selection', 'Installation supervision'],
                'related_project_slugs' => ['penthouse-city-view', 'commercial-space-banani'],
            ],
            [
                'title' => 'Renovation',
                'slug' => 'renovation',
                'summary' => 'Upgrade existing homes, offices, and commercial spaces with controlled execution.',
                'description' => 'We help improve existing properties through structural checks, layout improvements, finishes, services, and phased site work.',
                'icon' => 'Hammer',
                'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Better space utilization', 'Controlled disruption', 'Modern finishing'],
                'process' => ['Condition audit', 'Renovation scope', 'Execution schedule', 'Final correction'],
                'related_project_slugs' => ['commercial-space-banani'],
            ],
            [
                'title' => 'Project Management',
                'slug' => 'project-management',
                'summary' => 'Planning, tracking, vendor coordination, and quality control for complex projects.',
                'description' => 'We coordinate schedules, budgets, vendors, site progress, documentation, and reporting for smoother project delivery.',
                'icon' => 'Ruler',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Transparent tracking', 'Vendor coordination', 'Risk reduction'],
                'process' => ['Project roadmap', 'Resource planning', 'Progress tracking', 'Handover reporting'],
                'related_project_slugs' => ['industrial-logistics-hub', 'modern-townhouse'],
            ],
            [
                'title' => 'Consultancy',
                'slug' => 'consultancy',
                'summary' => 'Practical real estate and construction guidance before major investment decisions.',
                'description' => 'We support feasibility decisions with site review, market context, cost planning, and development strategy.',
                'icon' => 'ShieldCheck',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Feasibility review', 'Market guidance', 'Cost awareness'],
                'process' => ['Requirement review', 'Research', 'Advisory report', 'Decision support'],
                'related_project_slugs' => ['penthouse-city-view'],
            ],
            [
                'title' => 'Maintenance',
                'slug' => 'maintenance',
                'summary' => 'Preventive and corrective maintenance for properties after delivery.',
                'description' => 'Our maintenance support keeps buildings reliable through inspections, service coordination, repair planning, and documentation.',
                'icon' => 'Wrench',
                'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1000&q=80',
                'benefits' => ['Preventive inspections', 'Fast issue tracking', 'Asset life extension'],
                'process' => ['Inspection', 'Issue report', 'Repair planning', 'Service log'],
                'related_project_slugs' => ['commercial-space-banani'],
            ],
        ];

        foreach ($services as $index => $service) {
            $services[$index]['record'] = Service::updateOrCreate(['slug' => $service['slug']], [
                ...$service,
                'is_active' => true,
                'sort_order' => $index + 1,
                'seo_title' => $service['title'].' | CAMPANION Services',
                'seo_description' => $service['summary'],
            ]);
        }

        return $services;
    }

    private function seedProjects(): array
    {
        $categories = [
            'residential' => ProjectCategory::updateOrCreate(['slug' => 'residential'], ['name' => 'Residential']),
            'commercial' => ProjectCategory::updateOrCreate(['slug' => 'commercial'], ['name' => 'Commercial']),
            'industrial' => ProjectCategory::updateOrCreate(['slug' => 'industrial'], ['name' => 'Industrial']),
        ];

        $projects = [
            [
                'category_slug' => 'residential',
                'title' => 'Modern Townhouse',
                'slug' => 'modern-townhouse',
                'price' => 'BDT 2.2 Crore',
                'location' => 'Model Town, Dhaka',
                'status' => 'Ongoing',
                'client' => 'Campanion Homes',
                'start_date' => '2025-03-01',
                'end_date' => '2026-12-31',
                'progress' => 68,
                'beds' => 3,
                'baths' => 3,
                'parking' => 2,
                'area' => '2500 sq ft',
                'featured_image' => '/images/hero-modern-home.png',
                'summary' => 'A premium residential townhouse designed around natural light, open planning, and family comfort.',
                'description' => 'Modern Townhouse combines refined architecture, efficient planning, landscaped outdoor areas, and high-quality construction supervision for a dependable residential investment.',
                'features' => ['Rooftop garden', 'Smart layout', 'Premium fittings', 'Secured parking'],
                'gallery' => ['/images/hero-modern-home.png', 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=80'],
                'timeline' => [
                    ['title' => 'Land preparation', 'date_label' => 'March 2025', 'description' => 'Site survey, soil testing, and boundary work completed.'],
                    ['title' => 'Structural work', 'date_label' => 'January 2026', 'description' => 'Core structure and major concrete works moved into active progress.'],
                    ['title' => 'Finishing phase', 'date_label' => 'August 2026', 'description' => 'Interior finishing, landscaping, and service installations begin.'],
                ],
            ],
            [
                'category_slug' => 'residential',
                'title' => 'Penthouse with City View',
                'slug' => 'penthouse-city-view',
                'price' => 'BDT 3.1 Crore',
                'location' => 'Gulshan, Dhaka',
                'status' => 'Upcoming',
                'client' => 'Northline Developments',
                'start_date' => '2026-09-01',
                'end_date' => '2028-06-30',
                'progress' => 12,
                'beds' => 4,
                'baths' => 4,
                'parking' => 2,
                'area' => '3100 sq ft',
                'featured_image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=80',
                'summary' => 'An elevated residential project with open terraces, city views, and curated premium finishes.',
                'description' => 'This penthouse development focuses on a private, high-comfort lifestyle with panoramic views, thoughtful amenities, and long-term property value in a prime Dhaka location.',
                'features' => ['Sky terrace', 'City-facing lounge', 'Private lift lobby', 'Concierge support'],
                'gallery' => ['https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?auto=format&fit=crop&w=900&q=80'],
                'timeline' => [
                    ['title' => 'Design approval', 'date_label' => 'May 2026', 'description' => 'Architectural plans and authority coordination are in progress.'],
                    ['title' => 'Sales launch', 'date_label' => 'September 2026', 'description' => 'Public booking and project presentation will begin.'],
                    ['title' => 'Construction start', 'date_label' => 'November 2026', 'description' => 'Foundation and site mobilization are scheduled.'],
                ],
            ],
            [
                'category_slug' => 'commercial',
                'title' => 'Commercial Space',
                'slug' => 'commercial-space-banani',
                'price' => 'BDT 2.5 Crore',
                'location' => 'Banani, Dhaka',
                'status' => 'Completed',
                'client' => 'Arc Business Group',
                'start_date' => '2023-02-01',
                'end_date' => '2025-04-30',
                'progress' => 100,
                'beds' => null,
                'baths' => null,
                'parking' => 4,
                'area' => '2500 sq ft',
                'featured_image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=80',
                'summary' => 'A ready commercial address for offices, showrooms, and service businesses in a connected area.',
                'description' => 'Commercial Space Banani provides flexible floor planning, visible frontage, strong access, and construction quality prepared for professional business use.',
                'features' => ['Open floor plate', 'High visibility', 'Power backup', 'Dedicated parking'],
                'gallery' => ['https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=900&q=80'],
                'timeline' => [
                    ['title' => 'Project handover', 'date_label' => 'April 2025', 'description' => 'Final inspection and owner handover completed.'],
                    ['title' => 'Interior setup', 'date_label' => 'May 2025', 'description' => 'Tenant fit-out and service setup started.'],
                    ['title' => 'Operational launch', 'date_label' => 'June 2025', 'description' => 'Commercial occupancy opened for business users.'],
                ],
            ],
            [
                'category_slug' => 'industrial',
                'title' => 'Industrial Logistics Hub',
                'slug' => 'industrial-logistics-hub',
                'price' => 'Custom Quote',
                'location' => 'Gazipur',
                'status' => 'Ongoing',
                'client' => 'Eastgate Logistics',
                'start_date' => '2025-07-01',
                'end_date' => '2027-10-31',
                'progress' => 42,
                'beds' => null,
                'baths' => null,
                'parking' => 12,
                'area' => '48,000 sq ft',
                'featured_image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=900&q=80',
                'summary' => 'A large-scale industrial facility planned for distribution, storage, and heavy operational flow.',
                'description' => 'The logistics hub is designed for efficient loading, durable industrial surfaces, safe vehicle circulation, and scalable utility planning.',
                'features' => ['Heavy duty flooring', 'Loading bays', 'Wide access road', 'Fire safety system'],
                'gallery' => ['https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=900&q=80', 'https://images.unsplash.com/photo-1565008447742-97f6f38c985c?auto=format&fit=crop&w=900&q=80'],
                'timeline' => [
                    ['title' => 'Site mobilization', 'date_label' => 'July 2025', 'description' => 'Equipment access, temporary facilities, and site safety setup completed.'],
                    ['title' => 'Foundation package', 'date_label' => 'February 2026', 'description' => 'Foundation and drainage works are under active progress.'],
                    ['title' => 'Steel structure', 'date_label' => 'November 2026', 'description' => 'Warehouse frame and roof structure installation scheduled.'],
                ],
            ],
        ];

        foreach ($projects as $index => $projectData) {
            $gallery = $projectData['gallery'];
            $timeline = $projectData['timeline'];
            $categorySlug = $projectData['category_slug'];
            unset($projectData['gallery'], $projectData['timeline'], $projectData['category_slug']);

            $project = Project::updateOrCreate(['slug' => $projectData['slug']], [
                ...$projectData,
                'project_category_id' => $categories[$categorySlug]->id,
                'is_featured' => $index < 3,
                'seo_title' => $projectData['title'].' | CAMPANION Projects',
                'seo_description' => $projectData['summary'],
            ]);

            foreach ($gallery as $imageIndex => $image) {
                $project->images()->updateOrCreate(['sort_order' => $imageIndex + 1], [
                    'image' => $image,
                    'alt_text' => $project->title,
                ]);

                GalleryItem::updateOrCreate([
                    'project_id' => $project->id,
                    'sort_order' => $imageIndex + 1,
                ], [
                    'title' => $project->title.' Gallery '.($imageIndex + 1),
                    'category' => $imageIndex === 0 ? 'Photos' : 'Construction Progress',
                    'image' => $image,
                    'alt_text' => $project->title,
                ]);
            }

            foreach ($timeline as $timelineIndex => $item) {
                $project->timelines()->updateOrCreate(['sort_order' => $timelineIndex + 1], $item);
            }

            $projects[$index]['record'] = $project;
        }

        return $projects;
    }

    private function seedBlogPosts(): array
    {
        $categories = [
            'Market Analysis' => BlogCategory::updateOrCreate(['slug' => 'market-analysis'], ['name' => 'Market Analysis']),
            'Buying Guide' => BlogCategory::updateOrCreate(['slug' => 'buying-guide'], ['name' => 'Buying Guide']),
            'Investment' => BlogCategory::updateOrCreate(['slug' => 'investment'], ['name' => 'Investment']),
            'Industry Insights' => BlogCategory::updateOrCreate(['slug' => 'industry-insights'], ['name' => 'Industry Insights']),
        ];

        $posts = [
            ['category' => 'Market Analysis', 'title' => 'Property Prices Rise 15% in Major Cities', 'slug' => 'property-prices-rise-major-cities', 'author' => 'Campanion Research', 'published_at' => '2026-07-12', 'read_time' => '5 min read', 'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1000&q=80', 'excerpt' => "Latest market data shows strong growth in residential demand across Dhaka's prime neighborhoods.", 'content' => ['Demand for verified residential projects remains strong across Dhaka as buyers prioritize trusted documentation, clear location advantages, and construction quality.', 'Prime areas continue to attract both end users and investors, while emerging neighborhoods are gaining attention because of improved connectivity and planned infrastructure.', 'For buyers, the most important step is comparing project status, handover timelines, developer credibility, and total ownership cost before making a commitment.'], 'tags' => ['Market', 'Dhaka', 'Investment']],
            ['category' => 'Buying Guide', 'title' => "First-Time Home Buyer's Guide 2025", 'slug' => 'first-time-home-buyers-guide-2025', 'author' => 'Campanion Advisory', 'published_at' => '2026-06-28', 'read_time' => '7 min read', 'featured_image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1000&q=80', 'excerpt' => "Essential tips and considerations for first-time property buyers in Bangladesh's current market.", 'content' => ['First-time buyers should begin with a clear budget, preferred location, family requirements, and financing plan before visiting projects.', 'Document verification is just as important as layout and finishing quality. Buyers should review ownership papers, approval status, utility plans, and payment terms.', 'A trusted advisor can help compare projects objectively and reduce the risk of choosing based only on presentation materials.'], 'tags' => ['Buying', 'Guide', 'Planning']],
            ['category' => 'Investment', 'title' => 'Best Investment Areas to Explore in 2025', 'slug' => 'best-investment-areas-2025', 'author' => 'Market Desk', 'published_at' => '2026-05-18', 'read_time' => '6 min read', 'featured_image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80', 'excerpt' => 'Discover the top locations offering the strongest potential returns for property investors.', 'content' => ['Investment potential depends on more than current price. Accessibility, future development, rental demand, and supply quality all shape long-term performance.', 'Established areas provide liquidity and confidence, while developing corridors may offer stronger growth if infrastructure delivery remains on track.', 'Investors should balance appreciation potential with practical holding costs, rental suitability, and exit options.'], 'tags' => ['Investment', 'Areas', 'Returns']],
            ['category' => 'Industry Insights', 'title' => 'How Project Management Reduces Construction Risk', 'slug' => 'project-management-reduces-construction-risk', 'author' => 'Engineering Team', 'published_at' => '2026-04-22', 'read_time' => '4 min read', 'featured_image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1000&q=80', 'excerpt' => 'Clear scheduling, reporting, and vendor coordination help protect budgets and delivery timelines.', 'content' => ['Construction risk usually appears through unclear scope, weak documentation, poor vendor coordination, and delayed decisions.', 'Project management creates a single rhythm for planning, procurement, progress review, quality checks, and handover reporting.', 'For owners, consistent reporting makes problems visible early enough to solve before they become expensive.'], 'tags' => ['Construction', 'Management', 'Risk']],
        ];

        foreach ($posts as $index => $post) {
            $category = $post['category'];
            unset($post['category']);

            $posts[$index]['record'] = BlogPost::updateOrCreate(['slug' => $post['slug']], [
                ...$post,
                'blog_category_id' => $categories[$category]->id,
                'status' => 'Published',
                'seo_title' => $post['title'].' | CAMPANION Blog',
                'seo_description' => $post['excerpt'],
            ]);
        }

        return $posts;
    }

    private function seedHomeAndAboutSettings(array $services, array $projects, array $blogPosts): void
    {
        $settings = [
            'company' => ['name' => 'CAMPANION', 'tagline' => "Bangladesh's trusted real estate and construction partner.", 'email' => 'cdc2015@gmail.com', 'phone' => '+880 1798 119729', 'address' => 'Dhaka, Bangladesh'],
            'home_hero' => ['title' => 'Find Your Perfect Home in Dhaka', 'subtitle' => 'Discover premium properties, new projects, and construction services from trusted professionals across top locations.', 'image' => '/images/hero-modern-home.png', 'tabs' => ['Buy', 'Rent', 'Projects']],
            'home_trust_stats' => [['title' => 'Verified Listings', 'metric' => '100% Verified', 'icon' => 'ShieldCheck'], ['title' => 'Trusted Agents', 'metric' => '5000+ Agents', 'icon' => 'UsersRound'], ['title' => 'Client Satisfaction', 'metric' => '4.8/5 Rating', 'icon' => 'Star']],
            'home_features' => [['title' => 'Plot Finder', 'action' => 'Find Plots'], ['title' => 'Market Trends', 'action' => 'View Trends'], ['title' => 'New Projects', 'action' => 'Browse Projects']],
            'trending_areas' => [['name' => 'Gulshan', 'count' => '812 Properties'], ['name' => 'Banani', 'count' => '659 Properties'], ['name' => 'Baridhara', 'count' => '1,105 Properties'], ['name' => 'Bashundhara', 'count' => '742 Properties'], ['name' => 'Uttara', 'count' => '684 Properties'], ['name' => 'Dhanmondi', 'count' => '531 Properties']],
            'about' => ['story' => 'CAMPANION was planned as a real estate and construction platform for clients who need more than listings.', 'mission' => 'To make property discovery and construction delivery more transparent, organized, and client-friendly.', 'vision' => 'To become a trusted platform for verified real estate projects and dependable construction services across Bangladesh.', 'certifications' => ['Registered Construction Partner', 'Safety Compliance Framework', 'Quality Supervision Standard', 'Verified Property Advisory']],
            'content_map' => ['featured_project_slugs' => array_column($projects, 'slug'), 'service_slugs' => array_column($services, 'slug'), 'blog_slugs' => array_column($blogPosts, 'slug')],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['name' => 'Muhammad Ali', 'location' => 'Dhaka', 'rating' => 5, 'quote' => 'Transparent pricing, verified properties, and professional guidance made our investment decision much easier.'],
            ['name' => 'Nusrat Jahan', 'location' => 'Gulshan', 'rating' => 5, 'quote' => 'The team explained every project detail clearly and helped us compare locations without pressure.'],
            ['name' => 'Arif Rahman', 'location' => 'Banani', 'rating' => 4, 'quote' => 'Their construction planning support gave us a clear budget, timeline, and vendor coordination process.'],
            ['name' => 'Samira Ahmed', 'location' => 'Bashundhara', 'rating' => 5, 'quote' => 'CAMPANION made the quote process simple and kept communication organized from the first meeting.'],
        ];

        foreach ($testimonials as $index => $testimonial) {
            Testimonial::updateOrCreate(['name' => $testimonial['name']], [
                ...$testimonial,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedTeamMembers(): void
    {
        $members = [
            ['name' => 'Rafiq Hasan', 'designation' => 'Managing Director', 'department' => 'Leadership', 'photo' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=700&q=80'],
            ['name' => 'Nadia Rahman', 'designation' => 'Head of Architecture', 'department' => 'Leadership', 'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=700&q=80'],
            ['name' => 'Tanvir Ahmed', 'designation' => 'Project Director', 'department' => 'Leadership', 'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=700&q=80'],
        ];

        foreach ($members as $index => $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], [
                ...$member,
                'bio' => 'Leadership profile prepared from current frontend content.',
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['question' => 'Can CAMPANION verify project documents before buying?', 'answer' => 'Yes. The advisory team can review project details, ownership papers, approvals, and handover information before you move forward.'],
            ['question' => 'Do you provide construction and architecture together?', 'answer' => 'Yes. We can support architecture, civil engineering, project management, construction, interior design, and maintenance.'],
            ['question' => 'How quickly will you respond to a quote request?', 'answer' => 'The team usually reviews new requests and responds with the next step within one business day.'],
            ['question' => 'Can I list a project with CAMPANION?', 'answer' => 'Yes. Verified developers and agents can contact us to submit project information for review.'],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], [
                ...$faq,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedOffices(): void
    {
        $offices = [
            ['city' => 'Dhaka Head Office', 'address' => 'Gulshan Avenue, Dhaka, Bangladesh', 'phone' => '+880 1798 119729', 'email' => 'cdc2015@gmail.com'],
            ['city' => 'Project Desk', 'address' => 'Banani, Dhaka, Bangladesh', 'phone' => '+880 1711 224466', 'email' => 'projects@campanion.com'],
        ];

        foreach ($offices as $index => $office) {
            OfficeLocation::updateOrCreate(['city' => $office['city']], [
                ...$office,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedPartners(): void
    {
        foreach (['Northline Developments', 'Arc Business Group', 'Eastgate Logistics'] as $index => $name) {
            Partner::updateOrCreate(['name' => $name], [
                'logo' => null,
                'url' => null,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function seedMedia(): void
    {
        $items = [
            ['title' => 'Hero Modern Home', 'file_path' => '/images/hero-modern-home.png', 'alt_text' => 'Modern luxury home architectural render', 'category' => 'Hero', 'mime_type' => 'image/png', 'size' => 2400000],
            ['title' => 'Construction Site', 'file_path' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=700&q=80', 'alt_text' => 'Construction site with crane and workers', 'category' => 'Services', 'mime_type' => 'image/jpeg', 'size' => 890000],
            ['title' => 'Commercial Building', 'file_path' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=700&q=80', 'alt_text' => 'Modern commercial office building exterior', 'category' => 'Projects', 'mime_type' => 'image/jpeg', 'size' => 740000],
            ['title' => 'Interior Feature', 'file_path' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=80', 'alt_text' => 'Warm modern interior design space', 'category' => 'Blog', 'mime_type' => 'image/jpeg', 'size' => 1100000],
        ];

        foreach ($items as $item) {
            Media::updateOrCreate(['title' => $item['title']], $item);
        }
    }

    private function seedInquiries(): void
    {
        ContactMessage::updateOrCreate(['email' => 'sadia@example.com', 'subject' => 'Project document verification'], ['name' => 'Sadia Islam', 'phone' => '+880 1700 100200', 'message' => 'I need help verifying project documents.', 'status' => 'In Review']);
        ContactMessage::updateOrCreate(['email' => 'nabila@example.com', 'subject' => 'General project help'], ['name' => 'Nabila Rahman', 'phone' => '+880 1700 100201', 'message' => 'Please share project details.', 'status' => 'New']);
        QuoteRequest::updateOrCreate(['email' => 'mahmud@example.com'], ['name' => 'Mahmud Karim', 'phone' => '+880 1700 100202', 'project_type' => 'Residential', 'location' => 'Bashundhara', 'estimated_budget' => '1Cr - 3Cr', 'message' => 'Residential construction in Bashundhara.', 'status' => 'New']);
        QuoteRequest::updateOrCreate(['email' => 'rafi@example.com'], ['name' => 'Rafi Chowdhury', 'phone' => '+880 1700 100203', 'project_type' => 'Renovation', 'location' => 'Banani', 'estimated_budget' => '50L - 1Cr', 'message' => 'Commercial renovation estimate.', 'status' => 'Responded']);
        NewsletterSubscriber::updateOrCreate(['email' => 'nabila.news@example.com'], ['subscribed_at' => now()]);
    }
}
