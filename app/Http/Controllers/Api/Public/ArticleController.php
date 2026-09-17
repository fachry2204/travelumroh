<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::where('status', 'published')
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->orderByDesc('published_at')
            ->paginate($request->per_page ?? 9);

        return response()->json(['success' => true, 'data' => $articles]);
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return response()->json(['success' => true, 'data' => $article]);
    }
}
