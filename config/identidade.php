<?php

/*
|--------------------------------------------------------------------------
| Identidade visual e conteúdo fixo da edição
|--------------------------------------------------------------------------
|
| ESTE É O ÚNICO ARQUIVO QUE PRECISA MUDAR A CADA ANO.
|
| Todo o site (portal, painel, login, páginas de erro e certificado) lê
| daqui o nome, as cores, as fontes, o fundo e o logo. A paleta completa
| (tons de fundo, bordas, textos, badges) é calculada pelo CSS a partir
| das cores base — não é preciso escolher cada tom.
|
| O que vem do BANCO (não precisa mexer aqui): datas e local da edição,
| palestras, minicursos, programação dia a dia, locais e patrocinadores.
| O que vem DAQUI: textos de marca, maratona e pontos fixos do mapa.
|
| Depois de alterar, rode:  php artisan config:clear
| (não precisa "npm run build": cores e fontes vão direto no HTML).
|
| Tema 2026: "SIC//2026" — preto, carmesim e neon, grade de circuito,
| Chakra Petch + Inter + JetBrains Mono (design tokens do UI kit).
|
*/

return [

    // Textos de marca (cabeçalho, rodapé, título das abas, login).
    'nome' => 'Semana Integrada de Computação',
    'sigla' => 'SIC',
    'ano' => 2026,
    'subtitulo' => 'UEMS · UFGD',
    'descricao' => 'Palestras, minicursos e maratona de programação reunindo estudantes, professores e profissionais de Computação da UEMS e da UFGD.',

    // Faixa "Realização" da página inicial (nomes curtos) e nomes completos (certificado/rodapé).
    'realizacao' => ['UEMS', 'UFGD'],
    'instituicoes' => [
        'Universidade Estadual de Mato Grosso do Sul',
        'Universidade Federal da Grande Dourados',
    ],

    /*
    | Cores (hexadecimal) — tokens do UI kit.
    | - destaque:   carmesim dos botões de ação (sic-red)
    | - neon:       bordas acesas, brilho e títulos grandes (sic-neon)
    | - fundo:      fundo da página (sic-bg)
    | - superficie: fundo de cartões e tabelas (sic-surface)
    | - texto:      texto principal (sic-text)
    | Os demais tons (texto suave, bordas, vermelho legível #ff4d4d…) são
    | derivados. Tema claro? Use fundo claro e texto escuro.
    */
    'cores' => [
        'destaque' => '#e60000',
        'neon' => '#ff1a1a',
        'fundo' => '#050505',
        'superficie' => '#0a0a0a',
        'texto' => '#f5f5f5',
    ],

    /*
    | Fontes do Google Fonts. Os pesos vêm depois de ":" (formato da URL do
    | fonts.google.com). Sem ":" carrega só o peso normal.
    | - titulo / display: títulos, botões e números (font-display)
    | - corpo:            parágrafos e interface (font-sans)
    | - mono:             rótulos, horários e código (font-mono)
    */
    'fontes' => [
        'titulo' => 'Chakra Petch:wght@500;600;700',
        'display' => 'Chakra Petch:wght@500;600;700',
        'corpo' => 'Inter:wght@400;500;600;700',
        'mono' => 'JetBrains Mono:wght@400;500;700',
    ],

    /*
    | Fundo da página: 'grade' (grade de circuito), 'circuitos', 'rede' ou 'nenhum'.
    | 'brilho' (0 a 1): intensidade do neon no fundo.
    */
    'fundo' => 'grade',
    'brilho' => 0.7,

    /*
    | Logo: null usa o ícone em SVG de resources/views/identidade/logo.blade.php
    | (acompanha a cor neon). Para uma imagem, coloque em public/images/ e
    | informe o caminho, ex.: 'images/logo-2027.png'.
    */
    'logo' => null,

    // Selo do topo da página inicial (null para ocultar). Some sozinho depois do evento.
    'chamada' => 'Inscrições abertas!',

    /*
    | Seção "Maratona de programação" da página inicial (null para ocultar).
    | Troque os [colchetes] pelos dados do ano.
    */
    'maratona' => [
        'descricao' => 'Resolva problemas algorítmicos em equipe, contra o relógio. Escolha a sua linguagem e prepare o seu template.',
        'linguagens' => ['C++', 'Python', 'Java'],
        'equipes' => 'Até [N] integrantes por equipe, um computador por time.',
        'duracao' => '[N] horas de prova, com placar ao vivo e juiz online.',
        'premiacao' => '[Premiação para as equipes mais bem colocadas]',
        // Código exibido na janela de editor (A + B, o "hello world" das maratonas).
        'codigo' => [
            'C++' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    long long a, b;\n    cin >> a >> b;\n    cout << a + b << \"\\n\";\n}",
            'Python' => "import sys\n\ndef main():\n    a, b = map(int, sys.stdin.read().split())\n    print(a + b)\n\nmain()",
            'Java' => "import java.util.*;\n\npublic class Main {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        long a = in.nextLong(), b = in.nextLong();\n        System.out.println(a + b);\n    }\n}",
        ],
    ],

    /*
    | Seção "Mapa do evento".
    | - endereco: null usa o local da edição (banco).
    | - imagens:  mapas do visualizador, na ordem. Cada item usa UM destes:
    |     'view'    => desenho em SVG (Blade) em resources/views/mapas/<ano>/ — nítido em qualquer zoom;
    |     'arquivo' => imagem em public/images/mapas/<ano>/ (png/jpg/svg).
    |   Lista vazia = volta ao mapa esquemático com 'pontos'.
    | - pontos:   só para o mapa esquemático (até 4). null = "Credenciamento"
    |             + os locais das atividades da edição (banco).
    */
    'mapa' => [
        'endereco' => null,
        'imagens' => [
            ['view' => 'mapas.2026.campus-blocos', 'titulo' => 'Campus · blocos', 'detalhe' => 'FACET, Bloco Multiuso e Bloco C (UFGD) · Bloco G (UEMS)'],
            ['view' => 'mapas.2026.campus-auditorios', 'titulo' => 'Campus · auditórios', 'detalhe' => 'Auditório UFGD · Bloco A e Auditório Central (UEMS)'],
            ['view' => 'mapas.2026.uems-bloco-a-terreo', 'titulo' => 'Bloco A · UEMS', 'detalhe' => 'Térreo · Auditório do Bloco A'],
            ['view' => 'mapas.2026.ufgd-bloco-c-1-andar', 'titulo' => 'Bloco C · UFGD', 'detalhe' => 'Primeiro andar · Sala C-208'],
            ['view' => 'mapas.2026.uems-bloco-g-terreo', 'titulo' => 'Bloco G · UEMS', 'detalhe' => 'Térreo · Auditório do Bloco G, Lab I, Lab II e Lab III'],
            ['view' => 'mapas.2026.ufgd-facet-1-andar-labs', 'titulo' => 'FACET · UFGD · labs', 'detalhe' => 'Primeiro andar · Lab I, Lab de Redes e Lab II'],
            ['view' => 'mapas.2026.ufgd-facet-1-andar-eletronica-robotica', 'titulo' => 'FACET · UFGD · eletrônica e robótica', 'detalhe' => 'Primeiro andar · Lab de Eletrônica e Lab de Robótica'],
        ],
        'pontos' => null,
    ],
];
