@extends('layouts.app')

@section('title', 'جميع الكتب - مكتبة الكتب الإلكترونية')
@section('description', 'تصفح مجموعة شاملة من الكتب الإلكترونية المجانية في مختلف المجالات')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        تصفية النتائج
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('books.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="ابحث عن كتاب...">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">الفئة</label>
                            <select name="category" class="form-select">
                                <option value="">جميع الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->books_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Language Filter -->
                        <div class="mb-3">
                            <label class="form-label">اللغة</label>
                            <select name="language" class="form-select">
                                <option value="">جميع اللغات</option>
                                <option value="ar" {{ request('language') == 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>الإنجليزية</option>
                                <option value="fr" {{ request('language') == 'fr' ? 'selected' : '' }}>الفرنسية</option>
                                <option value="es" {{ request('language') == 'es' ? 'selected' : '' }}>الإسبانية</option>
                                <option value="other" {{ request('language') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                        </div>
                        
                        <!-- Sort -->
                        <div class="mb-3">
                            <label class="form-label">ترتيب حسب</label>                            <select name="sort" class="form-select">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>الأحدث</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>العنوان</option>
                                <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>المؤلف</option>
                                <option value="view_count" {{ request('sort') == 'view_count' ? 'selected' : '' }}>الأكثر مشاهدة</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>التقييم</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">الاتجاه</label>
                            <select name="direction" class="form-select">
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>
                                تطبيق التصفية
                            </button>
                            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                إزالة التصفية
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Categories List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        الفئات الشائعة
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($categories->take(10) as $category)
                        <a href="{{ route('categories.show', $category) }}" class="d-flex justify-content-between align-items-center text-decoration-none mb-2">
                            <span>{{ $category->name }}</span>
                            <span class="badge bg-secondary">{{ $category->books_count }}</span>
                        </a>
                    @endforeach
                    
                    @if($categories->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-primary">
                                عرض جميع الفئات
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        @if(request('search'))
                            نتائج البحث عن "{{ request('search') }}"
                        @elseif(request('category'))
                            كتب فئة "{{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}"
                        @else
                            جميع الكتب
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        تم العثور على {{ number_format($books->total()) }} كتاب
                    </p>
                </div>
                
                <!-- View Toggle -->
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="view" id="grid-view" checked>
                    <label class="btn btn-outline-primary" for="grid-view">
                        <i class="fas fa-th"></i>
                    </label>
                    
                    <input type="radio" class="btn-check" name="view" id="list-view">
                    <label class="btn btn-outline-primary" for="list-view">
                        <i class="fas fa-list"></i>
                    </label>
                </div>
            </div>
            
            @if($books->count() > 0)
                <!-- Books Grid -->
                <div id="books-grid" class="row g-4">
                    @foreach($books as $book)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card book-card h-100">
                                <div class="position-relative">
                                    @if($book->cover_image)
                                        <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top book-cover" alt="{{ $book->title }}">
                                    @else
                                        <div class="book-cover bg-secondary d-flex align-items-center justify-content-center text-white">
                                            <i class="fas fa-book fa-3x"></i>
                                        </div>
                                    @endif
                                    
                                    @if($book->is_featured)
                                        <span class="position-absolute top-0 end-0 badge badge-custom m-2">
                                            <i class="fas fa-star me-1"></i>
                                            مميز
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ Str::limit($book->title, 50) }}</h5>
                                    <p class="book-meta">
                                        <i class="fas fa-user me-1"></i> {{ $book->author }}<br>
                                        <i class="fas fa-tag me-1"></i> {{ $book->category->name }}<br>
                                        <i class="fas fa-language me-1"></i> 
                                        @switch($book->language)
                                            @case('ar') العربية @break
                                            @case('en') الإنجليزية @break
                                            @case('fr') الفرنسية @break
                                            @case('es') الإسبانية @break
                                            @default أخرى
                                        @endswitch
                                    </p>
                                    <p class="book-description text-muted">
                                        {{ Str::limit($book->description, 100) }}
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $book->rating ? '' : '-o' }}"></i>
                                                @endfor
                                                <span class="ms-1 text-muted">({{ $book->rating }})</span>
                                            </div>                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ number_format($book->view_count) }} مشاهدة
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('books.show', $book) }}" class="btn btn-primary">
                                                <i class="fas fa-eye me-1"></i>
                                                عرض التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Books List (Hidden by default) -->
                <div id="books-list" class="d-none">
                    @foreach($books as $book)
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-2">
                                    @if($book->cover_image)
                                        <img src="{{ Storage::url($book->cover_image) }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $book->title }}">
                                    @else
                                        <div class="h-100 bg-secondary d-flex align-items-center justify-content-center text-white rounded-start">
                                            <i class="fas fa-book fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-10">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title">{{ $book->title }}</h5>
                                                <p class="book-meta">
                                                    <i class="fas fa-user me-1"></i> {{ $book->author }} |
                                                    <i class="fas fa-tag me-1"></i> {{ $book->category->name }} |
                                                    <i class="fas fa-language me-1"></i> 
                                                    @switch($book->language)
                                                        @case('ar') العربية @break
                                                        @case('en') الإنجليزية @break
                                                        @case('fr') الفرنسية @break
                                                        @case('es') الإسبانية @break
                                                        @default أخرى
                                                    @endswitch
                                                </p>
                                                <p class="card-text">{{ Str::limit($book->description, 200) }}</p>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star{{ $i <= $book->rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                        <span class="ms-1 text-muted">({{ $book->rating }})</span>
                                                    </div>                                                    <small class="text-muted">
                                                        <i class="fas fa-eye me-1"></i>
                                                        {{ number_format($book->view_count) }} مشاهدة
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="ms-3">
                                                @if($book->is_featured)
                                                    <span class="badge badge-custom mb-2 d-block">
                                                        <i class="fas fa-star me-1"></i>
                                                        مميز
                                                    </span>
                                                @endif
                                                <a href="{{ route('books.show', $book) }}" class="btn btn-primary">
                                                    <i class="fas fa-eye me-1"></i>
                                                    عرض التفاصيل
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $books->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search fa-4x text-muted"></i>
                    </div>
                    <h3 class="text-muted">لا توجد نتائج</h3>
                    <p class="text-muted">لم يتم العثور على كتب تطابق معايير البحث الخاصة بك.</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-1"></i>
                        عرض جميع الكتب
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const booksGrid = document.getElementById('books-grid');
    const booksList = document.getElementById('books-list');
    
    listView.addEventListener('change', function() {
        if (this.checked) {
            booksGrid.classList.add('d-none');
            booksList.classList.remove('d-none');
        }
    });
    
    gridView.addEventListener('change', function() {
        if (this.checked) {
            booksList.classList.add('d-none');
            booksGrid.classList.remove('d-none');
        }
    });
});
</script>
@endpush
@endsection
