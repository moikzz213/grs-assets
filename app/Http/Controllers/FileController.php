<?php

namespace App\Http\Controllers;

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
    //
    public function storeData(Request $request){
       
        if(request()->file('filename')){
            // upload image
            $fileArray = array();
            $uploadDate = Carbon::now()->format('YmdHis');
            $file = Collection::wrap(request()->file('filename'));
            
            $userStorage = '/public/uploads';
            if (!Storage::exists($userStorage)) {
                Storage::makeDirectory($userStorage, 0755, true);
            } 
            
                $userStorageDir = storage_path() . '/app' . $userStorage;
                $fileName = $file[0]->getClientOriginalName();
                if (strlen($fileName) > 20){ 
                    $fileName = Str::random(15);
                }

                $title = pathinfo($fileName, PATHINFO_FILENAME);
                $extn = strtolower($file[0]->getClientOriginalExtension());
                $slugTitle = Str::slug($title, '-');
                $path = $slugTitle."-".$uploadDate.".".$extn;
                $mime = $file[0]->getClientMimeType();

                if($extn == 'pdf' || $extn == 'PDF'){
                    $file[0]->move($userStorageDir, $path);
                }else{
                    // File Optimization
                    $img = Img::make($file[0]);
                    $img->encode($extn, 50);

                    // Save file to storage directory
                    $img->save($userStorageDir . '/' . $path);
                }
              
                // Setup data into array
                array_push( $fileArray, array(
                    'original_name' => $fileName,
                    'title' => $title,
                    'disk' => 'local',
                    'path' => $path, 
                    'type' => $request->type,
                    'mime' => $mime,
                    'profile_id' => $request->profile_id,
                    'created_at' => Carbon::now(),
                ));
            
            $data = null;
            // Recursive create
            $idsToSync = array();
            $returnDataImage = array();
            foreach ($fileArray as $k => $singleFile) {
                $returnDataImage[$k] = $singleFile;
                $imgId = DB::table('files')->insertGetId($singleFile);
                $returnDataImage[$k]['id'] = $imgId;
                array_push($idsToSync, $imgId);
            } 

            if($request->type == 'incident'){
                $data = Incident::where('id', '=', $request->ID)->first();  
            }

            if($data && count($idsToSync) > 0){
                $data->files()->attach($idsToSync);
            } 

            return response()->json([ 
               $returnDataImage, 
            ], 200);
        }
    }

    public function removeData(Request $request){
       
        $data = Incident::where('id', '=', $request->ID)->first();  
        $data->files()->detach($request['data']['id']);

        $userStorage = '/public/uploads';
        $image_path = public_path().'/storage/uploads/'.$request['data']['path'];
        unlink($image_path); 

        return response()->json([ 
            "msg" => 'Image has been deleted', 
         ], 200);
    }
}
