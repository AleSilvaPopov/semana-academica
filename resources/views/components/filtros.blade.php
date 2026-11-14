@props(['busca' => true, 'placeholder' => 'Buscar...'])
{{-- Barra de busca + filtros (GET). Mantém a ordenação atual. --}}
<form method="GET" {{ $attributes->merge(['class' => 'cartao mb-4 flex flex-col gap-3 p-3 lg:flex-row lg:items-end']) }}>
    @if (request('ordem'))
        <input type="hidden" name="ordem" value="{{ request('ordem') }}">
        <input type="hidden" name="direcao" value="{{ request('direcao') }}">
    @endif
    @if ($busca)
        <div class="relative flex-1">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="campo pl-9" aria-label="Buscar">
        </div>
    @endif
    {{ $slot }}
    <div class="flex gap-2">
        <x-button type="submit" variante="secundario" icone="search">Filtrar</x-button>
        @if (collect(request()->except(['page', 'ordem', 'direcao']))->filter(fn ($v) => filled($v))->isNotEmpty())
            <x-button variante="fantasma" :href="url()->current()">Limpar</x-button>
        @endif
    </div>
</form>
