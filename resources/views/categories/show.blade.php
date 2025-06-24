@extends('layouts.app')

@section('title', $category->name . ' - فئات الكتب')
@section('description', $category->description ?: 'تصفح جميع الكتب في فئة ' . $category->name)

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">الفئات</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>
    
    <!-- Category Header -->
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                @if($category->image)
                    <img src="{{ Storage::url($category->image) }}" class="rounded-circle me-3" width="80" height="80" alt="{{ $category->name }}">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-folder fa-2x"></i>
                    </div>
                @endif
                
                <div>
                    <h1 class="mb-1">{{ $category->name }}</h1>
                    <p class="text-muted mb-0">{{ number_format($books->total()) }} كتاب متاح</p>
                </div>
            </div>
            
            @if($category->description)
                <p class="lead text-muted">{{ $category->description }}</p>
            @endif
        </div>
        
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <a href="{{ route('books.index', ['category' => $category->slug]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i>
                    عرض مفصل
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-sort me-1"></i>
                        ترتيب
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}">الأحدث</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => 'asc']) }}">العنوان (أ-ي)</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'author', 'direction' => 'asc']) }}">المؤلف (أ-ي)</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'download_count', 'direction' => 'desc']) }}">الأكثر تحميلاً</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'rating', 'direction' => 'desc']) }}">الأعلى تقييماً</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search in Category -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <form method="GET" action="{{ route('categories.show', $category) }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="ابحث في {{ $category->name }}..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @if(request()->has('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request()->has('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
            </form>
        </div>
        
        @if(request('search'))
            <div class="col-lg-6">
                <div class="alert alert-info mb-0 d-flex justify-content-between align-items-center">
                    <span>نتائج البحث عن: <strong>"{{ request('search') }}"</strong></span>
                    <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-times me-1"></i>
                        إزالة البحث
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    @if($books->count() > 0)
        <!-- Books Grid -->
        <div class="row g-4">
            @foreach($books as $book)
                <div class="col-xl-3 col-lg-4 col-md-6">
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
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-download me-1"></i>
                                        {{ number_format($book->download_count) }}
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
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $books->appends(request()->query())->links() }}
        </div>
    @else
        <!-- No Books -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-4x text-muted"></i>
            </div>
            @if(request('search'))
                <h3 class="text-muted">لا توجد نتائج</h3>
                <p class="text-muted">لم يتم العثور على كتب تطابق كلمة البحث "{{ request('search') }}" في فئة {{ $category->name }}.</p>
                <a href="{{ route('categories.show', $category) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-1"></i>
                    عرض جميع كتب الفئة
                </a>
            @else
                <h3 class="text-muted">لا توجد كتب</h3>
                <p class="text-muted">لا توجد كتب متاحة في فئة {{ $category->name }} حالياً.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>
                        تصفح الفئات الأخرى
                    </a>
                    <a href="{{ route('books.index') }}" class="btn btn-primary">
                        <i class="fas fa-book me-1"></i>
                        تصفح جميع الكتب
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
