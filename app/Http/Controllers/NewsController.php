<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada

class NewsController extends Controller
{
    // LIST SEMUA BERITA
    public function index()
    {
        // Mengembalikan data news beserta relasi dan diurutkan terbaru
        return response()->json([
            'status' => 'success',
            'data' => News::with(['author', 'comments.user'])->latest()->get()
        ]);
    }

    // TAMBAH BERITA
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string',
            'content'  => 'required|string',
            'category' => 'required|string',
            'gambar'   => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048' // Key disamakan 'gambar'
        ]);

        $imagePath = null;

        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('news_images', 'public');
        }

        $news = News::create([
            'title'    => $request->input('title'),
            'content'  => $request->input('content'),
            'category' => $request->input('category'),
            'image'    => $imagePath, // Nama kolom di DB tetap 'image'
            'user_id'  => Auth::id() 
        ]);

        return response()->json([
            'message' => 'Berita berhasil ditambahkan',
            'data'    => $news
        ], 201);
    }

    // UPDATE BERITA
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        // Proteksi: Pastikan hanya pemilik berita yang bisa edit (Opsional tapi disarankan)
        if ($news->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'    => 'nullable|string',
            'content'  => 'nullable|string',
            'category' => 'nullable|string',
            'gambar'   => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048' // Key disamakan 'gambar'
        ]);

        // 1. Update data teks
        $news->update($request->only(['title', 'content', 'category']));

        // 2. Logika Update Gambar
        if ($request->hasFile('gambar')) {
            // Hapus file lama jika ada di storage
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }

            // Simpan file baru
            $path = $request->file('gambar')->store('news_images', 'public');
            
            // Simpan path ke database
            $news->image = $path;
            $news->save();
        }

        return response()->json([
            'message' => 'Berita berhasil diupdate',
            'data'    => $news->fresh(['author', 'comments.user'])
        ]);
    }

    // DELETE BERITA
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Hapus file gambar dari storage sebelum hapus data di DB
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return response()->json(['message' => 'Berita berhasil dihapus']);
    }
}