@extends('layouts.app')

@section('title', 'جميع الفئات - مكتبة الكتب الإلكترونية')
@section('description', 'تصفح جميع فئات الكتب المتاحة في مكتبتنا الإلكترونية')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="section-title">جميع الفئات</h1>
        <p class="lead text-muted">اختر الفئة التي تهمك واكتشف الكتب المتاحة</p>
    </div>
    
    @if($categories->count() > 0)
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">
                        <div class="card category-card h-100 text-center">
                            @if($category->image)
                                <div class="text-center mb-3">
                                    <img src="{{ Storage::url($category->image) }}" class="rounded-circle" width="80" height="80" alt="{{ $category->name }}">
                                </div>
                            @else
                                <div class="text-center mb-3">
                                    <i class="fas fa-folder fa-4x mb-2 opacity-75"></i>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $category->name }}</h5>
                                
                                @if($category->description)
                                    <p class="card-text opacity-75 small flex-grow-1">
                                        {{ Str::limit($category->description, 80) }}
                                    </p>
                                @endif
                                
                                <div class="mt-auto">
                                    <span class="badge bg-white text-dark">
                                        {{ number_format($category->books_count) }} كتاب
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        
        <!-- Statistics -->
        <div class="row mt-5">
            <div class="col-md-4 text-center">
                <div class="stats-card">
                    <h3 class="fw-bold">{{ number_format($categories->count()) }}</h3>
                    <p class="mb-0">إجمالي الفئات</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="stats-card">
                    <h3 class="fw-bold">{{ number_format($categories->sum('books_count')) }}</h3>
                    <p class="mb-0">إجمالي الكتب</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="stats-card">
                    <h3 class="fw-bold">{{ number_format($categories->where('books_count', '>', 0)->count()) }}</h3>
                    <p class="mb-0">فئات تحتوي على كتب</p>
                </div>
            </div>
        </div>
    @else
        <!-- No Categories -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-folder-open fa-4x text-muted"></i>
            </div>
            <h3 class="text-muted">لا توجد فئات متاحة</h3>
            <p class="text-muted">لم يتم إضافة أي فئات بعد.</p>
            <a href="{{ route('books.index') }}" class="btn btn-primary">
                <i class="fas fa-book me-1"></i>
                تصفح الكتب
            </a>
        </div>
    @endif
</div>
@endsection
