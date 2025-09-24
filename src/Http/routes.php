<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

// Define base storage dir for all routes
$hgrhBase = storage_path('app/public/hgrh');

Route::post('/file-browser-files', function (Request $request) use ($hgrhBase) {
    $relativeDir = trim($request->input('dir', ''), '/');
    $dir = $hgrhBase . ($relativeDir ? '/' . $relativeDir : '');

    if (!File::exists($dir)) {
        $dir = $hgrhBase;
    }

    $dir = str_replace('\\', '/', $dir);
    if (!str_starts_with($dir, str_replace('\\', '/', $hgrhBase))) abort(403);

    $folders = collect(File::directories($dir))
        ->map(fn($f) => [
            'name' => basename($f),
            'path' => ltrim(str_replace($hgrhBase, '', $f), '/'),
            'isDir' => true
        ]);

    $files = collect(File::files($dir))
        ->map(fn($f) => [
            'name' => $f->getFilename(),
            'path' => ltrim(str_replace($hgrhBase, '', $f->getPathname()), '/'),
            'isDir' => false
        ]);

    return response()->json($folders->merge($files)->values());
});

Route::post('/file-browser-upload', function (Request $request) use ($hgrhBase) {
    $relativeDir = trim($request->input('dir', ''), '/');
    $dir = $hgrhBase . ($relativeDir ? '/' . $relativeDir : '');
    $dir = str_replace('\\', '/', $dir);

    if (!str_starts_with($dir, str_replace('\\', '/', $hgrhBase))) abort(403);
    if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);

    $file = $request->file('file');
    $file->move($dir, $file->getClientOriginalName());

    return response()->json([
        'success' => true,
        'url' => asset('storage/hgrh/' . ($relativeDir ? $relativeDir . '/' : '') . $file->getClientOriginalName())
    ]);
});

Route::post('/file-browser-create-folder', function (Request $request) use ($hgrhBase) {
    $relativeDir = trim($request->input('dir', ''), '/');
    $folder = $request->input('folder');
    $dir = $hgrhBase . ($relativeDir ? '/' . $relativeDir : '');
    $dir = str_replace('\\', '/', $dir);

    if (!str_starts_with($dir, str_replace('\\', '/', $hgrhBase))) abort(403);
    if ($folder) mkdir($dir . '/' . $folder, 0755, true);

    return response()->json(['success' => true]);
});

Route::post('/file-browser-delete', function (Request $request) use ($hgrhBase) {
    $path = trim($request->input('path', ''), '/');
    $isDir = $request->boolean('isDir', false);
    $fullPath = $hgrhBase . ($path ? '/' . $path : '');
    $fullPath = str_replace('\\', '/', $fullPath);

    if (!str_starts_with($fullPath, str_replace('\\', '/', $hgrhBase))) abort(403);

    if ($isDir) {
        if (count(File::files($fullPath)) > 0 || count(File::directories($fullPath)) > 0) {
            return response()->json(['success' => false, 'message' => 'Folder not empty']);
        }
        rmdir($fullPath);
    } else {
        unlink($fullPath);
    }

    return response()->json(['success' => true]);
});

// Optional: keep if you need a GET endpoint
Route::get('/file-browser', function (Request $request) use ($hgrhBase) {
    $relativeDir = trim($request->get('dir', ''), '/');
    $dir = $hgrhBase . ($relativeDir ? '/' . $relativeDir : '');

    if (!str_starts_with($dir, $hgrhBase)) {
        abort(403);
    }

    $items = collect(File::directories($dir))
        ->map(fn($folder) => [
            'name' => basename($folder),
            'path' => ltrim(str_replace($hgrhBase, '', $folder), '/'),
            'isDir' => true,
        ]);

    $files = collect(File::files($dir))
        ->map(fn($file) => [
            'name' => $file->getFilename(),
            'path' => ltrim(str_replace($hgrhBase, '', $file->getPathname()), '/'),
            'isDir' => false,
        ]);

    return Response::json($items->merge($files)->values());
});

// App routes
Route::middleware(['web'])->controller('\Bishopm\Hgrh\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
});
