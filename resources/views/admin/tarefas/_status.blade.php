{{-- Formulário rápido de troca de status (responsável, criador ou presidência). --}}
<form method="POST" action="{{ route('admin.tarefas.status', $tarefa) }}" class="flex items-center gap-2">
    @csrf
    @method('PATCH')
    <select name="status" class="campo w-auto py-1.5 pr-8 text-sm" aria-label="Status da tarefa" onchange="this.form.submit()">
        @foreach (\App\Enums\StatusTarefa::cases() as $s)
            <option value="{{ $s->value }}" @selected($tarefa->status === $s)>{{ $s->rotulo() }}</option>
        @endforeach
    </select>
    <noscript><x-button type="submit" tamanho="sm" variante="secundario">Salvar</x-button></noscript>
</form>
