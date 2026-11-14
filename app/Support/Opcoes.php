<?php

namespace App\Support;

use App\Models\Comissao;
use App\Models\Coordenadoria;
use App\Models\Integrante;
use Illuminate\Support\Collection;

/** Listas para os <select> dos formulários (FK mostrando o nome, nunca o ID). */
class Opcoes
{
    /** [rga => "Nome (RGA)"] */
    public static function integrantes(): Collection
    {
        return Integrante::query()->orderBy('nome_completo')->get(['rga', 'nome_completo'])
            ->mapWithKeys(fn ($i) => [$i->rga => $i->nome_completo.' ('.$i->rga.')']);
    }

    /** Coordenadorias agrupadas por edição: ["10ª edição (2026)" => [id => nome]] */
    public static function coordenadoriasPorEdicao(): array
    {
        $grupos = [];
        $edicoes = Comissao::query()->recentesPrimeiro()->with(['coordenadorias' => fn ($q) => $q->orderBy('nome')])->get();

        foreach ($edicoes as $edicao) {
            $grupos[$edicao->titulo] = $edicao->coordenadorias->pluck('nome', 'id_coordenadoria')->all();
        }

        return $grupos;
    }

    /** Coordenadorias de uma edição: [id => nome] */
    public static function coordenadoriasDaEdicao(int $idComissao, ?array $somenteIds = null): Collection
    {
        return Coordenadoria::query()
            ->daEdicao($idComissao)
            ->when($somenteIds !== null, fn ($q) => $q->whereIn('id_coordenadoria', $somenteIds))
            ->orderBy('nome')
            ->pluck('nome', 'id_coordenadoria');
    }
}
