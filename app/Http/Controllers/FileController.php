<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Incident;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Img;

class FileController extends Controller
{
    function upload(Request $request){
        $fileArray = array();
        $uploadKey = Carbon::now()->format('YmdHis');

        $files = Collection::wrap(request()->file('filepond'));
        $allfiles = $request->allFiles();

        $userStorage = '/uploads';
        if (!Storage::exists($userStorage)) {
            Storage::makeDirectory($userStorage, 0755, true);
        }

        $files->each(function ($file, $key) use (&$request, &$userStorage, &$fileArray, &$uploadKey) {

            // storage directory
            $userStorageDir = storage_path() . '/app' . $userStorage;

            // title
            $originalName = $file->getClientOriginalName();

            $theTitle = $originalName;
            if (strlen($originalName) > 50){
                $theTitle = substr($originalName, 0, 50);
            }
            $theTitle = pathinfo($theTitle, PATHINFO_FILENAME);

            // extension
            $extn = strtolower($file->getClientOriginalExtension());

            // path
            $ramdomChars = Str::random(10);
            $slug = Str::slug($ramdomChars);
            $path = $slug."-".$uploadKey."-".$request['profile_id'].".".$extn;

            // mime
            $mime = $file->getClientMimeType();

           // check if pdf file
           if($mime == 'application/pdf'){ // if the file is pdf
                // move file to storage directory
                $request->file('filepond')->move($userStorageDir, $path);
            }else{ // if the file is image
                // set image
                $img = Img::make($file);
                // optimization image
                $img->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->encode($extn, 75); // ext, size
                // // Save file to storage directory
                $img->save($userStorageDir . '/' . $path);
            }

            // Setup data into array
            array_push( $fileArray, array(
                'title' => $theTitle,
                'disk' => 'local',
                'path' => $path,
                'mime' => $mime,
                'type' => @$request['type'],
                'profile_id' => $request['profile_id'],
                'created_at' => Carbon::now(),
            ));
        });

        // Insert into database at once
        $uploadedFiles = File::insert($fileArray);

        return response()->json([
            'success' => true,
            'message' => 'Upload Success',
        ], 200);
    }

    function showFile($path) {
        $ext = explode(".", $path);
        $ext = end($ext);
        $ext = strtolower($ext);

        if($ext == 'pdf'){
            $mime_type = 'application/pdf';
        }else{
            $mime_type = 'image/'.$ext;
        }

        if( isset($path) ){
            $fileUrl = storage_path(). '/app/uploads/'.$path;
            return response()->file($fileUrl, array('Content-Type' => $mime_type));
        }else{
            return abort('403');
        }
    }

    function getPaginatedFiles(Request $request, $type='asset') {
        $files = File::where('type', $type)->orderBy('id', 'DESC')->paginate(15);
        
        if(@$request->userID){
            $files = File::where(['type'=> $type, 'profile_id' => $request->userID])->orderBy('id', 'DESC')->paginate(15);
        }
        
        return response()->json($files, 200);
    }
}
