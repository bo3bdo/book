@extends('layouts.app')

@section('title', 'مكتبة الكتب الإلكترونية - الصفحة الرئيسية')
@section('description', 'اكتشف آلاف الكتب الإلكترونية المجانية بصيغة PDF في مختلف المجالات. اقرأ الكتب مجاناً.')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    اكتشف عالم الكتب الإلكترونية
                </h1>
                <p class="lead mb-4">
                    مكتبة شاملة تضم آلاف الكتب الإلكترونية في مختلف المجالات. اقرأ الكتب مجاناً بصيغة PDF.
                </p>
                
                <!-- Search Box -->
                <form method="GET" action="{{ route('search') }}" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input type="text" name="search" class="form-control search-box text-white" 
                               placeholder="ابحث عن كتاب، مؤلف، أو موضوع..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="d-flex gap-3">
                    <a href="{{ route('books.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-book me-2"></i>
                        تصفح الكتب
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-list me-2"></i>
                        الفئات
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                <div class="row g-3">
                    @foreach($stats as $key => $value)
                        <div class="col-6">
                            <div class="stats-card">
                                <h3 class="fw-bold">{{ number_format($value) }}</h3>
                                <p class="mb-0">
                                    @switch($key)
                                        @case('total_books')
                                            إجمالي الكتب
                                            @break
                                        @case('total_categories')
                                            إجمالي الفئات
                                            @break
                                        @case('total_views')
                                            إجمالي المشاهدات
                                            @break
                                        @case('total_authors')
                                            إجمالي المؤلفين
                                            @break
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Books Section -->
@if($featuredBooks->count() > 0)
<section class="py-5">
    <div class="container">
        <h2 class="section-title">الكتب المميزة</h2>
        
        <div class="row g-4">
            @foreach($featuredBooks as $book)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card book-card h-100">
                        <div class="position-relative">
                            @if($book->cover_image)
                                <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top book-cover" alt="{{ $book->title }}">
                            @else
                                <div class="book-cover bg-gradient d-flex align-items-center justify-content-center text-white">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            @endif
                            <span class="position-absolute top-0 end-0 badge badge-custom m-2">
                                <i class="fas fa-star me-1"></i>
                                مميز
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($book->title, 50) }}</h5>
                            <p class="book-meta">
                                <i class="fas fa-user me-1"></i> {{ $book->author }}<br>
                                <i class="fas fa-tag me-1"></i> {{ $book->category->name }}
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
        
        <div class="text-center mt-4">
            <a href="{{ route('books.index') }}?featured=1" class="btn btn-outline-primary btn-lg">
                عرض جميع الكتب المميزة
                <i class="fas fa-arrow-left ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">الفئات الشائعة</h2>
        
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <a href="{{ route('categories.show', $category) }}" class="category-card d-block text-decoration-none">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" class="rounded-circle mb-2" width="60" height="60" alt="{{ $category->name }}">
                        @else
                            <i class="fas fa-folder fa-3x mb-2"></i>
                        @endif
                        <h6 class="fw-bold">{{ $category->name }}</h6>
                        <p class="mb-0 opacity-75">{{ $category->books_count }} كتاب</p>
                    </a>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary btn-lg">
                عرض جميع الفئات
                <i class="fas fa-arrow-left ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Latest Books Section -->
@if($latestBooks->count() > 0)
<section class="py-5">
    <div class="container">
        <h2 class="section-title">أحدث الكتب</h2>
        
        <div class="row g-4">
            @foreach($latestBooks as $book)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card book-card h-100">
                        @if($book->cover_image)
                            <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top book-cover" alt="{{ $book->title }}">
                        @else
                            <div class="book-cover bg-secondary d-flex align-items-center justify-content-center text-white">
                                <i class="fas fa-book fa-3x"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($book->title, 50) }}</h5>
                            <p class="book-meta">
                                <i class="fas fa-user me-1"></i> {{ $book->author }}<br>
                                <i class="fas fa-tag me-1"></i> {{ $book->category->name }}
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
        
        <div class="text-center mt-4">
            <a href="{{ route('books.index') }}" class="btn btn-outline-primary btn-lg">
                عرض جميع الكتب
                <i class="fas fa-arrow-left ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Popular Books Section -->
@if($popularBooks->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">الكتب الأكثر مشاهدة</h2>
        
        <div class="row g-4">
            @foreach($popularBooks as $book)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card book-card h-100">
                        <div class="position-relative">
                            @if($book->cover_image)
                                <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top book-cover" alt="{{ $book->title }}">
                            @else
                                <div class="book-cover bg-warning d-flex align-items-center justify-content-center text-white">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            @endif
                            <span class="position-absolute top-0 end-0 badge bg-success m-2">
                                <i class="fas fa-fire me-1"></i>
                                شائع
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($book->title, 50) }}</h5>
                            <p class="book-meta">
                                <i class="fas fa-user me-1"></i> {{ $book->author }}<br>
                                <i class="fas fa-tag me-1"></i> {{ $book->category->name }}
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
                                    <small class="text-success fw-bold">
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
        
        <div class="text-center mt-4">
            <a href="{{ route('books.index') }}?sort=view_count&direction=desc" class="btn btn-outline-primary btn-lg">
                عرض الكتب الأكثر مشاهدة
                <i class="fas fa-arrow-left ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif
@endsection
