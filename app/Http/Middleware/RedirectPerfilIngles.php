<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Impede que qualquer tela em ingles do Jetstream apareca:
// redireciona a pagina de perfil padrao para a nossa tela "Seguranca" em portugues.
// (Os endpoints de acao do 2FA em /user/two-factor-* continuam funcionando normalmente.)
class RedirectPerfilIngles
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get') && $request->path() === 'user/profile') {
            return redirect()->route('seguranca');
        }
        return $next($request);
    }
}
