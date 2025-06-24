@props(['category'])

<a href="{{ route('categories.show', $category) }}" class="category-card d-block text-decoration-none">
    @if($category->image)
        <img src="{{ Storage::url($category->image) }}" class="rounded-circle mb-2" width="60" height="60" alt="{{ $category->name }}">
    @else
        <i class="fas fa-folder fa-3x mb-2"></i>
    @endif
    <h6 class="fw-bold">{{ $category->name }}</h6>
    <p class="mb-0 opacity-75">{{ $category->books_count }} كتاب</p>
</a>
