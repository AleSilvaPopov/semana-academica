import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';

Alpine.plugin(mask);

/**
 * Máscara dinâmica de telefone:
 * 10 dígitos -> (67) 3353-8999 | 11 dígitos -> (67) 99999-9999.
 * Números estrangeiros (12 a 15 dígitos, ex.: Paraguai 595...) ficam só com dígitos.
 * O back-end sempre remove a máscara antes de salvar (VARCHAR(15) só com dígitos).
 */
window.mascaraTelefone = (valor) => {
    const digitos = valor.replace(/\D/g, '');
    if (digitos.length > 11) {
        return '999999999999999';
    }
    return digitos.length <= 10 ? '(99) 9999-99999' : '(99) 99999-9999';
};

const semAcento = (texto) =>
    String(texto)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '');

/**
 * Lista dinâmica usada pelos atributos multivalorados
 * (telefones, e-mails, certificados, equipamentos).
 */
Alpine.data('listaDinamica', (itensIniciais = [], minimo = 0) => ({
    itens: itensIniciais.map((v) => ({ valor: v ?? '' })),
    init() {
        while (this.itens.length < minimo) {
            this.itens.push({ valor: '' });
        }
    },
    adicionar() {
        this.itens.push({ valor: '' });
        this.$nextTick(() => {
            const campos = this.$root.querySelectorAll('[data-item]');
            campos[campos.length - 1]?.focus();
        });
    },
    remover(indice) {
        this.itens.splice(indice, 1);
    },
}));

/**
 * Seleção múltipla com busca (responsáveis da tarefa, ministrantes da atividade).
 * Gera um <input type="hidden" name="campo[]"> para cada item escolhido.
 */
Alpine.data('multiSelect', (opcoes = [], selecionados = []) => ({
    opcoes,
    selecionados: selecionados.map(String),
    busca: '',
    aberto: false,
    get filtradas() {
        const termo = semAcento(this.busca);
        return this.opcoes.filter(
            (o) => !this.selecionados.includes(String(o.valor)) && semAcento(o.rotulo).includes(termo),
        );
    },
    rotuloDe(valor) {
        return this.opcoes.find((o) => String(o.valor) === String(valor))?.rotulo ?? valor;
    },
    adicionar(valor) {
        this.selecionados.push(String(valor));
        this.busca = '';
    },
    remover(valor) {
        this.selecionados = this.selecionados.filter((v) => v !== String(valor));
    },
}));

/**
 * Credenciamento: marcar/desmarcar todos os checkboxes de presença.
 */
Alpine.data('presencaLote', () => ({
    marcarTodos(valor) {
        this.$root.querySelectorAll('input[type=checkbox][data-presenca]').forEach((c) => (c.checked = valor));
    },
}));

window.Alpine = Alpine;
Alpine.start();
