@if ($paginator->hasPages())
<div class="pagination">
    <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }} · {{ $paginator->total() }} data</span>
    <div>
        @foreach ($elements as $element)
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>
</div>
@endif
