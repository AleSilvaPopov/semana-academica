<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusConta;
use App\Enums\TipoParticipante;
use App\Http\Requests\Concerns\NormalizaCampos;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Participante no painel: CPF válido e único (só no cadastro), e-mail único, telefones só com dígitos. */
class ParticipanteRequest extends FormRequest
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
        $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
    }

    public function rules(): array
    {
        $participante = $this->route('participante');
        $editando = (bool) $participante;

        return [
            'cpf' => $editando ? ['prohibited'] : ['required', 'digits:11', new Cpf(), Rule::unique('participante', 'cpf')],
            'nome_completo' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:120', Rule::unique('participante', 'email')->ignore($participante?->cpf, 'cpf')],
            'senha' => [$editando ? 'nullable' : 'required', 'string', 'min:6', 'max:100', 'confirmed'],
            'pais' => ['required', 'string', 'max:60'],
            'data_nascimento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'status_conta' => ['required', Rule::enum(StatusConta::class)],
            'tipo_participante' => ['required', Rule::enum(TipoParticipante::class)],
            'instituicao_origem' => ['required', 'string', 'max:150'],
            'telefones' => ['array', 'max:5'],
            'telefones.*' => ['digits_between:10,15', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.unique' => 'Já existe um participante com este CPF.',
            'cpf.prohibited' => 'O CPF não pode ser alterado.',
            'email.unique' => 'Já existe um participante com este e-mail.',
            'telefones.*.digits_between' => 'Cada telefone deve ter de 10 a 15 dígitos (com DDD).',
        ];
    }
}
