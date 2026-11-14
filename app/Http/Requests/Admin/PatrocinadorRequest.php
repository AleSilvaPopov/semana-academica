<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Patrocinador: PK de texto "nome" (informada só no cadastro; somente leitura na edição). */
class PatrocinadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('cadastros-globais');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nome')) {
            $this->merge(['nome' => preg_replace('/\s+/', ' ', trim((string) $this->input('nome')))]);
        }
    }

    public function rules(): array
    {
        $regras = ['observacoes' => ['nullable', 'string', 'max:5000']];

        if (! $this->route('patrocinador')) {
            $regras['nome'] = ['required', 'string', 'max:120', 'not_regex:/[\/\\\\]/', Rule::unique('patrocinador', 'nome')];
        }

        return $regras;
    }

    public function messages(): array
    {
        return [
            'nome.unique' => 'Já existe um patrocinador com este nome.',
            'nome.not_regex' => 'O nome não pode conter barras ( / ou \\ ).',
        ];
    }
}
