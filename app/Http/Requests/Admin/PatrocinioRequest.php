<?php

namespace App\Http\Requests\Admin;

use App\Enums\TipoPatrocinio;
use App\Services\EdicaoAtual;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Patrocínio (N:N com atributos). PK composta (nome_patrocinador, id_comissao): no cadastro
 * o patrocinador não pode repetir na mesma edição; na edição as duas chaves ficam fixas.
 * CHECK valor >= 0 (NULL permitido para patrocínios não financeiros).
 */
class PatrocinioRequest extends FormRequest
{
    public function idComissao(): int
    {
        return (int) ($this->route('comissao')?->id_comissao ?? app(EdicaoAtual::class)->id());
    }

    public function authorize(): bool
    {
        return Gate::allows('gerir-edicao', $this->idComissao());
    }

    public function rules(): array
    {
        $regras = [
            'tipo_patrocinio' => ['required', Rule::enum(TipoPatrocinio::class)],
            'descricao_patrocinio' => ['required', 'string', 'max:255'],
            'valor' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'requisitos' => ['nullable', 'string', 'max:5000'],
        ];

        if (! $this->route('patrocinador')) {
            $regras['nome_patrocinador'] = [
                'required', 'string', Rule::exists('patrocinador', 'nome'),
                Rule::unique('patrocinio', 'nome_patrocinador')->where('id_comissao', $this->idComissao()),
            ];
        }

        return $regras;
    }

    public function messages(): array
    {
        return [
            'nome_patrocinador.unique' => 'Este patrocinador já está cadastrado nesta edição.',
            'valor.min' => 'O valor não pode ser negativo.',
        ];
    }
}
