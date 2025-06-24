<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category')->where('is_active', true);

        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // الفلترة حسب الفئة
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // الفلترة حسب اللغة
        if ($request->has('language') && $request->language) {
            $query->where('language', $request->language);
        }

        // الترتيب
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $validSorts = ['created_at', 'title', 'author', 'view_count', 'rating'];
        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortDirection);

        $books = $query->paginate(12);
        $categories = Category::where('is_active', true)->withCount('books')->get();
        $featuredBooks = Book::where('is_active', true)->where('is_featured', true)->limit(6)->get();

        return view('books.index', compact('books', 'categories', 'featuredBooks'));
    }

    public function show(Book $book)
    {
        if (!$book->is_active) {
            abort(404);
        }

        // زيادة عدد المشاهدات
        $book->incrementViewCount();

        // الكتب المشابهة
        $relatedBooks = Book::where('is_active', true)
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function view(Book $book)
    {
        if (!$book->is_active) {
            abort(404);
        }

        // زيادة عدد المشاهدات
        $book->incrementViewCount();

        // عرض صفحة القارئ باستخدام PDF.js
        return view('books.reader_pdfjs', compact('book'));
    }

    public function serve(Book $book)
    {
        if (!$book->is_active) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $book->pdf_file);

        if (!file_exists($filePath)) {
            abort(404, 'الملف غير موجود');
        }

        // التحقق من أن الطلب جاء من صفحة القارئ
        $referer = request()->header('referer');
        $userAgent = request()->header('User-Agent');

        // منع أدوات التحميل المعروفة
        $blockedAgents = ['wget', 'curl', 'download', 'spider', 'bot'];
        foreach ($blockedAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                abort(403, 'هذا النوع من البرامج غير مسموح');
            }
        }

        // منع الطلبات التي تحتوي على معلمات التحميل
        if (request()->has('download') || request()->has('save')) {
            abort(403, 'التحميل غير مسموح');
        }

        // إرجاع ملف PDF مع headers محسنة للأمان
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="view.pdf"',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "frame-ancestors 'self'; script-src 'none';",
            'X-Download-Options' => 'noopen',
            'X-Permitted-Cross-Domain-Policies' => 'none',
        ];

        return response()->file($filePath, $headers);
    }
}
