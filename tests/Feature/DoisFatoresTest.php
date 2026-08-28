<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class DoisFatoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_ativa_e_confirma_2fa(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1) ativa (gera o segredo) - sem exigir confirmacao de senha
        $this->post('/user/two-factor-authentication');
        $user->refresh();
        $this->assertNotNull($user->two_factor_secret, 'o segredo do 2FA deveria ter sido gerado');
        $this->assertNull($user->two_factor_confirmed_at, 'ainda nao confirmado');

        // 2) QR code disponivel
        $this->get('/user/two-factor-qr-code')->assertOk();

        // 3) confirma com um codigo TOTP valido gerado a partir do segredo
        $secret = decrypt($user->two_factor_secret);
        $code = (new Google2FA())->getCurrentOtp($secret);
        $this->post('/user/confirmed-two-factor-authentication', ['code' => $code])
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->two_factor_confirmed_at, 'o 2FA deveria estar confirmado/ativo');

        // 4) codigos de recuperacao existem
        $this->assertNotNull($user->two_factor_recovery_codes);
    }

    public function test_pagina_seguranca_abre_para_usuario_logado(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/seguranca')->assertOk()->assertSee('Verificação em duas etapas');
    }

    public function test_perfil_em_ingles_redireciona_para_seguranca(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/user/profile')->assertRedirect(route('seguranca'));
    }
}
