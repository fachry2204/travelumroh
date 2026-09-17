<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\CmsPage;
use App\Models\Package;
use App\Models\Gallery;
use App\Models\Article;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $hero = CmsPage::where('type', 'hero')->where('status', 'active')->first();
        $excellence = CmsPage::where('type', 'excellence')->where('status', 'active')->first();
        $contact = CmsPage::where('type', 'contact')->where('status', 'active')->first();
        $seo = CmsPage::where('type', 'seo')->first();

        $packages = Package::where('status', 'active')
            ->where('remaining_seat', '>', 0)
            ->orderBy('departure_date')
            ->limit(6)
            ->get();

        $testimonials = Testimonial::where('status', 'active')->orderBy('sort_order')->get();
        $galleries = Gallery::where('status', 'active')->orderBy('sort_order')->limit(9)->get();
        $articles = Article::where('status', 'published')->orderByDesc('published_at')->limit(3)->get();
        $faqs = Faq::where('status', 'active')->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'hero' => $hero ? json_decode($hero->content) : null,
                'excellence' => $excellence ? json_decode($excellence->content) : null,
                'contact' => $contact ? json_decode($contact->content) : null,
                'seo' => $seo ? ['meta_title' => $seo->meta_title, 'meta_description' => $seo->meta_description] : null,
                'packages' => $packages,
                'testimonials' => $testimonials,
                'galleries' => $galleries,
                'articles' => $articles,
                'faqs' => $faqs,
            ],
        ]);
    }

    public function settings()
    {
        // Ambil hanya setting yang sifatnya publik
        $publicKeys = ['app_name', 'app_email', 'app_phone', 'app_address', 'app_logo', 'app_favicon'];
        $settings = Setting::whereIn('setting_key', $publicKeys)->pluck('setting_value', 'setting_key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function getPage($type)
    {
        $page = CmsPage::where('type', $type)->first();
        return response()->json([
            'success' => true,
            'data' => $page ? [
                'type' => $page->type,
                'title' => $page->title,
                'content' => json_decode($page->content),
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
            ] : null
        ]);
    }
}
