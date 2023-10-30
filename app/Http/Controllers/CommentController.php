<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\UserComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    /**
     * Method create
     *
     * @param CommentRequest $request takes only comment parameters
     *
     * create comment
     *
     * @return void
     */
    public function create(CommentRequest $request)
    {
        $comment = Comment::create($request->only('comment'));

        $user_id = Auth::user()->id;
        $gallery_id = $request->id;
        $comment_id = $comment->id;

        $user_comment = new UserComment(
            ['user_id' => $user_id, 'gallery_id' => $gallery_id, 'comment_id' => $comment_id]
        );
        $user_comment->save();

        return redirect()->back();
    }

    /**
     * Method delete
     *
     * @param Request $request takes comment_id from request object
     *
     * deletes comment
     *
     * @return void
     */
    public function delete(Request $request)
    {
        // dd($request);
        $comment = Comment::findOrFail($request->only('comment_id'))->first();
        $comment->delete();

        return redirect()->back();
    }
}
