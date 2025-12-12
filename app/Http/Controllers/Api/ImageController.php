<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function show($filename)
    {
        $path = $filename;

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        $file = Storage::disk('public')->get($path);

        // FIX: Intelephense-friendly
        $type = Storage::mimeType('public/' . $path);

        return response($file)->header('Content-Type', $type);
    }
}
