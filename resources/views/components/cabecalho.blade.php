@props(['titulo', 'subtitulo' => null, 'voltar' => null])
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        @if ($voltar)
            <a href="{{ $voltar }}" class="mb-1 inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-800">
                <x-icon name="arrow-left" class="size-4" /> Voltar
            </a>
        @endif
        <h1 class="text-2xl font-semibold text-zinc-950">{{ $titulo }}</h1>
        @if ($subtitulo)
            <p class="mt-1 text-sm text-zinc-500">{{ $subtitulo }}</p>
        @endif
    </div>
    @if (trim($slot))
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
