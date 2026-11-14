<?php

/*
 * Funções globais de formatação usadas nas views (carregadas pelo composer, em "autoload.files").
 * O banco guarda CPF e telefone só com dígitos; a máscara é aplicada apenas na exibição.
 */

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

if (! function_exists('somente_digitos')) {
    /** Remove tudo o que não é dígito (usado antes de salvar CPF e telefones). */
    function somente_digitos(?string $valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor);
    }
}

if (! function_exists('formata_cpf')) {
    /** 12345678901 -> 123.456.789-01 */
    function formata_cpf(?string $cpf): string
    {
        $d = somente_digitos($cpf);

        if (strlen($d) !== 11) {
            return (string) $cpf;
        }

        return substr($d, 0, 3).'.'.substr($d, 3, 3).'.'.substr($d, 6, 3).'-'.substr($d, 9, 2);
    }
}

if (! function_exists('mascara_cpf')) {
    /** CPF parcialmente oculto, para certificados: ***.456.789-** */
    function mascara_cpf(?string $cpf): string
    {
        $d = somente_digitos($cpf);

        if (strlen($d) !== 11) {
            return (string) $cpf;
        }

        return '***.'.substr($d, 3, 3).'.'.substr($d, 6, 3).'-**';
    }
}

if (! function_exists('formata_telefone')) {
    /**
     * 67999999999 -> (67) 99999-9999 | 6733538999 -> (67) 3353-8999.
     * Números com DDI (12 a 15 dígitos, ex.: 595981800305 do Paraguai) -> +595 981800305.
     */
    function formata_telefone(?string $telefone): string
    {
        $d = somente_digitos($telefone);

        return match (true) {
            strlen($d) === 11 => '('.substr($d, 0, 2).') '.substr($d, 2, 5).'-'.substr($d, 7),
            strlen($d) === 10 => '('.substr($d, 0, 2).') '.substr($d, 2, 4).'-'.substr($d, 6),
            strlen($d) > 11 => '+'.substr($d, 0, 3).' '.substr($d, 3),
            default => (string) $telefone,
        };
    }
}

if (! function_exists('formata_moeda')) {
    /** 3500.5 -> R$ 3.500,50 (null -> "—") */
    function formata_moeda(int|float|string|null $valor, string $vazio = '—'): string
    {
        if ($valor === null || $valor === '') {
            return $vazio;
        }

        return 'R$ '.number_format((float) $valor, 2, ',', '.');
    }
}

if (! function_exists('formata_data')) {
    /** 2026-10-19 -> 19/10/2026 */
    function formata_data(CarbonInterface|string|null $data, string $vazio = '—'): string
    {
        if (blank($data)) {
            return $vazio;
        }

        return ($data instanceof CarbonInterface ? $data : Carbon::parse($data))->format('d/m/Y');
    }
}

if (! function_exists('formata_data_hora')) {
    /** 2026-10-19 19:00:00 -> 19/10/2026 19:00 */
    function formata_data_hora(CarbonInterface|string|null $data, string $vazio = '—'): string
    {
        if (blank($data)) {
            return $vazio;
        }

        return ($data instanceof CarbonInterface ? $data : Carbon::parse($data))->format('d/m/Y H:i');
    }
}

if (! function_exists('formata_duracao')) {
    /** 90 -> 1h30 | 60 -> 1h | 45 -> 45min | 240 -> 4h */
    function formata_duracao(?int $minutos): string
    {
        if (! $minutos) {
            return '—';
        }

        $h = intdiv($minutos, 60);
        $m = $minutos % 60;

        if ($h === 0) {
            return $m.'min';
        }

        return $h.'h'.($m ? str_pad((string) $m, 2, '0', STR_PAD_LEFT) : '');
    }
}

if (! function_exists('carga_horaria')) {
    /** Carga horária do certificado: 90 -> "1,5 hora" | 240 -> "4 horas" */
    function carga_horaria(int $minutos): string
    {
        $horas = $minutos / 60;
        $texto = rtrim(rtrim(number_format($horas, 2, ',', ''), '0'), ',');

        return $texto.' '.($horas <= 1 ? 'hora' : 'horas');
    }
}

if (! function_exists('iniciais')) {
    /** "Helena Vasques Tanaka" -> "HT" (avatar sem foto) */
    function iniciais(string $nome): string
    {
        $partes = preg_split('/\s+/', trim($nome)) ?: [];
        $primeira = mb_substr($partes[0] ?? '', 0, 1);
        $ultima = count($partes) > 1 ? mb_substr(end($partes), 0, 1) : '';

        return mb_strtoupper($primeira.$ultima);
    }
}
