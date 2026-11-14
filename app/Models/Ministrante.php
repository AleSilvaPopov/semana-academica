<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Tabela ministrante: especialização de participante (cpf é PK e FK, ON DELETE CASCADE).
 */
class Ministrante extends Model
{
    protected $table = 'ministrante';

    protected $primaryKey = 'cpf';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['cpf', 'imagem_divulgacao'];

    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class, 'cpf', 'cpf');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(MinistranteCertificado::class, 'cpf', 'cpf');
    }

    /** N:N sem atributos (tabela ministra). */
    public function atividades(): BelongsToMany
    {
        return $this->belongsToMany(Atividade::class, 'ministra', 'cpf_ministrante', 'id_atividade', 'cpf', 'id_atividade');
    }

    /**
     * URL pública da foto. Os dados de teste usam "img/ministrantes/x.jpg" (procurado em public/);
     * os uploads do sistema ficam no disco "public" (storage/app/public, servido em /storage).
     * Se o arquivo não existir, devolve null e a view mostra as iniciais.
     */
    public function getImagemUrlAttribute(): ?string
    {
        $caminho = $this->imagem_divulgacao;

        if (blank($caminho)) {
            return null;
        }

        if (is_file(public_path($caminho))) {
            return asset($caminho);
        }

        if (Storage::disk('public')->exists($caminho)) {
            return Storage::disk('public')->url($caminho);
        }

        return null;
    }

    public function getNomeAttribute(): string
    {
        return $this->participante?->nome_completo ?? $this->cpf;
    }
}
