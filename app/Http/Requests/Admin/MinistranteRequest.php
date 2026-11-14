<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\NormalizaCampos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Ministrante é especialização de participante (cpf é PK e FK):
 * o cadastro escolhe um participante que ainda NÃO é ministrante.
 */
class MinistranteRequest extends FormRequest
{
    use NormalizaCampos;

    public function authorize(): bool
    {
        return Gate::allows('cadastros-globais');
    }

    protected function prepareForValidation(): void
    {
        $this->normalizarCpf();
        $this->normalizarLista('certificados');
    }

    public function rules(): array
    {
        $editando = (bool) $this->route('ministrante');

        return [
            'cpf' => $editando ? ['prohibited'] : [
                'required', 'digits:11',
                Rule::exists('participante', 'cpf'),
                Rule::unique('ministrante', 'cpf'),
            ],
            'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remover_imagem' => ['nullable', 'boolean'],
            'certificados' => ['array', 'max:20'],
            'certificados.*' => ['string', 'max:200', 'distinct:ignore_case'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required' => 'Escolha o participante que será ministrante.',
            'cpf.exists' => 'O participante escolhido não existe.',
            'cpf.unique' => 'Este participante já é ministrante.',
            'imagem.max' => 'A imagem deve ter no máximo 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return ['cpf' => 'participante', 'imagem' => 'imagem de divulgação'];
    }
}
