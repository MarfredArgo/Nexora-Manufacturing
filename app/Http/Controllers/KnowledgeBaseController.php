<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class KnowledgeBaseController extends Controller
{
    public function store(Request $request)
    {
        // validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'target_module' => 'nullable|string|max:100',
            'author_name' => 'required|string|max:100',
        ]);

        // save to database
        Article::create($validated);

        // redirect back with success message
        return redirect()->back()->with('success', 'Article published successfully!');
    }
   public function knowledgeBase()
{
    $articles = Article::all(); // returns objects with properties
    return view('service.knowledgebase', compact('articles'));
}


}


