<?php

namespace App\Http\Requests\Admin;

use App\Models\Coordenadoria;
use App\Models\Integrante;
use App\Services\EdicaoAtual;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Coordenadoria: UNIQUE (id_comissao, nome), CHECK término >= início e as regras:
 *  - data_inicio_coordenacao não pode ser anterior à data_ingresso do coordenador;
 *  - uma pessoa coordena no máximo uma coordenadoria por edição (relação 1:1 "Coordena");
 *  - não troca o coordenador se ele já criou tarefas nesta coordenação (o trigger
 *    trg_tarefa_criador_bu passaria a recusar alterações nessas tarefas).
 */
class CoordenadoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $coordenadoria = $this->route('coordenadoria');

        return $coordenadoria
            ? $this->user('integrante')->can('update', $coordenadoria)
            : $this->user('integrante')->can('create', Coordenadoria::class);
    }

    private function idComissao(): int
    {
        return $this->route('coordenadoria')?->id_comissao ?? (int) app(EdicaoAtual::class)->id();
    }

    public function rules(): array
    {
        $coordenadoria = $this->route('coordenadoria');

        return [
            'nome' => [
                'required', 'string', 'max:80',
                Rule::unique('coordenadoria', 'nome')
                    ->where('id_comissao', $this->idComissao())
                    ->ignore($coordenadoria?->id_coordenadoria, 'id_coordenadoria'),
            ],
            'descricao_atuacao' => ['required', 'string', 'max:5000'],
            'rga_coordenador' => ['required', 'string', Rule::exists('integrante', 'rga')],
            'data_inicio_coordenacao' => ['required', 'date'],
            'data_termino_coordenacao' => ['nullable', 'date', 'after_or_equal:data_inicio_coordenacao'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->hasAny(['rga_coordenador', 'data_inicio_coordenacao'])) {
                return;
            }

            $atual = $this->route('coordenadoria');
            $coordenador = Integrante::find($this->input('rga_coordenador'));

            if ($coordenador && $this->input('data_inicio_coordenacao') < $coordenador->data_ingresso->format('Y-m-d')) {
                $validator->errors()->add(
                    'data_inicio_coordenacao',
                    'O coordenador ingressou na comissão em '.formata_data($coordenador->data_ingresso).'. A coordenação não pode começar antes disso.'
                );
            }

            $outra = Coordenadoria::query()
                ->where('id_comissao', $this->idComissao())
                ->where('rga_coordenador', $this->input('rga_coordenador'))
                ->when($atual, fn ($q) => $q->where('id_coordenadoria', '<>', $atual->id_coordenadoria))
                ->first();

            if ($outra) {
                $validator->errors()->add('rga_coordenador', "Esta pessoa já coordena a coordenadoria \"{$outra->nome}\" nesta edição.");
            }

            if ($atual && $atual->rga_coordenador !== $this->input('rga_coordenador')) {
                $tarefas = $atual->tarefas()->count();

                if ($tarefas > 0) {
                    $validator->errors()->add(
                        'rga_coordenador',
                        "O coordenador atual criou {$tarefas} tarefa(s) nesta coordenação. Trocar o coordenador faria o trigger "
                        .'trg_tarefa_criador_bu bloquear alterações nessas tarefas. Encerre a coordenação (data de término) e crie uma nova, se necessário.'
                    );
                }
            }
        }];
    }

    public function messages(): array
    {
        return [
            'nome.unique' => 'Já existe uma coordenadoria com este nome nesta edição.',
            'data_termino_coordenacao.after_or_equal' => 'O término da coordenação deve ser igual ou posterior ao início.',
        ];
    }
}
