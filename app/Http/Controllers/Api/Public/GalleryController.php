<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $galleries = Gallery::where('status', 'active')
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->orderBy('sort_order')
            ->get();

        return response()->json(['success' => true, 'data' => $galleries]);
    }
}
