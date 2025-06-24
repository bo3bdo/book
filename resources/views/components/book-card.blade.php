@props(['book'])

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
            <i class="fas fa-tag me-1"></i> {{ $book->category->name }}
            @if(isset($showLanguage) && $showLanguage)
                <br><i class="fas fa-language me-1"></i> 
                @switch($book->language)
                    @case('ar') العربية @break
                    @case('en') الإنجليزية @break
                    @case('fr') الفرنسية @break
                    @case('es') الإسبانية @break
                    @default أخرى
                @endswitch
            @endif
        </p>
        <p class="book-description text-muted">
            {{ Str::limit($book->description, 100) }}
        </p>
        
        <div class="mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <x-rating :rating="$book->rating" />
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
