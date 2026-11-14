@props(['rotulo'])
{{-- Item de lista de definição (telas de detalhes). --}}
<div {{ $attributes->merge(['class' => 'py-2.5 sm:grid sm:grid-cols-3 sm:gap-4']) }}>
    <dt class="text-sm font-medium text-zinc-500">{{ $rotulo }}</dt>
    <dd class="mt-1 text-sm text-zinc-900 sm:col-span-2 sm:mt-0">{{ $slot }}</dd>
</div>
