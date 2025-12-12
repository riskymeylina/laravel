<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    // LIST SEMUA BERITA
    public function index()
    {
        return response()->json(
            News::with(['author', 'comments.user'])->latest()->get()
        );
    }

    // TAMBAH BERITA
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string',
            'content'  => 'required|string',
            'category' => 'required|string',
            'image'    => 'nullable|file|image|mimes:jpg,jpeg,png'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                                 ->store('news_images', 'public');
        }

        $news = News::create([
            'title'    => $request->input('title'),
            'content'  => $request->input('content'),
            'category' => $request->input('category'),
            'image'    => $imagePath,
            'user_id'  => Auth::id() 
        ]);

        return response()->json([
            'message' => 'Berita berhasil ditambahkan',
            'data'    => $news
        ]);
    }

    // DETAIL BERITA
    public function show($id)
    {
        return response()->json(
            News::with(['author', 'comments.user'])->findOrFail($id)
        );
    }

    // UPDATE BERITA
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title'    => 'nullable|string',
            'content'  => 'nullable|string',
            'category' => 'nullable|string',
            'image'    => 'nullable|file|image|mimes:jpg,jpeg,png'
        ]);

        // Tidak akses property protected
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news_images', 'public');
            $news->update(['image' => $path]);
        }

        $news->update($request->only(['title', 'content', 'category']));

        return response()->json([
            'message' => 'Berita berhasil diupdate',
            'data'    => $news
        ]);
    }

    // DELETE BERITA
    public function destroy($id)
    {
        News::findOrFail($id)->delete();

        return response()->json(['message' => 'Berita berhasil dihapus']);
    }
}
