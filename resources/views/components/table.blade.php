@props(['vazio' => 'Nenhum registro encontrado.', 'vazia' => false])
{{-- Tabela responsiva: slot "cabecalho" (linhas <th>) + slot padrão (linhas <tr>) + slot opcional "rodape". --}}
<div {{ $attributes->merge(['class' => 'cartao overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="tabela">
            <thead>
                <tr>{{ $cabecalho }}</tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-superficie">
                @if ($vazia)
                    <tr>
                        <td colspan="99" class="py-10 text-center text-sm text-zinc-500">{{ $vazio }}</td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
            @isset($rodape)
                <tfoot class="border-t-2 border-zinc-200 bg-zinc-50 font-semibold">{{ $rodape }}</tfoot>
            @endisset
        </table>
    </div>
    @isset($paginacao)
        <div class="border-t border-zinc-100 px-4 py-3">{{ $paginacao }}</div>
    @endisset
</div>
