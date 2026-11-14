<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusAtividade;
use App\Enums\TipoAtividade;
use App\Models\Atividade;
use App\Models\Coordenadoria;
use App\Models\LocalEvento;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Atividade: CHECKs (duração > 0, vagas > 0, valor >= 0) e as regras de negócio:
 *  - vagas <= capacidade do local;
 *  - vagas >= inscritos já existentes (na edição);
 *  - data_realizacao entre data_inicio e data_termino da edição da coordenadoria;
 *  - coordenadoria da edição (coordenador só escolhe a(s) própria(s)).
 */
class AtividadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $atividade = $this->route('atividade');

        return $atividade
            ? $this->user('integrante')->can('update', $atividade)
            : $this->user('integrante')->can('create', Atividade::class);
    }

    /** Coordenadorias que o usuário pode escolher (todas da edição para a presidência; só as dele para o coordenador). */
    public function coordenadoriasPermitidas(): array
    {
        $atividade = $this->route('atividade');
        $idComissao = $atividade?->coordenadoria?->id_comissao ?? app(EdicaoAtual::class)->id();

        return self::coordenadoriasDoUsuario($this->user('integrante'), (int) $idComissao);
    }

    public static function coordenadoriasDoUsuario($integrante, int $idComissao): array
    {
        $permissoes = app(Permissoes::class);

        if ($permissoes->presideEdicao($integrante, $idComissao)) {
            return Coordenadoria::daEdicao($idComissao)->pluck('id_coordenadoria')->all();
        }

        return $permissoes->coordenadoriasNaEdicao($integrante, $idComissao);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'descricao' => ['required', 'string', 'max:5000'],
            'tipo' => ['required', Rule::enum(TipoAtividade::class)],
            'status' => ['required', Rule::enum(StatusAtividade::class)],
            'duracao_minutos' => ['required', 'integer', 'min:1', 'max:32767'],
            'data_realizacao' => ['required', 'date'],
            'vagas' => ['required', 'integer', 'min:1', 'max:100000'],
            'valor_inscricao' => ['required', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
            'id_local' => ['required', 'integer', Rule::exists('local_evento', 'id_local')],
            'id_coordenadoria' => ['required', 'integer', Rule::in($this->coordenadoriasPermitidas())],
            'ministrantes' => ['array'],
            'ministrantes.*' => ['string', Rule::exists('ministrante', 'cpf')],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $coordenadoria = Coordenadoria::with('comissao')->find($this->input('id_coordenadoria'));
            $edicao = $coordenadoria->comissao;
            $data = Carbon::parse($this->input('data_realizacao'));

            if ($data->toDateString() < $edicao->data_inicio->toDateString() || $data->toDateString() > $edicao->data_termino->toDateString()) {
                $validator->errors()->add('data_realizacao', 'A atividade deve acontecer durante a edição: '.$edicao->periodo.'.');
            }

            $local = LocalEvento::find($this->input('id_local'));
            if ((int) $this->input('vagas') > $local->capacidade) {
                $validator->errors()->add('vagas', "O local escolhido comporta {$local->capacidade} pessoas. As vagas não podem passar da capacidade.");
            }

            $atividade = $this->route('atividade');
            if ($atividade) {
                $inscritos = $atividade->inscricoes()->count();
                if ((int) $this->input('vagas') < $inscritos) {
                    $validator->errors()->add('vagas', "Já existem {$inscritos} inscritos. As vagas não podem ficar abaixo disso.");
                }
            }
        }];
    }

    public function messages(): array
    {
        return [
            'id_coordenadoria.in' => 'Escolha uma coordenadoria desta edição que você pode gerenciar.',
            'duracao_minutos.min' => 'A duração deve ser maior que zero.',
            'vagas.min' => 'O número de vagas deve ser maior que zero.',
            'valor_inscricao.min' => 'O valor da inscrição não pode ser negativo.',
        ];
    }
}
