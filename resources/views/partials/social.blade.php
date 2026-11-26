<div class="d-flex flex-wrap gap-2 social-links">
    @foreach($links as $link)
        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="{{ $link->platform }}" title="{{ $link->platform }}">
            <i class="{{ $link->icon ?: 'fa-solid fa-link' }}"></i>
        </a>
    @endforeach
</div>
