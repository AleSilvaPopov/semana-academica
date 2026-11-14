<x-layouts.admin :titulo="'Certificados · '.$atividade->nome">
    <x-cabecalho :titulo="$atividade->nome" :subtitulo="'Participantes com presença confirmada ('.$presentes->count().') · '.formata_data($atividade->data_realizacao).' · carga horária '.carga_horaria($atividade->duracao_minutos)" :voltar="route('admin.certificados.index')">
        <x-button variante="secundario" icone="ticket" :href="route('admin.inscricoes.show', $atividade)">Registrar presença</x-button>
    </x-cabecalho>

    <form method="GET" class="mb-4 flex max-w-md gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar participante..." class="campo">
        <x-button type="submit" variante="secundario" icone="search" />
    </form>

    <x-table :vazia="$presentes->isEmpty()" vazio="Nenhum participante com presença confirmada nesta atividade.">
        <x-slot:cabecalho><x-th>Participante</x-th><x-th>CPF</x-th><x-th>Tipo</x-th><x-th class="text-right">Certificado</x-th></x-slot:cabecalho>
        @foreach ($presentes as $p)
            <tr>
                <td class="font-medium text-zinc-900">{{ $p->nome_completo }}</td>
                <td class="tabular-nums">{{ mascara_cpf($p->cpf) }}</td>
                <td><x-badge :enum="$p->tipo_participante" /></td>
                <td class="text-right">
                    <x-button variante="secundario" tamanho="sm" icone="printer" target="_blank"
                        :href="route('admin.certificados.emitir', ['atividade' => $atividade, 'participante' => $p->cpf])">Gerar certificado</x-button>
                </td>
            </tr>
        @endforeach
    </x-table>
</x-layouts.admin>
