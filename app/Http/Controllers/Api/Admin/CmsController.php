<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    // Articles
    public function articles(Request $request)
    {
        $articles = Article::with('author')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);
        return response()->json(['success' => true, 'data' => $articles]);
    }

    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:300',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'status' => 'in:draft,published',
            'featured_image' => 'nullable|string|max:500',
        ]);
        if ($request->hasFile('image')) {
            $validated['featured_image'] = $request->file('image')->store('articles', 'public');
        }
        $validated['created_by'] = auth()->id();
        if ($validated['status'] === 'published') $validated['published_at'] = now();
        $article = Article::create($validated);
        return response()->json(['success' => true, 'message' => 'Artikel berhasil dibuat', 'data' => $article], 201);
    }

    public function updateArticle(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:300',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'status' => 'in:draft,published',
            'featured_image' => 'nullable|string|max:500',
        ]);
        if ($request->hasFile('image')) {
            $validated['featured_image'] = $request->file('image')->store('articles', 'public');
        }
        if (isset($validated['status']) && $validated['status'] === 'published' && !$article->published_at) {
            $validated['published_at'] = now();
        }
        $article->update($validated);
        return response()->json(['success' => true, 'message' => 'Artikel berhasil diupdate', 'data' => $article]);
    }

    public function destroyArticle(Article $article)
    {
        $article->delete();
        return response()->json(['success' => true, 'message' => 'Artikel berhasil dihapus']);
    }

    public function uploadArticleImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $path = $request->file('image')->store('articles', 'public');
        return response()->json(['success' => true, 'data' => ['url' => asset('storage/' . $path)]]);
    }

    public function uploadHeroImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:5120']);
        $path = $request->file('image')->store('hero', 'public');
        return response()->json([
            'success' => true,
            'message' => 'Gambar banner hero berhasil diupload',
            'data' => [
                'path' => $path,
                'url' => asset('storage/' . $path),
            ]
        ]);
    }

    // Galleries
    public function galleries(Request $request)
    {
        $galleries = Gallery::orderBy('sort_order')->paginate($request->per_page ?? 20);
        return response()->json(['success' => true, 'data' => $galleries]);
    }

    public function storeGallery(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'image' => 'nullable|image|max:5012',
            'images.*' => 'nullable|image|max:5012',
            'caption' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ]);

        $created = [];
        $albumTitle = $request->title;

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $maxOrder = Gallery::max('sort_order') ?: 0;
            foreach ($files as $file) {
                $path = $file->store('galleries', 'public');
                $created[] = Gallery::create([
                    'title' => $albumTitle,
                    'image_path' => $path,
                    'caption' => $request->caption,
                    'category' => $request->category,
                    'sort_order' => ++$maxOrder,
                    'status' => 'active',
                ]);
            }
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('galleries', 'public');
            $created[] = Gallery::create([
                'title' => $albumTitle,
                'image_path' => $path,
                'caption' => $request->caption,
                'category' => $request->category,
                'sort_order' => (Gallery::max('sort_order') ?: 0) + 1,
                'status' => 'active',
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Pilih minimal 1 file gambar untuk diupload'], 422);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Album Galeri "' . $albumTitle . '" (' . count($created) . ' foto) berhasil ditambahkan', 
            'data' => $created
        ], 201);
    }

    public function destroyGallery(Gallery $gallery)
    {
        $gallery->delete();
        return response()->json(['success' => true, 'message' => 'Foto Galeri berhasil dihapus']);
    }

    public function destroyAlbum(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $count = Gallery::where('title', $request->title)->delete();
        return response()->json(['success' => true, 'message' => "Album Galeri '{$request->title}' ({$count} foto) berhasil dihapus"]);
    }

    public function updateAlbum(Request $request)
    {
        $request->validate([
            'old_title' => 'required|string',
            'title' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
        ]);

        Gallery::where('title', $request->old_title)->update([
            'title' => $request->title,
            'category' => $request->category,
        ]);

        return response()->json(['success' => true, 'message' => 'Detail album berhasil diperbarui']);
    }

    public function addPhotosToAlbum(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'images' => 'required|array',
            'images.*' => 'image|max:5012',
        ]);

        $created = [];
        $files = $request->file('images');
        $maxOrder = Gallery::where('title', $request->title)->max('sort_order') ?: 0;

        foreach ($files as $file) {
            $path = $file->store('galleries', 'public');
            $created[] = Gallery::create([
                'title' => $request->title,
                'image_path' => $path,
                'category' => $request->category,
                'sort_order' => ++$maxOrder,
                'status' => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' foto baru berhasil ditambahkan ke album',
            'data' => $created
        ]);
    }

    public function reorderGalleries(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:galleries,id',
            'orders.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->orders as $item) {
            Gallery::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan foto galeri berhasil diperbarui']);
    }

    // FAQs
    public function faqs()
    {
        return response()->json(['success' => true, 'data' => Faq::orderBy('sort_order')->get()]);
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
        ]);
        $faq = Faq::create(array_merge($validated, ['sort_order' => Faq::max('sort_order') + 1, 'status' => 'active']));
        return response()->json(['success' => true, 'message' => 'FAQ berhasil ditambahkan', 'data' => $faq], 201);
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'sometimes|string|max:500',
            'answer' => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'status' => 'in:active,inactive',
        ]);
        $faq->update($validated);
        return response()->json(['success' => true, 'message' => 'FAQ berhasil diupdate', 'data' => $faq]);
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();
        return response()->json(['success' => true, 'message' => 'FAQ berhasil dihapus']);
    }

    // CMS Pages
    public function pages()
    {
        return response()->json(['success' => true, 'data' => CmsPage::orderBy('sort_order')->get()]);
    }

    public function updatePage(Request $request, $type)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:200',
            'content' => 'nullable',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive',
        ]);
        if (isset($validated['content']) && (is_array($validated['content']) || is_object($validated['content']))) {
            $validated['content'] = json_encode($validated['content']);
        }
        $page = CmsPage::updateOrCreate(['type' => $type], $validated);
        return response()->json(['success' => true, 'message' => 'Halaman CMS berhasil diupdate', 'data' => $page]);
    }

    // Testimonials
    public function testimonials()
    {
        return response()->json(['success' => true, 'data' => Testimonial::orderBy('sort_order')->get()]);
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'package_name' => 'nullable|string|max:200',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|max:2048']);
            $validated['photo'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial = Testimonial::create(array_merge($validated, [
            'sort_order' => Testimonial::max('sort_order') + 1,
            'status' => 'active'
        ]));

        return response()->json(['success' => true, 'message' => 'Testimoni berhasil ditambahkan', 'data' => $testimonial], 201);
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:200',
            'package_name' => 'nullable|string|max:200',
            'content' => 'sometimes|string',
            'rating' => 'sometimes|integer|min:1|max:5',
            'photo' => 'nullable|string|max:500',
            'status' => 'sometimes|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|max:2048']);
            $validated['photo'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return response()->json(['success' => true, 'message' => 'Testimoni berhasil diperbarui', 'data' => $testimonial]);
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json(['success' => true, 'message' => 'Testimoni berhasil dihapus']);
    }
}
