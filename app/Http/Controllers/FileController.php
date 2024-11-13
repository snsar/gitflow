<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function uploadVulnerable(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName(); // Lỗi: Không validate filename

            // Lỗi: Lưu trực tiếp vào storage mà không sanitize
            $file->move(storage_path('files'), $filename);

            return response()->json([
                'success' => true,
                'filename' => $filename
            ]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function showUploadForm()
    {
        return view('upload');
    }

    public function getFile($filename)
    {
        // Lỗ hổng: Không sanitize đường dẫn file
        $path = storage_path('files/' . $filename);
        return response()->download($path);
    }
}
