{{-- Paginação padrão do sistema (Paginator::defaultView no AppServiceProvider). --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        <p class="text-sm text-zinc-500">
            Mostrando <span class="font-medium text-zinc-800">{{ $paginator->firstItem() }}</span>
            a <span class="font-medium text-zinc-800">{{ $paginator->lastItem() }}</span>
            de <span class="font-medium text-zinc-800">{{ $paginator->total() }}</span> registros
        </p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg px-3 py-1.5 text-sm text-zinc-300">‹ Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg px-3 py-1.5 text-sm text-zinc-600 hover:bg-zinc-100">‹ Anterior</a>
            @endif

            <span class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 text-sm text-zinc-400">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="rounded-lg px-3 py-1.5 text-sm text-zinc-600 hover:bg-zinc-100">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg px-3 py-1.5 text-sm text-zinc-600 hover:bg-zinc-100">Próxima ›</a>
            @else
                <span class="rounded-lg px-3 py-1.5 text-sm text-zinc-300">Próxima ›</span>
            @endif
        </div>
    </nav>
@endif
