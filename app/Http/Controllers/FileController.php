<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class FileController extends Controller
{
    public function search($query){
        return File::where('title', 'LIKE', '%'.$query.'%')->get();
    }

    public function destroy($id, $filename) {
        File::destroy($id);
        Storage::disk('public')->delete('images/'.$filename);
        return response()->json(['message' => 'Destroyed file successfully', 'file_id' => $id, 'filename' => $filename]);
    }

    public function getImageFromStorage($filename){
        if(Storage::disk('public')->exists('images/'.$filename)){
            // return Storage::disk('public')->get('images/'.$filename);
            return Storage::url('images/'.$filename);
        } else return false;
    }
}
