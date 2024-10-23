<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Display a listing of posts
    public function index()
    {
        $posts = Post::with('user')->latest()->get();
        return response()->json(['message' => 'Posts.', 'data' => $posts], 200);

    }

    // Store a newly created post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        $user = Auth::user();
        $data['title'] = $request->title;
        $data['body'] = $request->body;
        $data['user_id'] = $user->id;
        $arr['post'] = Post::create($data);
        $arr['user'] = $user;
        return response()->json(['message' => 'Post created successfully.', 'data' => $arr], 200);
    }

    // Display the specified post
    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        if ($post) {
            return response()->json(['message' => 'Post', 'data' => $post], 200);
        }
        return response()->json(['message' => 'No post found.'], 400);

    }

    // Update the specified post
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255',
            'body' => 'string',
        ]);
        $post = Post::findOrFail($id);
        if ($post) {
            $user = Auth::user();
            if ($user->id === $post->user_id) {
                $data['title'] = $request->title;
                $data['body'] = $request->body;
                $data['user_id'] = $user->id;
                $post->update($data);
                return response()->json(['message' => 'Post updated successfully.', 'data' => $post], 200);
            }
        }
        return response()->json(['message' => 'No post found.'], 400);
    }

    // Remove the specified post
    public function destroy($id)
    {
        Post::destroy($id);
        return response()->json(['message' => 'Post deleted successfully.'], 200);
    }
}
