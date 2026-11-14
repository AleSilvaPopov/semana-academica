<?php

namespace App\Providers;

use App\Auth\Sha256Hasher;
use App\Auth\Sha256UserProvider;
use App\Services\EdicaoAtual;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Uma instância por requisição (guarda a edição selecionada já carregada).
        $this->app->scoped(EdicaoAtual::class);

        $this->app->singleton(Sha256Hasher::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('pt_BR');

        // Driver "sha256" usado pelos providers de config/auth.php (senhas SHA-256 do banco).
        Auth::provider('sha256', fn ($app, array $config) => new Sha256UserProvider(
            $app->make(Sha256Hasher::class),
            $config['model']
        ));

        // Seletor de edição no cabeçalho do painel.
        View::composer('components.layouts.admin', function ($view) {
            $edicao = app(EdicaoAtual::class);
            $view->with('edicoesMenu', $edicao->todas());
            $view->with('edicaoAtual', $edicao->get());
        });
    }
}
