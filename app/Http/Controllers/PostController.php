<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\CategoryPost;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:category_posts,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $admin = Auth::user();

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $request->input('category_id'),
            'created_by' => $admin->id,
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store('posts', 'public');

            PostImage::create([
                'post_id' => $post->id,
                'image_path' => $path,
            ]);
        }

        $post->load('category', 'user', 'images');

//         ✅ Send OneSignal Notification
        $this->sendOneSignalNotification(
            'عنوان جديد: ' . $post->title,  // Arabic heading
            'محتوى المقال: ' . substr($post->content, 0, 100) . '...',  // Arabic content
            env('FRONTEND_URL') . '/post/detail/' . $post->id // URL to the post
        );


        return response()->json($post);
    }


    public function delete($id)
    {
        $post = Post::with('images')->findOrFail($id);

        // Delete associated images from storage and DB
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        // Optionally: Delete comments related to the post (if cascade not set)
        Comment::where('post_id', $post->id)->delete();

        // Delete the post
        $post->delete();

        return response()->json(['message' => 'تم حذف الخبر بنجاح.'], 200);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:category_posts,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'removed_images' => 'array',
            'removed_images.*' => 'integer|exists:post_images,id',
        ]);

        $post = Post::findOrFail($id);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
        ]);

        // ✅ Delete removed images
        if ($request->has('removed_images')) {
            foreach ($request->removed_images as $imageId) {
                $image = PostImage::find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // ✅ Save new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json($post->load('images'));
    }


    function getAll()
    {
        //        return Post::with('category', 'images')->paginate(6); // or any number per page
        return Post::with('category', 'images')->paginate(20);
    }


    function search($id)
    {
        return Post::find($id)
            ->load([
                'category',
                'user',
                'images',
                'comments' => function ($query) {
                    $query->orderBy('created_at', 'desc') // Order comments by created_at in descending order
                    ->with('user'); // Eager load the user associated with each comment
                }
            ]);

    }


    public function findByCategory($name)
    {
        return Post::with(['images', 'user', 'comments', 'category'])
            ->whereHas('category', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->paginate(20);  // Add pagination
    }



    public function sendOneSignalNotification($heading, $content, $url = null)
    {
        $plainContent = strip_tags($content);

        // Ensure proper UTF-8 encoding for the content
        $heading = mb_convert_encoding($heading, 'UTF-8', 'auto');
        $plainContent = mb_convert_encoding($plainContent, 'UTF-8', 'auto');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('ONESIGNAL_API_KEY'),
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => env('ONESIGNAL_APP_ID'),
                'included_segments' => ['All'],  // Send to all subscribers
                'headings' => [
                    'ar' => $heading,  // Arabic heading
                    'en' => 'New Post: ' . $heading  // English heading
                ],
                'contents' => [
                    'ar' => $plainContent,  // Arabic content
                    'en' => 'New post content: ' . $plainContent  // English content
                ],
                'url' => $url,
            ]);

            Log::info('OneSignal response: ' . json_encode($response->body()));
            return $response->body();

        } catch (\Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
        }
    }


}
