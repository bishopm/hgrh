<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::post('/file-browser-files', function (\Illuminate\Http\Request $request) {
    $baseDir = public_path('storage');
    $relativeDir = trim($request->input('dir', ''), '/');
    $dir = $baseDir . ($relativeDir ? '/' . $relativeDir : '');
    if (!\Illuminate\Support\Facades\File::exists($dir)) {
        $dir = $baseDir;
    }
    $dir = str_replace('\\','/',$dir);
    if (!str_starts_with($dir, str_replace('\\','/',$baseDir))) abort(403);

    $folders = collect(\Illuminate\Support\Facades\File::directories($dir))
        ->map(fn($f)=>['name'=>basename($f),'path'=>ltrim(str_replace($baseDir,'',$f),'/'),'isDir'=>true]);
    $files = collect(\Illuminate\Support\Facades\File::files($dir))
        ->map(fn($f)=>['name'=>$f->getFilename(),'path'=>ltrim(str_replace($baseDir,'',$f->getPathname()),'/'),'isDir'=>false]);

    return response()->json($folders->merge($files)->values());
});

Route::post('/file-browser-upload', function (\Illuminate\Http\Request $request) {
    $baseDir = public_path('storage');
    $relativeDir = trim($request->input('dir',''),'/');
    $dir = $baseDir . ($relativeDir ? '/'.$relativeDir : '');
    $dir = str_replace('\\','/',$dir);
    if (!str_starts_with($dir, str_replace('\\','/',$baseDir))) abort(403);
    if (!\Illuminate\Support\Facades\File::exists($dir)) \Illuminate\Support\Facades\File::makeDirectory($dir,0755,true);
    $file = $request->file('file');
    $file->move($dir,$file->getClientOriginalName());
    return response()->json(['success'=>true]);
});

Route::post('/file-browser-create-folder', function (\Illuminate\Http\Request $request) {
    $baseDir = public_path('storage');
    $relativeDir = trim($request->input('dir',''),'/');
    $folder = $request->input('folder');
    $dir = $baseDir . ($relativeDir ? '/'.$relativeDir : '');
    $dir = str_replace('\\','/',$dir);
    if (!str_starts_with($dir, str_replace('\\','/',$baseDir))) abort(403);
    if ($folder) mkdir($dir.'/'.$folder,0755,true);
    return response()->json(['success'=>true]);
});

Route::post('/file-browser-delete', function (\Illuminate\Http\Request $request) {
    $baseDir = public_path('storage');
    $path = trim($request->input('path',''),'/');
    $isDir = $request->input('isDir',false);
    $fullPath = $baseDir.($path ? '/'.$path : '');
    $fullPath = str_replace('\\','/',$fullPath);
    if (!str_starts_with($fullPath,str_replace('\\','/',$baseDir))) abort(403);
    if ($isDir) {
        if (count(\Illuminate\Support\Facades\File::files($fullPath))>0 || count(\Illuminate\Support\Facades\File::directories($fullPath))>0) {
            return response()->json(['success'=>false,'message'=>'Folder not empty']);
        }
        rmdir($fullPath);
    } else unlink($fullPath);
    return response()->json(['success'=>true]);
});

Route::get('/file-browser', function (Request $request) {
    $relativeDir = trim($request->get('dir', ''), '/');
    $dir = public_path($relativeDir);

    if (! str_starts_with($dir, public_path())) {
        abort(403);
    }

    $items = collect(File::directories($dir))
        ->map(fn($folder) => [
            'name' => basename($folder),
            'path' => ltrim(str_replace(public_path(), '', $folder), '/'),
            'isDir' => true,
        ]);

    $files = collect(File::files($dir))
        ->map(fn($file) => [
            'name' => $file->getFilename(),
            'path' => ltrim(str_replace(public_path(), '', $file->getPathname()), '/'),
            'isDir' => false,
        ]);

    return Response::json($items->merge($files)->values());
});

// App routes
Route::middleware(['web'])->controller('\Bishopm\Hgrh\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
});


