{{--
    Certificado de participação imprimível (A4 paisagem, @media print).
    Usado pelo painel (admin.certificados.emitir) e pelo portal (portal.certificados.show).
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificado · {{ $participante->nome_completo }} · {{ $atividade->nome }}</title>
    <style>
        /* Cor de destaque da edição (config/identidade.php), escurecida para impressão em papel branco */
        :root { --c: {{ preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) config('identidade.cores.destaque')) ? config('identidade.cores.destaque') : '#e3262f' }}; --c-escura: color-mix(in srgb, var(--c) 70%, #000); --c-clara: color-mix(in srgb, var(--c) 40%, #fff); }
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #e4e4e7; font-family: Georgia, 'Times New Roman', serif; color: #18181b; }
        .barra { display: flex; gap: 8px; justify-content: center; padding: 16px; font-family: system-ui, sans-serif; }
        .barra a, .barra button { border: 1px solid #d4d4d8; background: #fff; color: #27272a; padding: 8px 16px; border-radius: 8px; font-size: 14px; cursor: pointer; text-decoration: none; }
        .barra button { background: var(--c); border-color: var(--c); color: #fff; }
        .pagina { width: 297mm; height: 210mm; margin: 0 auto 24px; background: #fff; padding: 14mm; box-shadow: 0 10px 30px rgba(0,0,0,.12); }
        .moldura { height: 100%; border: 3px solid var(--c-escura); outline: 1px solid var(--c-clara); outline-offset: -9px; padding: 16mm 22mm; display: flex; flex-direction: column; text-align: center; position: relative; }
        .topo { font-family: system-ui, sans-serif; font-size: 12px; letter-spacing: .25em; text-transform: uppercase; color: var(--c-escura); }
        h1 { font-size: 44px; margin: 10mm 0 2mm; letter-spacing: .08em; color: #18181b; font-weight: normal; }
        .sub { font-family: system-ui, sans-serif; font-size: 13px; color: #71717a; letter-spacing: .15em; text-transform: uppercase; }
        .texto { margin-top: 12mm; font-size: 19px; line-height: 1.8; }
        .nome { font-size: 30px; font-weight: bold; color: #18181b; display: block; margin: 3mm 0; }
        .rodape { margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; font-family: system-ui, sans-serif; font-size: 12px; color: #52525b; }
        .assinatura { width: 70mm; border-top: 1px solid #52525b; padding-top: 2mm; }
        .codigo { text-align: left; }
        @media screen and (max-width: 1100px) { .pagina { transform: scale(.5); transform-origin: top center; margin-bottom: -100mm; } }
        @media print {
            body { background: #fff; }
            .barra { display: none; }
            .pagina { margin: 0; box-shadow: none; transform: none !important; }
        }
    </style>
</head>
<body>
    <div class="barra">
        @isset($voltar)<a href="{{ $voltar }}">← Voltar</a>@endisset
        <button type="button" onclick="window.print()">Imprimir / salvar em PDF</button>
    </div>

    <div class="pagina">
        <div class="moldura">
            <div class="topo">Universidade Federal da Grande Dourados · Faculdade de Ciências Exatas e Tecnologia</div>
            <h1>CERTIFICADO</h1>
            <div class="sub">de participação</div>

            <p class="texto">
                Certificamos que
                <span class="nome">{{ $participante->nome_completo }}</span>
                CPF {{ mascara_cpf($participante->cpf) }}, participou {{ $atividade->tipo === \App\Enums\TipoAtividade::Palestra ? 'da palestra' : ($atividade->tipo === \App\Enums\TipoAtividade::Minicurso ? 'do minicurso' : 'do workshop') }}
                <strong>“{{ $atividade->nome }}”</strong>, realizad{{ $atividade->tipo === \App\Enums\TipoAtividade::Palestra ? 'a' : 'o' }} em {{ formata_data($atividade->data_realizacao) }},
                com carga horária de <strong>{{ $cargaHoraria }}</strong>, durante a
                <strong>{{ $edicao->edicao }}ª edição da {{ $edicao->nome_evento }}</strong> ({{ $edicao->periodo }}).
            </p>

            <div class="rodape">
                <div class="codigo">
                    Código de verificação: <strong>{{ $codigo }}</strong><br>
                    Emitido em {{ formata_data(now()) }}
                </div>
                <div class="assinatura">Comissão Organizadora<br>{{ $edicao->local_sede }}</div>
            </div>
        </div>
    </div>
</body>
</html>
