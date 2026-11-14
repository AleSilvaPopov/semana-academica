<?php

namespace App\Providers;

use App\Auth\Sha256Hasher;
use App\Auth\Sha256UserProvider;
use App\Models\Integrante;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Uma instância por requisição (guardam dados já carregados do banco).
        $this->app->scoped(EdicaoAtual::class);
        $this->app->scoped(Permissoes::class);

        $this->app->singleton(Sha256Hasher::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('pt_BR');
        Paginator::defaultView('components.paginacao');

        // Driver "sha256" usado pelos providers de config/auth.php (senhas SHA-256 do banco).
        Auth::provider('sha256', fn ($app, array $config) => new Sha256UserProvider(
            $app->make(Sha256Hasher::class),
            $config['model']
        ));

        $this->registrarGates();

        // Cabeçalho do painel: seletor de edição e papel do usuário na edição selecionada.
        View::composer(['components.layouts.admin', 'admin.*'], function ($view) {
            $edicao = app(EdicaoAtual::class);
            $usuario = auth('integrante')->user();

            $view->with('edicoesMenu', $edicao->todas());
            $view->with('edicaoAtual', $edicao->get());
            $view->with('papelNaEdicao', $usuario ? app(Permissoes::class)->papelNaEdicao($usuario, $edicao->id()) : null);
        });
    }

    /**
     * Gates das permissões que não pertencem a um único model
     * (as demais estão nas Policies em app/Policies).
     */
    private function registrarGates(): void
    {
        // Cadastros globais (cursos, locais, patrocinadores, participantes, ministrantes, integrantes):
        // presidente ou vice de QUALQUER edição.
        Gate::define('cadastros-globais', fn (Integrante $u) => app(Permissoes::class)->presideAlgumaEdicao($u));

        // Acesso total à edição (patrocínios, coordenadorias...): presidência da edição informada ou da selecionada.
        Gate::define('gerir-edicao', fn (Integrante $u, ?int $idComissao = null) => app(Permissoes::class)
            ->presideEdicao($u, $idComissao ?? app(EdicaoAtual::class)->id()));

        // Página /status: com APP_DEBUG=true ou para presidente/vice.
        Gate::define('ver-status', fn (?Integrante $u) => config('app.debug')
            || ($u && app(Permissoes::class)->presideAlgumaEdicao($u)));
    }
}
