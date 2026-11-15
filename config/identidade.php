<?php

/*
|--------------------------------------------------------------------------
| Identidade visual da edição
|--------------------------------------------------------------------------
|
| ESTE É O ÚNICO ARQUIVO QUE PRECISA MUDAR A CADA ANO.
|
| Todo o site (portal, painel, login, páginas de erro e certificado) lê
| daqui o nome, as cores, as fontes, o fundo e o logo. A paleta completa
| (tons claros, escuros, bordas, textos) é calculada automaticamente pelo
| CSS a partir das três cores base — não é preciso escolher 11 tons.
|
| Depois de alterar, rode:  php artisan config:clear
| (não precisa rodar "npm run build": as cores e fontes são injetadas
| direto no HTML).
|
| Tema 2026: preto + vermelho neon, circuitos, tipografia Cinzel
| (inspirado nas artes da Semana Integrada de Computação 2026).
|
*/

return [

    // Textos de marca exibidos no cabeçalho, rodapé e título das abas.
    'nome' => 'Semana Integrada de Computação',
    'sigla' => 'SIC',
    'ano' => 2026,
    'subtitulo' => 'UFGD · UEMS',
    'instituicoes' => [
        'Universidade Federal da Grande Dourados',
        'Universidade Estadual de Mato Grosso do Sul',
    ],

    /*
    | Cores base (hexadecimal).
    | - destaque: cor principal (botões, links, brilho neon, ícones)
    | - fundo:    cor de fundo da página
    | - texto:    cor do texto principal
    | Tema claro? Basta usar fundo claro e texto escuro: a paleta se adapta.
    */
    'cores' => [
        'destaque' => '#e3262f',
        'fundo' => '#070708',
        'texto' => '#f2f0f0',
    ],

    /*
    | Fontes do Google Fonts (nome exato da família).
    | - titulo:  títulos das páginas e seções
    | - display: título grande da página inicial (pode ser igual a "titulo")
    | - corpo:   textos, tabelas e formulários
    | - mono:    rótulos em caixa alta ("INSCRIÇÕES ABERTAS", datas)
    | Os pesos vêm depois de ":" no formato do Google Fonts (copie da URL
    | gerada em fonts.google.com). Sem ":" carrega só o peso normal.
    */
    'fontes' => [
        'titulo' => 'Cinzel:wght@500;600;700',
        'display' => 'Cinzel Decorative:wght@400;700',
        'corpo' => 'Inter:wght@400;500;600;700',
        'mono' => 'Space Mono:wght@400;700',
    ],

    /*
    | Desenho de fundo da página: 'circuitos', 'rede' ou 'nenhum'.
    | 'brilho' (0 a 1) controla a intensidade do brilho neon do fundo.
    */
    'fundo' => 'circuitos',
    'brilho' => 0.6,

    /*
    | Logo. null = usa o desenho em SVG de resources/views/identidade/logo.blade.php
    | (a nuvem, que já acompanha a cor de destaque). Para usar uma imagem,
    | coloque o arquivo em public/images/ e informe o caminho, ex.: 'images/logo-2027.png'.
    */
    'logo' => null,

    // Chamada exibida em destaque na página inicial (null para ocultar).
    'chamada' => 'Inscrições abertas!',
];
