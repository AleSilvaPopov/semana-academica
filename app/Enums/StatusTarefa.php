<?php

namespace App\Enums;

/** tarefa.status ENUM('pendente','em_andamento','concluida','cancelada') */
enum StatusTarefa: string
{
    use RotulosEnum;

    case Pendente = 'pendente';
    case EmAndamento = 'em_andamento';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    public function rotulo(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::EmAndamento => 'Em andamento',
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Pendente => 'amber',
            self::EmAndamento => 'blue',
            self::Concluida => 'green',
            self::Cancelada => 'zinc',
        };
    }

    /** Status que ainda contam como "em aberto" (podem ficar atrasados). */
    public static function abertos(): array
    {
        return [self::Pendente->value, self::EmAndamento->value];
    }
}
