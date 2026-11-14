<?php

namespace App\Http\Requests\Admin;

use App\Enums\TipoLocal;
use App\Http\Requests\Concerns\NormalizaCampos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Local: UNIQUE (faculdade, bloco, numero), CHECK capacidade > 0 e
 * regra de negócio "vagas da atividade <= capacidade do local" (vale também ao reduzir a capacidade).
 */
class LocalRequest extends FormRequest
{
    use NormalizaCampos;

    public function authorize(): bool
    {
        return Gate::allows('cadastros-globais');
    }

    protected function prepareForValidation(): void
    {
        $this->normalizarLista('equipamentos');
    }

    public function rules(): array
    {
        $local = $this->route('local');

        return [
            'tipo' => ['required', Rule::enum(TipoLocal::class)],
            'faculdade' => ['required', 'string', 'max:150'],
            'bloco' => ['required', 'string', 'max:20'],
            'numero' => [
                'required', 'string', 'max:10',
                Rule::unique('local_evento', 'numero')
                    ->where('faculdade', (string) $this->input('faculdade'))
                    ->where('bloco', (string) $this->input('bloco'))
                    ->ignore($local?->id_local, 'id_local'),
            ],
            'capacidade' => ['required', 'integer', 'min:1', 'max:100000'],
            'observacoes' => ['nullable', 'string', 'max:5000'],
            'equipamentos' => ['array', 'max:30'],
            'equipamentos.*' => ['string', 'max:60', 'distinct:ignore_case'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $local = $this->route('local');

            if (! $local || $validator->errors()->has('capacidade')) {
                return;
            }

            $maiorVagas = (int) $local->atividades()->max('vagas');

            if ($maiorVagas > (int) $this->input('capacidade')) {
                $validator->errors()->add(
                    'capacidade',
                    "Há atividade neste local com {$maiorVagas} vagas. A capacidade não pode ser menor que isso."
                );
            }
        }];
    }

    public function messages(): array
    {
        return [
            'numero.unique' => 'Já existe um local com esta faculdade, bloco e número.',
            'capacidade.min' => 'A capacidade deve ser maior que zero.',
        ];
    }
}
