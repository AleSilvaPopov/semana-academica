<x-layouts.admin :titulo="$curso->nome_curso">
    <x-cabecalho :titulo="$curso->nome_curso" :subtitulo="$curso->faculdade" :voltar="route('admin.cursos.index')">
        @can('cadastros-globais')
            <x-button variante="secundario" icone="pencil" :href="route('admin.cursos.edit', $curso)">Editar</x-button>
            <x-excluir :action="route('admin.cursos.destroy', $curso)" :descricao="'o curso '.$curso->nome_curso" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <x-secao :titulo="'Integrantes do curso ('.$curso->integrantes->count().')'">
        @if ($curso->integrantes->isEmpty())
            <p class="text-sm text-zinc-500">Nenhum integrante vinculado a este curso.</p>
        @else
            <ul class="divide-y divide-zinc-100">
                @foreach ($curso->integrantes as $i)
                    <li class="flex items-center justify-between gap-3 py-2.5">
                        <div>
                            <a href="{{ route('admin.integrantes.show', $i) }}" class="link">{{ $i->nome_completo }}</a>
                            <p class="text-xs text-zinc-500">RGA {{ $i->rga }} · {{ $i->funcao }}</p>
                        </div>
                        @if ($i->coordenadoria)
                            <x-badge cor="blue">{{ $i->coordenadoria->nome }} · {{ $i->coordenadoria->comissao?->edicao }}ª ed.</x-badge>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </x-secao>
</x-layouts.admin>
