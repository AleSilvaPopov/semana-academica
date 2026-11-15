<div class="grid gap-6 lg:grid-cols-2">
    <x-secao titulo="Participante e foto">
        <div class="space-y-4">
            @if ($ministrante->exists)
                <x-input name="participante_exibicao" label="Participante" :value="$ministrante->participante->rotulo" disabled
                    ajuda="O ministrante é o próprio participante (mesmo CPF); não pode ser trocado." />
            @else
                <x-select name="cpf" label="Participante" :opcoes="$participantes" :selecionado="$ministrante->cpf" obrigatorio
                    vazio="Selecione um participante que ainda não é ministrante..."
                    ajuda="Não encontrou? Cadastre primeiro o participante." />
            @endif

            <div x-data="{ previa: null }">
                <span class="rotulo">Imagem de divulgação</span>
                <div class="flex items-center gap-4">
                    <template x-if="previa"><img :src="previa" class="size-20 rounded-full object-cover ring-2 ring-superficie" alt="Prévia"></template>
                    <template x-if="!previa">
                        <x-avatar :nome="$ministrante->participante?->nome_completo ?? 'Novo ministrante'" :url="$ministrante->imagem_url" tamanho="size-20" />
                    </template>
                    <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp"
                        @change="previa = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                        class="block w-full text-sm text-zinc-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                </div>
                <p class="mt-1 text-xs text-zinc-500">JPG, PNG ou WEBP de até 2 MB. Fica em storage/app/public/ministrantes.</p>
                @error('imagem')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                @if ($ministrante->exists && $ministrante->imagem_divulgacao)
                    <label class="mt-2 flex items-center gap-2 text-sm text-zinc-600">
                        <input type="checkbox" name="remover_imagem" value="1" class="rounded border-zinc-300 text-brand-600"> Remover a imagem atual
                        <code class="text-xs text-zinc-400">({{ $ministrante->imagem_divulgacao }})</code>
                    </label>
                @endif
            </div>
        </div>
    </x-secao>
    <x-secao titulo="Certificados e titulações" descricao="Atributo multivalorado (ministrante_certificado)">
        <x-lista-dinamica name="certificados" label="Certificados" :itens="$ministrante->exists ? $ministrante->certificados->pluck('certificado')->all() : []"
            placeholder="Ex.: Doutorado em Ciência da Computação - UNICAMP" maxlength="200" textoAdicionar="Adicionar certificado" />
    </x-secao>
</div>
