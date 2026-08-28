@extends('layout')
@section('title','Segurança da conta')
@section('content')

<div class="section-head">
  <div>
    <h1>Segurança da conta</h1>
    <p class="muted">Proteja seu acesso &middot; <a class="link" href="{{ route('paciente.index') }}">Voltar ao painel</a></p>
  </div>
</div>

{{-- ================= Verificação em duas etapas (2FA) ================= --}}
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0 0 6px">Verificação em duas etapas</h2>
  <p class="muted" style="margin:0 0 14px">
    Adicione uma camada extra de segurança. Além da senha, será pedido um código do seu aplicativo autenticador
    (Google Authenticator, Microsoft Authenticator, Authy, entre outros).
  </p>

  {{-- estado atual --}}
  <div id="status-2fa" style="margin-bottom:14px">
    @if($twoFactorAtivo)
      <span class="tag" style="background:color-mix(in srgb, #16a34a 16%, transparent);color:#16a34a;border-color:color-mix(in srgb,#16a34a 30%,transparent)">Ativada</span>
    @elseif($twoFactorPendente)
      <span class="tag">Configuração em andamento</span>
    @else
      <span class="tag" style="background:color-mix(in srgb, var(--muted) 16%, transparent);color:var(--muted);border-color:var(--border)">Desativada</span>
    @endif
  </div>

  <div id="alerta-2fa"></div>

  {{-- botões conforme o estado --}}
  <div id="acoes-2fa">
    @if($twoFactorAtivo)
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="btn btn-soft" type="button" onclick="verCodigos()">Ver códigos de recuperação</button>
        <button class="btn btn-soft" type="button" onclick="desativar2fa()">Desativar</button>
      </div>
    @elseif(!$twoFactorPendente)
      <button class="btn btn-primary" type="button" onclick="ativar2fa()">Ativar verificação em duas etapas</button>
    @endif
  </div>

  {{-- área de configuração (QR + código) --}}
  <div id="setup-2fa" style="display:{{ $twoFactorPendente ? 'block' : 'none' }};margin-top:16px">
    <p class="muted" style="margin:0 0 10px">1. Abra seu aplicativo autenticador e escaneie o QR code abaixo (ou digite a chave manualmente).</p>
    <div id="qr-2fa" style="background:#fff;display:inline-block;padding:12px;border-radius:12px"></div>
    <p class="muted" style="margin:10px 0 4px">Chave manual:</p>
    <code id="chave-2fa" style="user-select:all;word-break:break-all"></code>

    <div class="field" style="margin-top:16px;max-width:260px">
      <label for="codigo-2fa">2. Digite o código de 6 dígitos do app</label>
      <input class="input" id="codigo-2fa" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000">
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <button class="btn btn-primary" type="button" onclick="confirmar2fa()">Confirmar e ativar</button>
      <button class="btn btn-ghost" type="button" onclick="cancelar2fa()">Cancelar</button>
    </div>
  </div>

  {{-- códigos de recuperação --}}
  <div id="recuperacao-2fa" style="display:none;margin-top:16px">
    <p class="muted" style="margin:0 0 8px">
      Guarde estes códigos de recuperação em um lugar seguro. Cada um pode ser usado uma vez caso você perca o acesso ao aplicativo.
    </p>
    <ul id="lista-codigos" style="font-family:monospace;line-height:1.9"></ul>
  </div>
</div>

{{-- ================= Alterar senha ================= --}}
<div class="card">
  <h2 style="margin:0 0 6px">Alterar senha</h2>
  <p class="muted" style="margin:0 0 14px">
    Use uma senha forte: no mínimo 10 caracteres, com maiúsculas, minúsculas, números e símbolos.
    Quem entra pelo Google e não tem senha pode defini-la pela opção <a class="link" href="{{ route('password.request') }}">Esqueci a senha</a>.
  </p>

  <div id="alerta-senha"></div>

  <form id="form-senha" onsubmit="return alterarSenha(event)">
    <div class="form-grid">
      <div class="field">
        <label for="current_password">Senha atual</label>
        <input class="input" id="current_password" type="password" autocomplete="current-password" required>
      </div>
    </div>
    <div class="form-grid">
      <div class="field">
        <label for="password">Nova senha</label>
        <input class="input" id="password" type="password" autocomplete="new-password" required>
      </div>
      <div class="field">
        <label for="password_confirmation">Confirmar nova senha</label>
        <input class="input" id="password_confirmation" type="password" autocomplete="new-password" required>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Salvar nova senha</button>
  </form>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const cabecalhos = { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' };

function aviso(id, texto, tipo) {
  document.getElementById(id).innerHTML =
    '<div class="alert alert-' + (tipo || 'danger') + '">' + texto + '</div>';
}

async function ativar2fa() {
  try {
    const r = await fetch('/user/two-factor-authentication', { method: 'POST', headers: cabecalhos });
    if (!r.ok) throw new Error();
    await carregarQr();
    document.getElementById('acoes-2fa').style.display = 'none';
    document.getElementById('setup-2fa').style.display = 'block';
  } catch (e) {
    aviso('alerta-2fa', 'Não foi possível iniciar a ativação. Tente novamente.');
  }
}

async function carregarQr() {
  const qr = await (await fetch('/user/two-factor-qr-code', { headers: { 'Accept': 'application/json' } })).json();
  document.getElementById('qr-2fa').innerHTML = qr.svg;
  const chave = await (await fetch('/user/two-factor-secret-key', { headers: { 'Accept': 'application/json' } })).json();
  document.getElementById('chave-2fa').textContent = chave.secretKey;
}

async function confirmar2fa() {
  const code = document.getElementById('codigo-2fa').value.trim();
  if (!/^[0-9]{6}$/.test(code)) { aviso('alerta-2fa', 'Digite o código de 6 dígitos do aplicativo.'); return; }
  const r = await fetch('/user/confirmed-two-factor-authentication', {
    method: 'POST', headers: cabecalhos, body: JSON.stringify({ code })
  });
  if (r.ok) {
    location.reload();
  } else {
    aviso('alerta-2fa', 'Código inválido. Verifique o horário do celular e tente o código atual do app.');
  }
}

async function cancelar2fa() {
  await fetch('/user/two-factor-authentication', { method: 'DELETE', headers: cabecalhos });
  location.reload();
}

async function desativar2fa() {
  if (!confirm('Desativar a verificação em duas etapas desta conta?')) return;
  await fetch('/user/two-factor-authentication', { method: 'DELETE', headers: cabecalhos });
  location.reload();
}

async function verCodigos() {
  const codigos = await (await fetch('/user/two-factor-recovery-codes', { headers: { 'Accept': 'application/json' } })).json();
  document.getElementById('lista-codigos').innerHTML = codigos.map(c => '<li>' + c + '</li>').join('');
  document.getElementById('recuperacao-2fa').style.display = 'block';
}

async function alterarSenha(ev) {
  ev.preventDefault();
  const r = await fetch('/user/password', {
    method: 'PUT', headers: cabecalhos,
    body: JSON.stringify({
      current_password: document.getElementById('current_password').value,
      password: document.getElementById('password').value,
      password_confirmation: document.getElementById('password_confirmation').value,
    })
  });
  if (r.ok) {
    aviso('alerta-senha', 'Senha alterada com sucesso.', 'success');
    document.getElementById('form-senha').reset();
  } else {
    let msg = 'Não foi possível alterar a senha. Verifique os dados.';
    try {
      const j = await r.json();
      if (j.errors) msg = Object.values(j.errors).flat().join('<br>');
    } catch (e) {}
    aviso('alerta-senha', msg);
  }
  return false;
}

// se a configuração estava pendente, já carrega o QR ao abrir a página
@if($twoFactorPendente)
carregarQr();
@endif
</script>

@endsection
