<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Article;
use App\Models\Setting;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        // For local dev, frontend usually runs on 5173. For production, change to actual domain.
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        
        $packages = Package::where('status', 'active')->get();
        $articles = Article::where('status', 'published')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Static routes
        $staticRoutes = [
            '/',
            '/paket-umroh',
            '/galeri',
            '/artikel',
            '/faq',
            '/kontak',
            '/login',
            '/register'
        ];

        foreach ($staticRoutes as $route) {
            $xml .= '<url>';
            $xml .= '<loc>' . $frontendUrl . $route . '</loc>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>' . ($route === '/' ? '1.0' : '0.8') . '</priority>';
            $xml .= '</url>';
        }

        // Packages
        foreach ($packages as $package) {
            $xml .= '<url>';
            $xml .= '<loc>' . $frontendUrl . '/paket-umroh/' . $package->slug . '</loc>';
            $xml .= '<lastmod>' . $package->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.9</priority>';
            $xml .= '</url>';
        }

        // Articles
        foreach ($articles as $article) {
            $xml .= '<url>';
            $xml .= '<loc>' . $frontendUrl . '/artikel/' . $article->slug . '</loc>';
            $xml .= '<lastmod>' . $article->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
