<?php

namespace App\Http\Requests\Admin;

use App\Models\Comissao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Edição: UNIQUE (nome_evento, edicao), CHECK data_termino >= data_inicio,
 * presidência com presidente <> vice (CHECK ck_presidente_diferente_vice) e as atividades
 * já cadastradas precisam continuar dentro do novo período.
 */
class ComissaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comissao = $this->route('comissao');

        return $comissao
            ? $this->user('integrante')->can('update', $comissao)
            : $this->user('integrante')->can('create', Comissao::class);
    }

    public function rules(): array
    {
        $comissao = $this->route('comissao');

        return [
            'nome_evento' => ['required', 'string', 'max:150'],
            'edicao' => [
                'required', 'integer', 'min:1', 'max:32767',
                Rule::unique('comissao_organizadora', 'edicao')
                    ->where('nome_evento', (string) $this->input('nome_evento'))
                    ->ignore($comissao?->id_comissao, 'id_comissao'),
            ],
            'data_inicio' => ['required', 'date'],
            'data_termino' => ['required', 'date', 'after_or_equal:data_inicio'],
            'local_sede' => ['required', 'string', 'max:150'],
            'observacoes' => ['nullable', 'string', 'max:5000'],
            'rga_presidente' => ['required', 'string', Rule::exists('integrante', 'rga')],
            'rga_vice' => ['required', 'string', 'different:rga_presidente', Rule::exists('integrante', 'rga')],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $comissao = $this->route('comissao');

            if (! $comissao || $validator->errors()->hasAny(['data_inicio', 'data_termino'])) {
                return;
            }

            // Regra: data_realizacao das atividades deve ficar entre data_inicio e data_termino da edição.
            $fora = $comissao->atividades()
                ->where(fn ($q) => $q->whereDate('data_realizacao', '<', $this->input('data_inicio'))
                    ->orWhereDate('data_realizacao', '>', $this->input('data_termino')))
                ->count();

            if ($fora > 0) {
                $validator->errors()->add('data_inicio', "Há {$fora} atividade(s) desta edição fora do novo período. Ajuste as atividades antes.");
            }
        }];
    }

    public function messages(): array
    {
        return [
            'edicao.unique' => 'Já existe esta edição para este evento.',
            'rga_vice.different' => 'Presidente e vice-presidente devem ser pessoas diferentes.',
            'data_termino.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
        ];
    }
}
