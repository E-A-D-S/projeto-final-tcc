# Clínica ULBRA · Sistema de gestão de pacientes (versão modernizada 2026)

Sistema web para cadastro e acompanhamento de pacientes de uma clínica escola de Psicologia. O projeto nasceu como Trabalho de Conclusão de Curso na ULBRA em 2022 e, em 2026, passou por uma modernização completa.

> **Sobre esta branch.** O `main` continua com o trabalho original de 2022, preservado exatamente como foi entregue. Esta branch (`modernizacao-ia-2026`) é um novo ponto de ramificação com o projeto totalmente revisado, seguro e, pela primeira vez, publicado online.

> **Aplicação no ar:** https://clinica-ulbra.onrender.com

> Projeto acadêmico. Todos os dados da demonstração são **fictícios**.

---

## Como esta versão foi construída

Toda a modernização foi feita **sem escrita manual de código**, usando inteligência artificial como par de programação. Não reescrevi o sistema do zero: parti do meu próprio TCC de 2022 e fui melhorando cada parte com apoio de IA, revisando e validando cada mudança.

O foco foi transformar um trabalho acadêmico que só rodava na minha máquina em um sistema real, seguro, acessível e publicado, aplicando as boas práticas atuais de segurança do Brasil e do mundo.

---

## O que mudou em relação ao projeto de 2022

### Hospedagem (antes não existia)
O TCC original nunca chegou a ficar online. Agora ele roda 24 horas por dia, com custo zero:
- **Render** (Docker, plano gratuito) executa a aplicação. Ver `Dockerfile`, `docker-entrypoint.sh` e `render.yaml`.
- **Neon** (PostgreSQL gratuito) guarda os dados, com conexão via TLS.
- Ajustes feitos para o ambiente de produção: proxy reverso confiável (`TrustProxies`), forçar HTTPS, e sessões guardadas no banco (para o usuário não cair a cada reinício do servidor no plano gratuito).

### Segurança (auditoria OWASP Top 10 + NIST)
Fiz uma auditoria de segurança do projeto, do banco e da hospedagem seguindo OWASP Top 10 e as referências do NIST. Correções e proteções aplicadas:
- Validação server-side em todos os cadastros e edições.
- Proteção contra mass assignment (lista branca de campos em cada model).
- Limite de requisições (rate limit) e honeypot anti-spam no formulário público.
- Cadastro self-service desativado (`/register` fora do ar), evitando criação de contas indevidas.
- Controle de acesso por permissão nas rotas administrativas (visitante recebe 403 no painel e nos PDFs).
- Credenciais fora do código: tudo por variáveis de ambiente. O `.env` real com segredos foi removido do histórico exposto e trocado por exemplo.
- HTTPS forçado e sessões persistidas em banco.

### Proteção de dados e conformidade legal
- **LGPD:** aviso de consentimento no cadastro, explicando o uso dos dados e o direito de solicitar remoção. O envio só é aceito com o aceite marcado.
- **Retenção de prontuário (Resolução CFP 001/2009):** dados de paciente **nunca são apagados de verdade**. A ação de excluir virou **Arquivar** (soft delete): o registro sai da lista ativa, mas o histórico fica guardado e pode ser restaurado. Existe uma tela dedicada de arquivados.

### Novas funcionalidades
- **E-mail real funcionando:** ao cadastrar, o paciente recebe uma confirmação por e-mail e a clínica recebe um aviso do novo cadastro (SMTP da própria conta da clínica).
- **Histórico de atendimentos:** registro de cada sessão (data, hora, profissional e anotações), com impressão do histórico completo em PDF.
- **Login com Google** (OAuth), além do login por e-mail e senha, para acesso mais fácil da equipe.
- **Ficha e contrato do paciente em PDF**, reescritos para sair limpos e com a identidade visual correta.

### Experiência e acessibilidade
- **Interface redesenhada** do zero com um design system próprio (cores harmônicas, componentes consistentes).
- **Modo claro e modo escuro**, com a preferência salva.
- **Totalmente responsivo** (celular, tablet e computador).
- **Tudo em português**, com textos revisados (UX writing).
- **Acessibilidade:** navegação por teclado com foco visível, informação que não depende só de cor (apoio a daltônicos) e **VLibras** (tradução em Libras, ferramenta gratuita do governo) em todas as telas.
- CSS e JS servidos localmente, sem depender de CDNs externas.

---

## Acesso de demonstração

As credenciais aparecem na própria tela de login. O ambiente publicado roda em **modo demonstração**: o cadastro público de paciente funciona de verdade (inclusive o disparo de e-mail), mas as ações de escrita do admin ficam bloqueadas para preservar os dados de exemplo.

---

## Tecnologias

- PHP 8 e **Laravel 9**
- Jetstream, Fortify e Sanctum (autenticação)
- Laravel Socialite (login com Google)
- Livewire (componentes)
- spatie/laravel-permission (permissões)
- barryvdh/laravel-dompdf (PDF)
- Blade e design system próprio em CSS (com Tailwind e Vite disponíveis)
- Banco: SQLite (local) ou PostgreSQL (produção, no Neon)
- Deploy: Docker no Render

---

## Rodando localmente

Pré-requisitos: PHP 8, Composer e Node.

```bash
composer install
npm install

cp .env.example .env
touch database/database.sqlite     # usando SQLite local
php artisan key:generate

php artisan migrate --seed          # cria as tabelas e os dados ficticios
npm run build
php artisan serve
```

Abra http://127.0.0.1:8000. As credenciais de demonstração aparecem na tela de login.

---

## Deploy (Render + Neon, gratuito)

Já vem pronto para publicar:
- **Render** (Docker, plano free) roda a aplicação. Ver `Dockerfile` e `render.yaml`.
- **Neon** (PostgreSQL free) guarda os dados, informado via `DATABASE_URL`.
- No Render, defina no mínimo: `APP_KEY` (gere com `php artisan key:generate --show`), `DATABASE_URL` (do Neon), as variáveis de e-mail (`MAIL_*`) e, para o login com Google, `GOOGLE_CLIENT_ID` e `GOOGLE_CLIENT_SECRET`.

---

## Créditos

Trabalho de Conclusão de Curso (ULBRA, 2022), modernizado em 2026 com apoio de inteligência artificial. Uso educacional.
