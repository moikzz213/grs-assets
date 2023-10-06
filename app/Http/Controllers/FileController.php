<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

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

        return response()->json([
            'allfiles' => $allfiles,
            'files' => $files,
            'request' => request()->file('filepond'),
            'message' => 'Upload Success',
        ], 200);

        dd($allfiles);
        dd(request()->file('filepond'));


        $files->each(function ($file, $key) use (&$request, &$userStorage, &$fileArray, &$uploadKey) {

            $userStorageDir = storage_path() . '/app' . $userStorage;
            $fileName = $file->getClientOriginalName();
            if (strlen($fileName) > 10){
                // $fileName = substr($fileName, 0, 5).'-'.Str::random(5).'-';
                $fileName = Str::random(5).'-';
            }
            $title = pathinfo($fileName, PATHINFO_FILENAME);
            $extn = strtolower($file->getClientOriginalExtension());
            // $extn = 'jpg';
            $slugTitle = Str::slug($title);
            $path = $slugTitle."-".$uploadKey."-".$request['profile_id'].".".$extn;
            $mime = $file->getClientMimeType();

            // File Optimization
            // $img = Img::make($file);
            // $img->resize(800, null, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->encode($extn, 50);
            $file_type = 'image';

            // // Save file to storage directory
            // $img->save($userStorageDir . '/' . $path);

            // Setup data into array
            array_push( $fileArray, array(
                    // 'original_name' => $fileName,
                    'title' => $title,
                    'disk' => 'local',
                    'path' => $path,
                    // 'file_type' => $file_type,
                    'mime' => $mime,
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
}
