<?php

namespace App\Http\Requests\Portal;

use App\Auth\Sha256Hasher;
use App\Enums\TipoParticipante;
use App\Http\Requests\Concerns\NormalizaCampos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** "Meu perfil": dados, telefones e troca de senha (exige a senha atual). CPF e status não mudam aqui. */
class PerfilRequest extends FormRequest
{
    use NormalizaCampos;

    public function authorize(): bool
    {
        return $this->user('participante') !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizarLista('telefones', somenteDigitos: true);
        $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
    }

    public function rules(): array
    {
        $cpf = $this->user('participante')->cpf;

        return [
            'nome_completo' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:120', Rule::unique('participante', 'email')->ignore($cpf, 'cpf')],
            'pais' => ['required', 'string', 'max:60'],
            'data_nascimento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'tipo_participante' => ['required', Rule::enum(TipoParticipante::class)],
            'instituicao_origem' => ['required', 'string', 'max:150'],
            'telefones' => ['array', 'max:5'],
            'telefones.*' => ['digits_between:10,15', 'distinct'],
            'senha_atual' => ['nullable', 'required_with:senha', 'string'],
            'senha' => ['nullable', 'string', 'min:6', 'max:100', 'confirmed'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($this->filled('senha') && $this->filled('senha_atual')
                && ! app(Sha256Hasher::class)->check($this->input('senha_atual'), $this->user('participante')->senha_hash)) {
                $validator->errors()->add('senha_atual', 'A senha atual está incorreta.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Já existe outra conta com este e-mail.',
            'senha_atual.required_with' => 'Informe a senha atual para definir uma nova.',
            'telefones.*.digits_between' => 'Cada telefone deve ter de 10 a 15 dígitos (com DDD).',
        ];
    }
}
