<?php

namespace App\Support;

/**
 * Atalhos para a identidade visual da edição (config/identidade.php).
 */
class Identidade
{
    /** Ex.: "Semana Integrada de Computação 2026". */
    public static function nomeCompleto(): string
    {
        return trim(config('identidade.nome').' '.config('identidade.ano'));
    }

    /** Título da aba do navegador: "Programação · SIC 2026". */
    public static function tituloPagina(?string $titulo = null): string
    {
        $marca = trim(config('identidade.sigla').' '.config('identidade.ano'));

        return $titulo ? "$titulo · $marca" : self::nomeCompleto();
    }

    /** Atributos do <html>: desenho de fundo e esquema (claro/escuro) para os controles nativos. */
    public static function atributosHtml(): string
    {
        $fundo = in_array(config('identidade.fundo'), ['grade', 'circuitos', 'rede'], true) ? config('identidade.fundo') : 'nenhum';
        $esquema = self::fundoEhClaro() ? 'light' : 'dark';

        return sprintf('data-fundo="%s" data-esquema="%s"', $fundo, $esquema);
    }

    /** Luminância relativa da cor de fundo > 0.5 → tema claro. */
    public static function fundoEhClaro(): bool
    {
        $hex = ltrim((string) config('identidade.cores.fundo', '#000000'), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (! ctype_xdigit(substr($hex, 0, 6))) {
            return false;
        }
        [$r, $g, $b] = array_map(fn ($c) => hexdec($c) / 255, str_split(substr($hex, 0, 6), 2));

        return (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 0.5;
    }
}
