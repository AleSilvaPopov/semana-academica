<x-layouts.simples titulo="Status do banco" class="max-w-4xl">
    <div class="cartao overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-zinc-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-semibold text-zinc-900">Status da aplicação e do banco</h1>
                <p class="text-sm text-zinc-500">Conexão Laravel → MySQL ({{ $conexao['driver'] ?? '?' }} em {{ $conexao['host'] ?? '?' }}:{{ $conexao['port'] ?? '?' }})</p>
            </div>
            @if ($erro)
                <x-badge cor="red">Sem conexão</x-badge>
            @else
                <x-badge cor="green">Conectado</x-badge>
            @endif
        </div>

        @if ($erro)
            <div class="p-6">
                <x-alert tipo="erro" titulo="Não foi possível conectar ao banco">
                    <code class="text-xs break-all">{{ $erro }}</code>
                    <p class="mt-2">Confira DB_HOST, DB_DATABASE, DB_USERNAME e DB_PASSWORD no arquivo .env e se o script.sql foi importado.</p>
                </x-alert>
            </div>
        @else
            <dl class="grid grid-cols-2 gap-px bg-zinc-100 sm:grid-cols-4">
                @foreach (['Banco' => $info['banco'], 'Versão do MySQL' => $info['versao'], 'Collation' => $info['collation'], 'Total de registros' => number_format($total, 0, ',', '.')] as $rotulo => $valor)
                    <div class="bg-superficie px-6 py-4">
                        <dt class="text-xs text-zinc-500">{{ $rotulo }}</dt>
                        <dd class="mt-1 font-semibold break-all text-zinc-900">{{ $valor }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="grid gap-6 p-6 md:grid-cols-[1.4fr_1fr]">
                <div>
                    <h2 class="mb-2 text-sm font-semibold text-zinc-900">Registros por tabela ({{ count($contagens) }} tabelas)</h2>
                    <div class="overflow-hidden rounded-lg border border-zinc-200">
                        <table class="tabela">
                            <thead><tr><th>#</th><th>Tabela</th><th class="text-right">Registros</th></tr></thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach ($contagens as $tabela => $qtd)
                                    <tr>
                                        <td class="text-zinc-400">{{ $loop->iteration }}</td>
                                        <td><code class="text-xs">{{ $tabela }}</code></td>
                                        <td class="text-right font-medium tabular-nums">{{ $qtd }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="space-y-5">
                    <div>
                        <h2 class="mb-2 text-sm font-semibold text-zinc-900">Triggers ({{ count($objetos['triggers']) }})</h2>
                        <ul class="space-y-1.5 text-sm">
                            @forelse ($objetos['triggers'] as $t)
                                <li class="rounded-lg border border-zinc-200 px-3 py-2"><code class="text-xs">{{ $t->nome }}</code><br><span class="text-xs text-zinc-500">{{ $t->momento }} {{ $t->evento }} em {{ $t->tabela }}</span></li>
                            @empty
                                <li class="text-zinc-500">Nenhum trigger encontrado.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-2 text-sm font-semibold text-zinc-900">Views ({{ count($objetos['views']) }})</h2>
                        <ul class="text-sm">
                            @forelse ($objetos['views'] as $v)
                                <li><code class="text-xs">{{ $v->nome }}</code></li>
                            @empty
                                <li class="text-zinc-500">Nenhuma view.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-2 text-sm font-semibold text-zinc-900">Índices secundários ({{ count($objetos['indices']) }})</h2>
                        <ul class="space-y-0.5 text-sm">
                            @foreach ($objetos['indices'] as $i)
                                <li><code class="text-xs">{{ $i->nome }}</code> <span class="text-xs text-zinc-500">({{ $i->tabela }})</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="text-xs text-zinc-500">PHP {{ PHP_VERSION }} · Laravel {{ app()->version() }} · {{ config('app.env') }} · consultado em {{ formata_data_hora($info['agora']) }}</p>
                </div>
            </div>
        @endif
    </div>
</x-layouts.simples>
