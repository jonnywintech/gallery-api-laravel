<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Gallery;
use App\Models\UserComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Method index
     *
     *it shows all galleries

     * @return void
     */
    public function index()
    {
        $query = Gallery::query();
        $galleries = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(9);
        return view('home', compact('galleries'));
    }

    /**
     * Method myGalleries
     *
     * @param $perPage $perPage
     *
     * list of all user galleries , with case of no pagination default is used
     *
     * @return void
     */
    public function myGalleries($perPage = 9)
    {
        $user_id = Auth::user()->id;
        $query = Gallery::where('user_id', $user_id);
        $galleries = $query->orderBy('created_at', 'DESC')->paginate($perPage);

        return view('home', compact('galleries'));
    }

    /**
     * Method view
     *
     * @param $id $id find gallery by id
     *
     * @return void
     */
    public function view($id)
    {
        $query = Gallery::query();
        $query = $query->where('id', $id);
        $final = $query->with(array('images' => function ($q) {
            $q->orderBy('order_number', 'ASC');
        }))->get();

        $comments = UserComment::with('comments')
            ->with('user')
            ->where('gallery_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('pages.view-images', ['gallery' => $final, 'comments' => $comments]);
    }

    /**
     * Method edit
     *
     * @param $id $id of gallery
     *
     * return view of gallery that can be edited
     *
     * @return void
     */
    public function edit($id)
    {
        $query = Gallery::query();
        $query = $query->where('id', $id);
        $final = $query->with(array('images' => function ($q) {
            $q->orderBy('order_number', 'ASC');
        }))->get();

        return view('pages.edit-images', ['gallery' => $final]);
    }

    /**
     * Method update
     *
     * @param Request $request
     *
     * find gallery by id from request and update name and images accordingly
     *
     * @return void return back to the gallery page
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        // dd($request);
        $gallery = Gallery::find($request->id);

        if ($gallery->name !== $request->gallery_name) {
            $gallery->name =  $request->gallery_name;
        }

        if ($gallery->gallery_image !== $request->gallery_image) {
            $gallery->main_image = $request->gallery_image;
        }

        if (!empty($request->elementsToBeDeleted)) {
            $images_to_delete = json_decode($request->elementsToBeDeleted);
            Image::whereIn('id', $images_to_delete)->delete();
        }

        $image_ids = $request->only('image_id')['image_id'];
        $image_url = $request->only('image')['image'];
        // update existing image url
        $images = Image::whereIn('id', $image_ids)->get();
        $len = count($images);
        for ($i = 0; $i < $len; $i++) {
            $images[$i]->image = $image_url[$i];
            $images[$i]->save();
        }


        // logic to change gallery positions;
        if (isset($request->image_id)) {
            $image_ids = $request->image_id;
            $image_positions = $request->position;

            $img_pos = array_combine($image_ids, $image_positions);

            $images_to_sort = Image::whereIn('id', $image_ids)->get();

            foreach ($images_to_sort as &$image) {
                $key = $image->id;
                $value = $img_pos[$key];
                $image->order_number = $value;
                $image->save();
            }
        }


        if (isset($request->image_new[0])) {
            $length = count($request->image_new);
            for ($i = 0; $i < $length; $i++) {
                $newImage = new Image();
                $newImage->gallery_id = $request->id;
                $newImage->image = $request->image_new[$i];
                $newImage->order_number = $request->position_new[$i];
                $newImage->save();
            }
        }

        $gallery->save();

        DB::commit();

        return redirect()->back();
    }


    /**
     * Method create
     *
     * takes two parameters from $request object: name and image
     *
     * @param Request $request  gallery_name and gallery_url to create gallery
     *
     * @return void
     */
    public function create(Request $request)
    {
        if (!empty($request->gallery_name) && !empty($request->gallery_url)) {

            $gallery = new Gallery();
            $user_id = Auth::user()->id;

            $gallery->user_id = $user_id;
            $gallery->name = $request->gallery_name;
            $gallery->main_image = $request->gallery_url;
            $gallery->save();

            $final[0] = $gallery;

            session()->flash('status_message', "Gallery created successfully");

            return view('pages.edit-images', ['gallery' => $final]);
            // return redirect()->back();
        }

        session()->flash('status_message', "Gallery not create fields are empty.");
        return redirect()->back();
    }

    /**
     * Method destroy
     *
     * @param Request $request takes gallery id
     *
     * @return void
     */
    public function destroy(Request $request)
    {
        $gallery = Gallery::find($request->id);
        $gallery->delete();

        return redirect()->back();
    }
}
