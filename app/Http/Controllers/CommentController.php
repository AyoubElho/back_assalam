<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    function store(Request $request)
    {
        $validator = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string',
        ]);

        $user = Auth::user();

        $comm = Comment::create([
            'post_id' => $validator['post_id'],
            'user_id' => $user['id'],
            'comment' => $validator['comment'],
        ]);

        return response()->json($comm);

    }

    public function getAll($post_id)
    {
        // Start the query
        $query = Comment::with('user')->orderBy('created_at', 'desc');

        // If post_id is provided, filter by it
        if ($post_id) {
            $query->where('post_id', $post_id);
        }

        // Paginate the results
        $comments = $query->paginate(10);

        return response()->json($comments);
    }
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Ensure the authenticated user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }




}
