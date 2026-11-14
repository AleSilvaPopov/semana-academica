<?php

namespace App\Services;

use App\Models\Comissao;
use Illuminate\Database\Eloquent\Collection;

/**
 * Edição (comissao_organizadora) selecionada no cabeçalho do painel.
 * Fica guardada na sessão; o padrão é a edição mais recente (maior data_inicio).
 */
class EdicaoAtual
{
    private const CHAVE_SESSAO = 'admin.edicao_id';

    private ?Comissao $edicao = null;

    private bool $carregada = false;

    private ?Collection $todas = null;

    public function get(): ?Comissao
    {
        if (! $this->carregada) {
            $id = session(self::CHAVE_SESSAO);
            $this->edicao = ($id ? Comissao::find($id) : null) ?? Comissao::maisRecente();
            $this->carregada = true;
        }

        return $this->edicao;
    }

    public function id(): ?int
    {
        return $this->get()?->id_comissao;
    }

    /** Edição obrigatória nas telas filtradas por edição (404 se o banco estiver vazio). */
    public function obrigatoria(): Comissao
    {
        return $this->get() ?? abort(404, 'Nenhuma edição cadastrada.');
    }

    public function selecionar(int $idComissao): void
    {
        session([self::CHAVE_SESSAO => $idComissao]);
        $this->carregada = false;
    }

    /** @return Collection<int, Comissao> */
    public function todas(): Collection
    {
        return $this->todas ??= Comissao::query()->recentesPrimeiro()->get(['id_comissao', 'edicao', 'data_inicio', 'nome_evento']);
    }
}
