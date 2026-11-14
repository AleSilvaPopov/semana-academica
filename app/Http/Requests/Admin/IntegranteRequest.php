<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\NormalizaCampos;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Integrante: RGA (PK, só no cadastro), CPF válido e único, telefones/e-mails (multivalorados),
 * coordenadoria em que atua (opcional) e a regra "data_ingresso <= início das coordenações dele".
 */
class IntegranteRequest extends FormRequest
{
    use NormalizaCampos;

    public function authorize(): bool
    {
        return Gate::allows('cadastros-globais');
    }

    protected function prepareForValidation(): void
    {
        $this->normalizarCpf();
        $this->normalizarLista('telefones', somenteDigitos: true);
        $this->normalizarLista('emails');
        $this->merge(['emails' => array_map('mb_strtolower', (array) $this->input('emails', []))]);

        if ($this->has('rga')) {
            $this->merge(['rga' => trim((string) $this->input('rga'))]);
        }
    }

    public function rules(): array
    {
        $integrante = $this->route('integrante');
        $editando = (bool) $integrante;

        return [
            'rga' => $editando ? ['prohibited'] : ['required', 'string', 'max:20', 'alpha_num', Rule::unique('integrante', 'rga')],
            'cpf' => ['required', 'digits:11', new Cpf(), Rule::unique('integrante', 'cpf')->ignore($integrante?->rga, 'rga')],
            'nome_completo' => ['required', 'string', 'min:3', 'max:150'],
            'id_curso' => ['required', 'integer', Rule::exists('curso', 'id_curso')],
            'funcao' => ['required', 'string', 'max:60'],
            'data_ingresso' => ['required', 'date', 'before_or_equal:today'],
            'id_coordenadoria' => ['nullable', 'integer', Rule::exists('coordenadoria', 'id_coordenadoria')],
            'observacoes' => ['nullable', 'string', 'max:5000'],
            'senha' => [$editando ? 'nullable' : 'required', 'string', 'min:6', 'max:100', 'confirmed'],
            'telefones' => ['array', 'max:5'],
            'telefones.*' => ['digits_between:10,15', 'distinct'],
            'emails' => ['array', 'max:5'],
            'emails.*' => ['email', 'max:120', 'distinct'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $integrante = $this->route('integrante');

            if (! $integrante || $validator->errors()->has('data_ingresso')) {
                return;
            }

            // Regra de negócio: data_inicio_coordenacao não pode ser anterior à data_ingresso do coordenador.
            $primeiraCoordenacao = $integrante->coordenadoriasQueCoordena()->min('data_inicio_coordenacao');

            if ($primeiraCoordenacao && $this->input('data_ingresso') > $primeiraCoordenacao) {
                $validator->errors()->add(
                    'data_ingresso',
                    'Este integrante coordena desde '.formata_data($primeiraCoordenacao).'. A data de ingresso não pode ser posterior a isso.'
                );
            }
        }];
    }

    public function messages(): array
    {
        return [
            'rga.unique' => 'Já existe um integrante com este RGA.',
            'rga.prohibited' => 'O RGA não pode ser alterado.',
            'cpf.unique' => 'Já existe um integrante com este CPF.',
            'telefones.*.digits_between' => 'Cada telefone deve ter de 10 a 15 dígitos (com DDD).',
            'emails.*.distinct' => 'Há e-mails repetidos.',
        ];
    }
}
