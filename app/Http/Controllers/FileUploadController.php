<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\File;


class FileUploadController extends Controller
{
    public function test(){
        Storage::disk('local')->put('another_example.txt', 'Contents');
    }

    public function upload_image(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,png,jpeg,gif|max:2048' // Allow specific file types
        ]);

        if ($request->hasFile('file')) {

            $file = new File;

            $fileData = $request->file('file');
            $fileName = $request['name'].'.'.pathinfo($fileData->getClientOriginalName())['extension'];

            if( $file->where('title', '=', $fileName)->exists() ){
                abort(403,'Taki plik już istnieje! Zmień nazwę lub usuń ten plik ');
            }

            $path = 'images/'.$fileName;

            Storage::disk('public')->putFileAs('images', $fileData, $fileName);

            // Putting file path into the database
            $file->title = $fileName;
            $file->url = Storage::url($path);

            $file->save();
            
            return response()->json([
                'message' => 'File uploaded successfully',
                'file_path' => Storage::url($path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

}
