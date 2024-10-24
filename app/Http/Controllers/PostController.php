<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // Display a listing of posts
    public function index(Request $request)
    {
        //$this->startSQL();
        $postSQL = Post::select('posts.id', 'posts.title', 'posts.body', 'posts.user_id', 'posts.created_at')->with('user');
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $wildcardSearch = '%' . $search . '%';
            // Initialize flag to check if it's a valid date
            $isDate = false;
            $mysqlDate = "";
            try {
                // Attempt to parse the search term as a full date
                $parsedDate = Carbon::parse($search);
                $mysqlDate = $parsedDate->format('Y-m-d');
                $isDate = true;
            } catch (\Exception $e) {
                // If parsing fails, treat it as partial input (not a full date)
                $isDate = false;
            }
            // Build the query with a flexible search
            $postSQL->where(function ($query) use ($wildcardSearch, $mysqlDate, $isDate, $search) {
                // Search in name and email with wildcards
                $query->where('title', 'like', $wildcardSearch)
                    ->orWhere('body', 'like', $wildcardSearch);
                // If it's a valid full date, search by exact date in created_at
                if ($isDate) {
                    $query->orWhereDate('posts.created_at', $mysqlDate);
                } else {
                    // If not a valid date, perform a partial search on the created_at (e.g. '%23%')
                    $query->orWhere('posts.created_at', 'like', '%' . $search . '%');
                }
            });
            // Join with users to access user.name
            $postSQL->join('users', 'posts.user_id', '=', 'users.id') // Assuming user_id is the foreign key in posts
                ->orWhere('users.name', 'like', $search);

        }
        $posts = $postSQL->orderBy('posts.id', 'DESC')->paginate(9);
        //$this->showSQL();

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

    public function startSQL()
    {
        DB::enableQueryLog();
    }

    public function showSQL()
    {
        dd(DB::getQueryLog());
    }

}
