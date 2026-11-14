<?php

namespace App\Http\Requests\Portal;

use App\Enums\TipoParticipante;
use App\Http\Requests\Concerns\NormalizaCampos;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Cadastro de conta no portal (cria um participante com status 'ativo'). */
class CadastroRequest extends FormRequest
{
    use NormalizaCampos;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizarCpf();
        $this->normalizarLista('telefones', somenteDigitos: true);
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'pais' => trim((string) $this->input('pais')) ?: 'Brasil',
        ]);
    }

    public function rules(): array
    {
        return [
            'cpf' => ['required', 'digits:11', new Cpf(), Rule::unique('participante', 'cpf')],
            'nome_completo' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:120', Rule::unique('participante', 'email')],
            'senha' => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
            'pais' => ['required', 'string', 'max:60'],
            'data_nascimento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'tipo_participante' => ['required', Rule::enum(TipoParticipante::class)],
            'instituicao_origem' => ['required', 'string', 'max:150'],
            'telefones' => ['array', 'max:5'],
            'telefones.*' => ['digits_between:10,15', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.unique' => 'Já existe uma conta com este CPF.',
            'email.unique' => 'Já existe uma conta com este e-mail.',
            'telefones.*.digits_between' => 'Cada telefone deve ter de 10 a 15 dígitos (com DDD).',
        ];
    }
}
