<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostReactionController extends Controller
{
    /**
     * Add or update a reaction (like or dislike) to a post.
     */
    public function react(Request $request, $postId)
    {
        // Validate the reaction input (either 'like' or 'dislike')
        $validator = Validator::make($request->all(), [
            'reaction' => 'required|in:like,dislike',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid reaction type.',
                'messages' => $validator->errors()
            ], 400);
        }

        // Get the current authenticated user
        $user = Auth::user();

        // Check if the post exists
        $post = Post::find($postId);
        if (!$post) {
            return response()->json([
                'error' => 'Post not found.'
            ], 404);
        }

        // Check if the user has already reacted to this post
        $existingReaction = PostReaction::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($existingReaction) {
            // Update the existing reaction
            $existingReaction->reaction = $request->reaction;
            $existingReaction->save();
        } else {
            // Add a new reaction if the user hasn't reacted to this post before
            PostReaction::create([
                'user_id' => $user->id,
                'post_id' => $postId,
                'reaction' => $request->reaction,
            ]);
        }

        return response()->json([
            'message' => 'Your reaction has been updated successfully.',
            'reaction' => $request->reaction
        ], 200);
    }

    /**
     * Remove the user's reaction to a post.
     */
    public function removeReaction($postId)
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Check if the post exists
        $post = Post::find($postId);
        if (!$post) {
            return response()->json([
                'error' => 'Post not found.'
            ], 404);
        }

        // Delete the user's reaction to the post
        $reaction = PostReaction::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($reaction) {
            $reaction->delete();
            return response()->json([
                'message' => 'Your reaction has been removed successfully.'
            ], 200);
        }

        return response()->json([
            'error' => 'No reaction found for this post.'
        ], 404);
    }

    public function show($postId)
    {
        $postReact = PostReaction::where('post_id', $postId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$postReact) {
            return response()->json([
                'error' => 'Reaction not found or access denied.'
            ], 404);
        }

        return response()->json([
            'data' => $postReact->reaction
        ]);
    }

    public function likeAndDeslikeByPost($postId)
    {
        $likes = PostReaction::where('post_id', $postId)
            ->where('reaction', 'like')
            ->count();

        $dislikes = PostReaction::where('post_id', $postId)
            ->where('reaction', 'dislike')
            ->count();

        return response()->json([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }




}
