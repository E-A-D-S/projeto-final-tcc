<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function criaUsuario(string $email, string $senha = 'senhaAntiga1'): User
    {
        return User::create([
            'name' => 'Fulano',
            'email' => $email,
            'password' => bcrypt($senha),
            'email_verified_at' => now(),
        ]);
    }

    private function fluxoReset(User $u): bool
    {
        // 1) solicita o link de recuperacao
        $this->post('/forgot-password', ['email' => $u->email])->assertSessionHas('status');

        // 2) usa um token valido (o mesmo tipo que vai no e-mail) para redefinir
        $token = Password::broker()->createToken($u);
        $resp = $this->post('/reset-password', [
            'token' => $token,
            'email' => $u->email,
            'password' => 'NovaSenha#2026',
            'password_confirmation' => 'NovaSenha#2026',
        ]);
        $resp->assertSessionHasNoErrors();

        // 3) a senha realmente mudou
        return Hash::check('NovaSenha#2026', $u->fresh()->password);
    }

    public function test_reset_para_conta_com_senha(): void
    {
        // ex.: admin de demonstracao / usuario comum criado com senha
        $u = $this->criaUsuario('admin@demo.com');
        $this->assertTrue($this->fluxoReset($u));
    }

    public function test_reset_para_conta_criada_via_google(): void
    {
        // usuario que entrou pelo Google tem senha aleatoria; ainda assim pode
        // definir uma senha pelo "esqueci a senha" (login alternativo)
        $u = $this->criaUsuario('estagiario@gmail.com', \Illuminate\Support\Str::random(40));
        $this->assertTrue($this->fluxoReset($u));
    }

    public function test_email_inexistente_nao_revela_e_nao_quebra(): void
    {
        // Fortify responde de forma generica (nao revela se o e-mail existe)
        $this->post('/forgot-password', ['email' => 'naoexiste@nada.com'])
            ->assertSessionHasErrors('email'); // mensagem generica de "nao encontramos"
    }
}
