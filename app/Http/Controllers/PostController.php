<?php

namespace App\Http\Controllers;

use App\Mail\NewPostCreated;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'posts' => Post::get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $this->validate($request, [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $authorName = Auth::check() ? Auth::user()->name : User::find($request->input('id'))->name;

        // Create a new post
        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author_name' => $authorName,
        ]);

            // Mail::to(Auth::user()->email)->send(new NewPostCreated($post));

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post,
            ]);
        } catch (\Exception $e) {

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Error creating post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        try {
            // Validate the request data
            $this->validate($request, [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            // Update the post
            $post->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => $post,
            ]);
        } catch (\Exception $e) {

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Error updating post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            // Delete the post
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);
        } catch (\Exception $e) {

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Error deleting post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
