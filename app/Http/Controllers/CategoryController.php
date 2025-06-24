<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount('books')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category, Request $request)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $query = $category->books()->where('is_active', true);

        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $validSorts = ['created_at', 'title', 'author', 'download_count', 'view_count', 'rating'];
        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortDirection);

        $books = $query->paginate(12);

        return view('categories.show', compact('category', 'books'));
    }
}
