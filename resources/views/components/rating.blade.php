@props(['rating', 'showText' => true])

<div class="rating">
    @for($i = 1; $i <= 5; $i++)
        <i class="fas fa-star {{ $i <= $rating ? '' : 'empty' }}"></i>
    @endfor
    @if($showText)
        <span class="ms-1 text-muted">({{ number_format($rating, 1) }})</span>
    @endif
</div>
