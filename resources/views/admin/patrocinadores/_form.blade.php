<x-secao titulo="Dados do patrocinador">
    <div class="space-y-4">
        @if ($patrocinador->exists)
            <x-input name="nome_exibicao" label="Nome" :value="$patrocinador->nome" disabled
                ajuda="O nome é a chave do patrocinador e não pode ser alterado (os patrocínios apontam para ele)." />
        @else
            <x-input name="nome" label="Nome" obrigatorio maxlength="120" autofocus />
        @endif
        <x-textarea name="observacoes" label="Observações" :value="$patrocinador->observacoes" rows="4" />
    </div>
</x-secao>
