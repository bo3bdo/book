@extends('layouts.app')

@section('title', $book->title . ' - مكتبة الكتب الإلكترونية')
@section('description', Str::limit($book->description, 160))

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('books.index') }}">الكتب</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.show', $book->category) }}">{{ $book->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($book->title, 50) }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Book Cover and Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" class="img-fluid rounded shadow-sm mb-3" style="max-height: 400px;" alt="{{ $book->title }}">
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded mb-3" style="height: 400px;">
                            <i class="fas fa-book fa-5x"></i>
                        </div>
                    @endif
                    
                    @if($book->is_featured)
                        <div class="mb-3">
                            <span class="badge badge-custom">
                                <i class="fas fa-star me-1"></i>
                                كتاب مميز
                            </span>
                        </div>
                    @endif
                      <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('books.view', $book) }}" target="_blank" class="btn btn-primary btn-lg">
                            <i class="fas fa-book-open me-2"></i>
                            قراءة الكتاب
                        </a>
                        
                        <button class="btn btn-outline-primary" onclick="shareBook()">
                            <i class="fas fa-share me-2"></i>
                            مشاركة الكتاب
                        </button>
                        
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                هذا الكتاب متاح للقراءة فقط ولا يمكن تحميله
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Book Stats -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        إحصائيات الكتاب
                    </h6>
                </div>                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-success">{{ number_format($book->view_count) }}</h4>
                        <small class="text-muted">مشاهدة</small>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <div class="rating mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $book->rating ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <p class="mb-0 text-muted">التقييم: {{ $book->rating }}/5</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Book Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title mb-3">{{ $book->title }}</h1>
                    
                    <!-- Book Meta Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong><i class="fas fa-user me-2"></i>المؤلف:</strong></td>
                                    <td>{{ $book->author }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-tag me-2"></i>الفئة:</strong></td>
                                    <td>
                                        <a href="{{ route('categories.show', $book->category) }}" class="text-decoration-none">
                                            {{ $book->category->name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-language me-2"></i>اللغة:</strong></td>
                                    <td>
                                        @switch($book->language)
                                            @case('ar') العربية @break
                                            @case('en') الإنجليزية @break
                                            @case('fr') الفرنسية @break
                                            @case('es') الإسبانية @break
                                            @default أخرى
                                        @endswitch
                                    </td>
                                </tr>
                                @if($book->pages)
                                <tr>
                                    <td><strong><i class="fas fa-file me-2"></i>عدد الصفحات:</strong></td>
                                    <td>{{ number_format($book->pages) }} صفحة</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($book->isbn)
                                <tr>
                                    <td><strong><i class="fas fa-barcode me-2"></i>ISBN:</strong></td>
                                    <td>{{ $book->isbn }}</td>
                                </tr>
                                @endif
                                @if($book->publisher)
                                <tr>
                                    <td><strong><i class="fas fa-building me-2"></i>الناشر:</strong></td>
                                    <td>{{ $book->publisher }}</td>
                                </tr>
                                @endif
                                @if($book->publication_year)
                                <tr>
                                    <td><strong><i class="fas fa-calendar me-2"></i>سنة النشر:</strong></td>
                                    <td>{{ $book->publication_year }}</td>
                                </tr>
                                @endif
                                @if($book->file_size)
                                <tr>
                                    <td><strong><i class="fas fa-hdd me-2"></i>حجم الملف:</strong></td>
                                    <td>{{ $book->getFileSizeFormatted() }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <!-- Book Description -->
                    <div class="mb-4">
                        <h3>وصف الكتاب</h3>
                        <div class="text-muted">
                            {!! nl2br(e($book->description)) !!}
                        </div>
                    </div>
                    
                    <!-- Tags/Keywords (if any) -->
                    <div class="mb-4">
                        <h5>الكلمات المفتاحية</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-secondary">{{ $book->category->name }}</span>
                            <span class="badge bg-secondary">{{ $book->author }}</span>
                            @if($book->publisher)
                                <span class="badge bg-secondary">{{ $book->publisher }}</span>
                            @endif
                            <span class="badge bg-secondary">
                                @switch($book->language)
                                    @case('ar') العربية @break
                                    @case('en') الإنجليزية @break
                                    @case('fr') الفرنسية @break
                                    @case('es') الإسبانية @break
                                    @default أخرى
                                @endswitch
                            </span>
                        </div>
                    </div>
                    
                    <!-- Share Buttons -->
                    <div class="border-top pt-3">
                        <h5>شارك هذا الكتاب</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook me-1"></i>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($book->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fab fa-twitter me-1"></i>
                                Twitter
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($book->title . ' - ' . url()->current()) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp me-1"></i>
                                WhatsApp
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyLink()">
                                <i class="fas fa-link me-1"></i>
                                نسخ الرابط
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Books Section -->
    @if($relatedBooks->count() > 0)
    <div class="mt-5">
        <h2 class="section-title">كتب مشابهة</h2>
        
        <div class="row g-4">
            @foreach($relatedBooks as $relatedBook)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card book-card h-100">
                        <div class="position-relative">
                            @if($relatedBook->cover_image)
                                <img src="{{ Storage::url($relatedBook->cover_image) }}" class="card-img-top book-cover" alt="{{ $relatedBook->title }}">
                            @else
                                <div class="book-cover bg-secondary d-flex align-items-center justify-content-center text-white">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            @endif
                            
                            @if($relatedBook->is_featured)
                                <span class="position-absolute top-0 end-0 badge badge-custom m-2">
                                    <i class="fas fa-star me-1"></i>
                                    مميز
                                </span>
                            @endif
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($relatedBook->title, 50) }}</h5>
                            <p class="book-meta">
                                <i class="fas fa-user me-1"></i> {{ $relatedBook->author }}<br>
                                <i class="fas fa-tag me-1"></i> {{ $relatedBook->category->name }}
                            </p>
                            <p class="book-description text-muted">
                                {{ Str::limit($relatedBook->description, 100) }}
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $relatedBook->rating ? '' : '-o' }}"></i>
                                        @endfor
                                        <span class="ms-1 text-muted">({{ $relatedBook->rating }})</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-download me-1"></i>
                                        {{ number_format($relatedBook->download_count) }}
                                    </small>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('books.show', $relatedBook) }}" class="btn btn-primary">
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
    </div>
    @endif
</div>

@push('scripts')
<script>
function shareBook() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $book->title }}',
            text: '{{ Str::limit($book->description, 100) }}',
            url: window.location.href
        });
    } else {
        copyLink();
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Create a temporary toast notification
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.textContent = 'تم نسخ الرابط بنجاح!';
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }).catch(function(err) {
        console.error('خطأ في نسخ الرابط: ', err);
    });
}
</script>
@endpush
@endsection
