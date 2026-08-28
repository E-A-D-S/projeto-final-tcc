# Clínica ULBRA · Sistema de gestão de pacientes (versão modernizada 2026)

Sistema web para cadastro e acompanhamento de pacientes de uma clínica escola de Psicologia. O projeto nasceu como Trabalho de Conclusão de Curso na ULBRA em 2022 e, em 2026, passou por uma modernização completa.

> **Sobre esta branch.** O `main` continua com o trabalho original de 2022, preservado exatamente como foi entregue. Esta branch (`modernizacao-ia-2026`) é um novo ponto de ramificação com o projeto totalmente revisado, seguro, acessível e com hospedagem permanente.

> **Aplicação no ar:** https://clinica-ulbra.onrender.com
>
> Projeto acadêmico. Todos os dados de demonstração são **fictícios** e as credenciais de acesso de teste aparecem na própria tela de login.

---

## Como esta versão foi construída

Toda a modernização foi feita **sem escrita manual de código**, usando inteligência artificial como par de programação. Não reescrevi o sistema do zero: parti do meu próprio TCC de 2022 e fui melhorando cada parte com apoio de IA, revisando, testando e validando cada mudança.

O objetivo foi transformar um trabalho acadêmico, que rodava localmente, em um sistema real, seguro, acessível e publicado, aplicando as boas práticas atuais de segurança do Brasil e do mundo.

---

## Antes (2022) x Agora (2026)

| Tema | Antes | Agora |
|---|---|---|
| Hospedagem | Local; na apresentação, um túnel temporário (ngrok + QR code) | Permanente e gratuita (Render + Neon), 24h no ar |
| Acesso | Um único nível de admin | Papéis Dono / Tutor / Estagiário (menor privilégio) |
| Senhas | Hash padrão | **Argon2id** + política forte + checagem de vazamento |
| Duas etapas (2FA) | Não tinha | Ativação por QR (Google/Microsoft Authenticator) |
| Exclusão de dados | Apagava de fato | Arquiva (nunca apaga) por guarda legal de prontuário |
| LGPD | Não tratava | Consentimento + Política de Privacidade + direitos |
| E-mail | Não enviava | Confirmação ao paciente + aviso à clínica |
| Histórico clínico | Não tinha | Registro de sessões + impressão em PDF |
| Acessibilidade | Não tinha | VLibras (Libras), teclado, apoio a daltônicos |
| Idioma | Partes em inglês (Jetstream) | 100% em português |
| Aparência | Padrão | Design próprio, claro/escuro, responsivo |
| Segurança web | Básica | Cabeçalhos de segurança + CSP + auditoria |
| Testes | Não tinha | Testes automatizados de acesso, 2FA e senha |

---

## O que foi feito e por quê

### Hospedagem permanente (antes não existia)
Na época do TCC, o sistema rodava localmente e, para a apresentação, eu o expunha por um túnel temporário (ngrok) via QR code, o que só funcionava com a minha máquina ligada. **Por quê mudar:** um sistema precisa estar sempre disponível. Agora roda 24h com custo zero em **Render** (Docker) + **Neon** (PostgreSQL com TLS), com HTTPS forçado e sessões guardadas no banco para não cair a cada reinício.

### Controle de acesso por papéis (RBAC, menor privilégio)
Antes existia só "admin". **Por quê mudar:** numa clínica escola, cada pessoa deve enxergar apenas o necessário. Agora há três papéis:

| Ação | Dono | Tutor | Estagiário |
|---|:---:|:---:|:---:|
| Cadastrar, ver, imprimir | Sim | Sim | Sim |
| Registrar atendimento | Sim | Sim | Sim (auditado) |
| Editar paciente | Sim | Sim | Não |
| Arquivar / Restaurar | Sim | Não | Não |
| Gerenciar equipe | Sim (todos) | Sim (só estagiários) | Não |
| Ver auditoria | Sim | Sim (seus estagiários) | Não |

O acesso é liberado por e-mail: o dono cria tutores e o tutor cria estagiários; a pessoa entra com o Google e já recebe o papel certo.

### Senhas e autenticação
- **Argon2id** (recomendação nº 1 da OWASP) para o hash das senhas, com política forte (mínimo 10 caracteres, maiúsculas, minúsculas, números e símbolos) e verificação de vazamento (HaveIBeenPwned). **Por quê:** proteger a credencial mesmo em caso de incidente.
- **Login com Google** (OAuth) além do e-mail e senha, para acesso mais simples da equipe.
- **Recuperação de senha** funcional e segura (não revela se um e-mail existe).
- **Verificação em duas etapas (2FA)** por aplicativo autenticador, com convite para ativar após o login.

### Proteção de dados e conformidade legal (LGPD)
- **Consentimento** no cadastro e uma **Política de Privacidade** dedicada.
- **Retenção de prontuário (Resolução CFP 001/2009):** dados de paciente **nunca são apagados**. A exclusão virou **Arquivar** (o registro sai da lista ativa, mas o histórico fica guardado e pode ser restaurado).
- **Modo demonstração em sandbox:** a conta pública de demo mostra o painel completo, mas **só enxerga dados fictícios**, nunca clientes, equipe ou atividade reais.

### Funcionalidades novas
- **E-mail real:** confirmação ao paciente e aviso à clínica a cada novo cadastro.
- **Histórico de atendimentos:** registro de cada sessão (data, hora, profissional e anotações) com impressão em PDF.
- **Ficha e contrato do paciente em PDF**, reescritos para sair limpos e com a identidade visual correta.

### Experiência e acessibilidade
- **Interface redesenhada** com design próprio, **modo claro e escuro** e **totalmente responsiva**.
- **100% em português** (inclusive telas que vinham em inglês do Jetstream).
- **Acessibilidade:** VLibras (tradução em Libras, ferramenta gratuita do governo), navegação por teclado com foco visível, "pular para o conteúdo", e informação que não depende só de cor (apoio a daltônicos).

### Segurança web
- **Cabeçalhos de segurança** (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS) e **Content-Security-Policy** compatível com o VLibras.
- Validação no servidor, proteção contra mass assignment, rate limit e honeypot anti-spam, CSRF, saída escapada (sem XSS) e Eloquent (sem SQL injection).
- **Trilha de auditoria:** quem fez o quê, em qual paciente, quando e de qual IP.
- Cadastro self-service desativado e **conteúdo não indexável** por buscadores (o projeto ainda não está em uso real).

### Testes automatizados
Cobrem o controle de acesso por papel, o isolamento do sandbox de demonstração, a recuperação de senha e o fluxo de 2FA.

---

## Tecnologias

- PHP 8 e **Laravel 9**
- Jetstream, Fortify e Sanctum (autenticação)
- Laravel Socialite (login com Google)
- Livewire, spatie/laravel-permission (papéis e permissões)
- barryvdh/laravel-dompdf (PDF)
- Blade e design system próprio em CSS
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
- **Neon** (PostgreSQL free) guarda os dados, via `DATABASE_URL`.
- No Render, defina no mínimo: `APP_KEY`, `DATABASE_URL`, as variáveis de e-mail (`MAIL_*`) e, para o login com Google, `GOOGLE_CLIENT_ID` e `GOOGLE_CLIENT_SECRET`.

---

## Créditos

Trabalho de Conclusão de Curso (ULBRA, 2022), modernizado em 2026 com apoio de inteligência artificial. Uso educacional.
