<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // CREATE COMMENT
    public function store(Request $request)
    {
        $request->validate([
            'news_id' => 'required|exists:news,id',
            'body'    => 'required|string'
        ]);

        $comment = Comment::create([
            'news_id' => $request->news_id,
            'user_id' => Auth::id(),
            'body'    => $request->body
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data'    => $comment
        ]);
    }

    // LIST COMMENTS BY NEWS
    public function listByNews($newsId)
    {
        return response()->json(
            Comment::with('user')
                ->where('news_id', $newsId)
                ->latest()
                ->get()
        );
    }
    
    // READ DETAIL COMMENT
    public function show($id)
    {
        $comment = Comment::with('user')->findOrFail($id);

        return response()->json($comment);
    }

    // UPDATE COMMENT
    public function update(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        $comment = Comment::findOrFail($id);

        // Hanya pemilik komentar yang boleh update
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'body' => $request->body
        ]);

        return response()->json([
            'message' => 'Komentar berhasil diupdate',
            'data' => $comment
        ]);
    }


    // DELETE COMMENT
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Hanya pemilik komentar yang boleh hapus
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }
}
