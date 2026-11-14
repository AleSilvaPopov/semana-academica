<?php

namespace App\Http\Controllers\Portal;

use App\Auth\Sha256Hasher;
use App\Enums\TipoParticipante;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\CadastroRequest;
use App\Models\Participante;
use App\Services\ParticipanteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Autenticação do portal (guard "participante"): login por e-mail, cadastro de conta
 * e "Esqueci minha senha".
 *
 * O "Esqueci minha senha" não usa a tabela password_reset_tokens (que não existe):
 * o link é uma URL ASSINADA e com validade (URL::temporarySignedRoute) que carrega uma
 * "versão" derivada do hash atual da senha. Depois que a senha muda, o link deixa de valer.
 */
class AuthController extends Controller
{
    public function formularioLogin(): View
    {
        return view('portal.auth.login');
    }

    public function entrar(Request $request, Sha256Hasher $hasher): RedirectResponse
    {
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
        ]);

        $email = mb_strtolower(trim($dados['email']));
        $chave = 'login-participante:'.$email.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($chave)]),
            ]);
        }

        $participante = Participante::where('email', $email)->first();

        if (! $participante || ! $hasher->check($dados['senha'], $participante->senha_hash)) {
            RateLimiter::hit($chave);

            throw ValidationException::withMessages(['email' => 'E-mail ou senha incorretos.']);
        }

        // Regra de negócio: conta inativa ou bloqueada não entra.
        if (! $participante->podeEntrar()) {
            throw ValidationException::withMessages([
                'email' => 'Sua conta está '.mb_strtolower($participante->status_conta->rotulo()).'. Entre em contato com a organização do evento.',
            ]);
        }

        RateLimiter::clear($chave);
        Auth::guard('participante')->login($participante);
        $request->session()->regenerate();

        return redirect()->intended(route('portal.home'))
            ->with('sucesso', 'Olá, '.Str::before($participante->nome_completo, ' ').'!');
    }

    public function sair(Request $request): RedirectResponse
    {
        Auth::guard('participante')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('portal.home')->with('sucesso', 'Você saiu da sua conta.');
    }

    public function formularioCadastro(): View
    {
        return view('portal.auth.cadastro', ['tipos' => TipoParticipante::opcoes()]);
    }

    public function cadastrar(CadastroRequest $request, ParticipanteService $servico): RedirectResponse
    {
        $participante = $servico->criar($request->validated() + ['status_conta' => 'ativo']);

        Auth::guard('participante')->login($participante);
        $request->session()->regenerate();

        return redirect()->route('portal.programacao')
            ->with('sucesso', 'Conta criada! Agora você já pode se inscrever nas atividades.');
    }

    /* ------------------------ Esqueci minha senha ------------------------ */

    public function formularioEsqueci(): View
    {
        return view('portal.auth.esqueci');
    }

    public function enviarLink(Request $request): RedirectResponse
    {
        $dados = $request->validate(['email' => ['required', 'email']]);
        $email = mb_strtolower(trim($dados['email']));

        $chave = 'esqueci-senha:'.$request->ip();
        if (RateLimiter::tooManyAttempts($chave, 3)) {
            return back()->with('erro', 'Muitas solicitações. Aguarde alguns minutos.');
        }
        RateLimiter::hit($chave, 300);

        $participante = Participante::where('email', $email)->first();
        $mensagem = 'Se o e-mail estiver cadastrado, enviamos um link para redefinir a senha (válido por 60 minutos).';

        if (! $participante || ! $participante->podeEntrar()) {
            return back()->with('sucesso', $mensagem);
        }

        $link = URL::temporarySignedRoute('portal.senha.redefinir', now()->addMinutes(60), [
            'participante' => $participante->cpf,
            'v' => $this->versaoSenha($participante),
        ]);

        Mail::raw(
            "Olá, {$participante->nome_completo}!\n\nPara criar uma nova senha no portal da Semana Acadêmica, acesse:\n{$link}\n\nO link vale por 60 minutos. Se você não pediu, ignore este e-mail.",
            fn ($m) => $m->to($participante->email)->subject('Redefinição de senha — Semana Acadêmica')
        );

        $retorno = back()->with('sucesso', $mensagem);

        // Em desenvolvimento (MAIL_MAILER=log) o e-mail vai para o log; mostramos o link para facilitar o teste.
        if (config('app.debug') && config('mail.default') === 'log') {
            $retorno->with('link_dev', $link);
        }

        return $retorno;
    }

    public function formularioRedefinir(Request $request, Participante $participante): View
    {
        abort_unless(
            hash_equals($this->versaoSenha($participante), (string) $request->query('v')),
            403,
            'Este link já foi usado ou não é mais válido. Peça um novo.'
        );

        return view('portal.auth.redefinir', ['participante' => $participante]);
    }

    public function redefinir(Request $request, Participante $participante, Sha256Hasher $hasher): RedirectResponse
    {
        abort_unless(
            hash_equals($this->versaoSenha($participante), (string) $request->query('v')),
            403,
            'Este link já foi usado ou não é mais válido. Peça um novo.'
        );

        $dados = $request->validate(['senha' => ['required', 'string', 'min:6', 'max:100', 'confirmed']]);

        $participante->forceFill(['senha_hash' => $hasher->make($dados['senha'])])->save();

        return redirect()->route('portal.login')->with('sucesso', 'Senha redefinida. Entre com a nova senha.');
    }

    /** "Versão" da senha atual: muda sempre que a senha muda (invalida links antigos). */
    private function versaoSenha(Participante $participante): string
    {
        return substr(hash_hmac('sha256', $participante->senha_hash, (string) config('app.key')), 0, 24);
    }
}
