<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // الكتب المميزة
        $featuredBooks = Book::where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->limit(8)
            ->get();

        // أحدث الكتب
        $latestBooks = Book::where('is_active', true)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // الكتب الأكثر مشاهدة
        $popularBooks = Book::where('is_active', true)
            ->with('category')
            ->orderBy('view_count', 'desc')
            ->limit(8)
            ->get();

        // الفئات النشطة
        $categories = Category::where('is_active', true)
            ->withCount('books')
            ->orderBy('name')
            ->limit(12)
            ->get();

        // إحصائيات
        $stats = [
            'total_books' => Book::where('is_active', true)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'total_views' => Book::where('is_active', true)->sum('view_count'),
            'total_authors' => Book::where('is_active', true)->distinct('author')->count(),
        ];

        return view('home', compact('featuredBooks', 'latestBooks', 'popularBooks', 'categories', 'stats'));
    }
}
