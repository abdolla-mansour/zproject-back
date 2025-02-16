<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleriesController extends Controller
{
    public function index()
    {
        $galleries = Gallery::paginate(6);
        if (!$galleries) {
            return response()->json([['data' => null, 'success' => false], 401]);
        }
        $galleries= Gallery::all();
        return view('galleries.index', compact('galleries'));
    }


    
    public function show(Request $req){

        $gallery = Gallery::with('images')->find($req->query('id'));

        return view('galleries.show', compact('gallery'));
    }

    




    public function store(Request $req){



        $gallery = new Gallery();
        $gallery->title = $req->title;

        if($req->hasFile('thumbnail')){
            
            $thm = $req->file('thumbnail');
            $thm->move(public_path('galleries'), $thm->getClientOriginalName());
            $path = url('/galleries/' .  $thm->getClientOriginalName() );
            $gallery->thumbnail = $path;
            
        }

        $gallery->save();

            
        if($req->hasFile('images')){
            foreach($req->file('images') as $img){
                
                

            // dd($gallery->id .   $thm->getClientOriginalName());
    
            $img->move(public_path('galleries'), $img->getClientOriginalName());
            $path = url('/galleries/' .  $img->getClientOriginalName() );
       
    

            $image = new Image(); 
            $image->path = $path;
            $image->gallery_id = $gallery->id;
            

                $image->save();
            }
        }


        $galleries = Gallery::with('images')->get();
        return view('galleries.index', compact('galleries'));
        
    }
    
    
    
    
    public function delete(Request $req){
        $gallery = Gallery::find($req->query('id'));
        
        $gallery->delete();

        $galleries = Gallery::with('images')->get();
        return view('galleries.index', compact('galleries'));

    }

}
