<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A raiz redireciona para a tela de boas-vindas, que responde com sucesso.
     */
    public function test_the_application_returns_a_successful_response()
    {
        $this->get('/')->assertRedirect(route('paciente.homeScreen'));
        $this->get('/home')->assertOk();
    }
}
