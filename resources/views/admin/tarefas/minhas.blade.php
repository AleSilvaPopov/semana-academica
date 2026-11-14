<x-layouts.admin titulo="Minhas tarefas">
    <x-cabecalho titulo="Minhas tarefas" subtitulo="Tarefas em que você é responsável (todas as edições). Altere o status direto na lista." />

    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach (\App\Enums\StatusTarefa::cases() as $s)
            <a href="{{ route('admin.minhas-tarefas', ['status' => $s->value]) }}"
                class="cartao flex items-center justify-between p-4 hover:border-brand-300 {{ request('status') === $s->value ? 'border-brand-400 ring-2 ring-brand-100' : '' }}">
                <x-badge :enum="$s" />
                <span class="text-2xl font-semibold tabular-nums text-zinc-900">{{ $resumo[$s->value] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <x-filtros :busca="false">
        <x-filtro-select name="status" :opcoes="$statusOpcoes" todos="Todos os status" class="lg:w-56" />
    </x-filtros>

    <x-table :vazia="$tarefas->isEmpty()" vazio="Você não é responsável por nenhuma tarefa.">
        <x-slot:cabecalho><x-th>Tarefa</x-th><x-th>Criada por</x-th><x-th>Prazo</x-th><x-th>Status</x-th></x-slot:cabecalho>
        @foreach ($tarefas as $tarefa)
            <tr @class(['bg-red-50/70 hover:bg-red-50' => $tarefa->atrasada])>
                <td class="max-w-lg">
                    <a href="{{ route('admin.tarefas.show', $tarefa) }}" class="link">{{ $tarefa->descricao }}</a>
                    @if ($tarefa->atrasada)<x-badge cor="red" class="ml-1">Atrasada</x-badge>@endif
                </td>
                <td class="text-zinc-600">{{ $tarefa->criador?->nome_completo }}</td>
                <td class="whitespace-nowrap {{ $tarefa->atrasada ? 'font-semibold text-red-700' : '' }}">{{ formata_data($tarefa->prazo_final) }}</td>
                <td>@include('admin.tarefas._status', ['tarefa' => $tarefa])</td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $tarefas->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
