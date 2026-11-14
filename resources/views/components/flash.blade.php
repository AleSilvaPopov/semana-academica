{{-- Mensagens flash padrão: session('sucesso'), session('erro'), session('aviso') e erros de validação. --}}
<div class="space-y-3">
    @if (session('sucesso'))
        <x-alert tipo="sucesso" fechavel>{{ session('sucesso') }}</x-alert>
    @endif
    @if (session('erro'))
        <x-alert tipo="erro" fechavel>{{ session('erro') }}</x-alert>
    @endif
    @if (session('aviso'))
        <x-alert tipo="aviso" fechavel>{{ session('aviso') }}</x-alert>
    @endif
    @if ($errors->any() && ! session('erro'))
        <x-alert tipo="erro" titulo="Verifique os campos destacados" fechavel>
            <ul class="mt-1 list-inside list-disc space-y-0.5">
                @foreach (array_slice($errors->all(), 0, 6) as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif
</div>
