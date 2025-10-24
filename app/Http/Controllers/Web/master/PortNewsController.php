<?php

namespace App\Http\Controllers\Web\master;

use Illuminate\Http\Request;
use App\Models\PortNews;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PortNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        return view('portnews.index', compact('categories'));
    }

    /**
     * Get port news data via AJAX
     */
    public function getPortNews(Request $request)
    {

        $query = PortNews::with(['category'])->whereNotNull('news_title');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('news_title', 'like', "%{$search}%")
                    ->orWhere('news', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('kategori_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('post', $request->status);
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $portNews = $query->orderBy('date_create', 'desc')->paginate($perPage);

        // Debug: Log query results
        Log::info('PortNews Query Results:', [
            'total' => $portNews->total(),
            'count' => $portNews->count(),
            'items_count' => count($portNews->items()),
            'first_item' => $portNews->items()[0] ?? 'No items'
        ]);

        // Transform items to ensure category data is available
        $items = $portNews->items();
        foreach ($items as $item) {
            if (!$item->category) {
                // Fallback category if relation fails
                $item->category = (object) [
                    'id' => $item->kategori_id,
                    'name' => 'Kategori ' . $item->kategori_id
                ];
            }
        }

        return response()->json([
            'success' => true,
            'portnews' => $items,
            'pagination' => [
                'current_page' => $portNews->currentPage(),
                'last_page' => $portNews->lastPage(),
                'per_page' => $portNews->perPage(),
                'total' => $portNews->total(),
                'from' => $portNews->firstItem(),
                'to' => $portNews->lastItem(),
                'has_more_pages' => $portNews->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'news_title' => 'required|string|max:255',
            'news' => 'required|string',
            'kategori_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'post' => 'required|in:0,1',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the highest ID and add 1
            $maxId = PortNews::max('id') ?? 0;
            $newId = $maxId + 1;

            $data = [
                'id' => $newId,
                'news_title' => $request->news_title,
                'news' => $request->news,
                'kategori_id' => $request->kategori_id,
                'author' => $request->author,
                'post' => $request->post,
                'date_create' => now(),
            ];

            // Handle image upload
            if ($request->hasFile('img')) {
                $imageFile = $request->file('img');
                $imageName = 'portnews_' . $newId . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('upload/portnews'), $imageName);
                $data['img'] = 'upload/portnews/' . $imageName;
            }

            $portNews = PortNews::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dibuat',
                'portnews' => $portNews->load(['category'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PortNews $portNews)
    {
        return response()->json([
            'success' => true,
            'portnews' => $portNews->load(['category'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PortNews $portNews)
    {
        $validator = Validator::make($request->all(), [
            'news_title' => 'required|string|max:255',
            'news' => 'required|string',
            'kategori_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'post' => 'required|in:0,1',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'news_title' => $request->news_title,
                'news' => $request->news,
                'kategori_id' => $request->kategori_id,
                'author' => $request->author,
                'post' => $request->post,
            ];

            // Handle image upload
            if ($request->hasFile('img')) {
                // Delete old image if exists
                if ($portNews->img && file_exists(public_path($portNews->img))) {
                    unlink(public_path($portNews->img));
                }

                $imageFile = $request->file('img');
                $imageName = 'portnews_' . $portNews->id . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('upload/portnews'), $imageName);
                $updateData['img'] = 'upload/portnews/' . $imageName;
            }

            $portNews->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil diperbarui',
                'portnews' => $portNews->load(['category'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PortNews $portNews)
    {
        try {
            // Delete image if exists
            if ($portNews->img && file_exists(public_path($portNews->img))) {
                unlink(public_path($portNews->img));
            }

            $portNews->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
