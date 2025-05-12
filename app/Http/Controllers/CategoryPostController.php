<?php

namespace App\Http\Controllers;

use App\Models\CategoryPost;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryPostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:category_posts,name'
        ]);

        $category = CategoryPost::create([
            'name' => $validated['name']
        ]);

        return response()->json($category, 201);
    }

    function getAll()
    {
        return CategoryPost::all();
    }


}
