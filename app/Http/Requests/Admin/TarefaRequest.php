<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusTarefa;
use App\Models\Coordenadoria;
use App\Models\Integrante;
use App\Models\Tarefa;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Tarefa. Regra "só coordenador cria tarefa" validada AQUI antes de chegar ao trigger
 * trg_tarefa_criador_bi/bu (que continua como segunda barreira no banco):
 *  - o criador precisa coordenar uma coordenadoria da edição;
 *  - a data de criação precisa cair no período dessa coordenação (é assim que a tarefa
 *    fica associada à coordenadoria e à edição, já que a tabela não guarda esses dados);
 *  - coordenador cria tarefas em nome próprio; a presidência escolhe qualquer coordenador.
 * CHECK: prazo_final >= data_criacao.
 */
class TarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tarefa = $this->route('tarefa');

        return $tarefa
            ? $this->user('integrante')->can('update', $tarefa)
            : $this->user('integrante')->can('create', Tarefa::class);
    }

    public function idComissao(): int
    {
        $tarefa = $this->route('tarefa');

        return (int) ($tarefa?->coordenadoriaVigente()?->id_comissao ?? app(EdicaoAtual::class)->id());
    }

    /** RGAs que podem aparecer como criador para o usuário logado. */
    public static function criadoresPermitidos(Integrante $usuario, int $idComissao): array
    {
        $permissoes = app(Permissoes::class);

        if ($permissoes->presideEdicao($usuario, $idComissao)) {
            return Coordenadoria::daEdicao($idComissao)->pluck('rga_coordenador')->unique()->values()->all();
        }

        return $permissoes->coordenaNaEdicao($usuario, $idComissao) ? [$usuario->rga] : [];
    }

    protected function prepareForValidation(): void
    {
        // Coordenador (sem presidência) sempre cria em nome próprio.
        $permitidos = self::criadoresPermitidos($this->user('integrante'), $this->idComissao());
        if (count($permitidos) === 1 && ! app(Permissoes::class)->presideEdicao($this->user('integrante'), $this->idComissao())) {
            $this->merge(['rga_criador' => $permitidos[0]]);
        }
    }

    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'max:5000'],
            'data_criacao' => ['required', 'date'],
            'prazo_final' => ['required', 'date', 'after_or_equal:data_criacao'],
            'status' => ['required', Rule::enum(StatusTarefa::class)],
            'rga_criador' => ['required', 'string', Rule::in(self::criadoresPermitidos($this->user('integrante'), $this->idComissao()))],
            'responsaveis' => ['array'],
            'responsaveis.*' => ['string', 'distinct', Rule::exists('integrante', 'rga')],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->hasAny(['rga_criador', 'data_criacao'])) {
                return;
            }

            $coordenacao = Coordenadoria::query()
                ->daEdicao($this->idComissao())
                ->where('rga_coordenador', $this->input('rga_criador'))
                ->first();

            if (! $coordenacao) {
                $validator->errors()->add('rga_criador', 'Somente coordenadores podem criar tarefas.');

                return;
            }

            $data = $this->input('data_criacao');
            $inicio = $coordenacao->data_inicio_coordenacao->format('Y-m-d');
            $fim = $coordenacao->data_termino_coordenacao?->format('Y-m-d');

            if ($data < $inicio || ($fim && $data > $fim)) {
                $validator->errors()->add(
                    'data_criacao',
                    "A data de criação deve estar dentro da coordenação de \"{$coordenacao->nome}\" ("
                    .formata_data($inicio).' a '.formata_data($fim, 'atual').').'
                );
            }
        }];
    }

    public function messages(): array
    {
        return [
            'rga_criador.in' => 'Somente coordenadores desta edição podem criar tarefas.',
            'prazo_final.after_or_equal' => 'O prazo final deve ser igual ou posterior à data de criação.',
        ];
    }
}
