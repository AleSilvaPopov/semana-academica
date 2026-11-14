<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Curso: UNIQUE (nome_curso, faculdade). */
class CursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('cadastros-globais');
    }

    public function rules(): array
    {
        $curso = $this->route('curso');

        return [
            'nome_curso' => [
                'required', 'string', 'max:100',
                Rule::unique('curso', 'nome_curso')
                    ->where('faculdade', (string) $this->input('faculdade'))
                    ->ignore($curso?->id_curso, 'id_curso'),
            ],
            'faculdade' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return ['nome_curso.unique' => 'Já existe um curso com este nome nesta faculdade.'];
    }
}
