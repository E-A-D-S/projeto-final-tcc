@extends('layout')
@section('title','Política de Privacidade')
@section('content')

<div class="section-head">
  <div>
    <h1>Política de Privacidade</h1>
    <p class="muted">Como tratamos e protegemos seus dados (LGPD) &middot; <a class="link" href="{{ route('paciente.homeScreen') }}">Voltar</a></p>
  </div>
</div>

<div class="card" style="max-width:820px;line-height:1.7">
  <p class="muted" style="margin-top:0">Última atualização: {{ now()->format('d/m/Y') }}. Projeto acadêmico da Clínica Escola de Psicologia da ULBRA.</p>

  <h2>1. Quem trata os dados</h2>
  <p>A Clínica Escola de Psicologia da ULBRA de São Jerônimo é a responsável pelo tratamento dos dados informados neste sistema. Contato: <b>clinicaescolasj@gmail.com</b>.</p>

  <h2>2. Quais dados coletamos</h2>
  <p>Coletamos apenas os dados necessários ao atendimento: nome, data de nascimento, documentos (RG e CPF), contato (telefone e e-mail), endereço, dados do responsável (quando menor de idade) e informações sobre o motivo e o histórico do atendimento.</p>

  <h2>3. Para que usamos</h2>
  <p>Os dados são usados exclusivamente para agendar e realizar o atendimento psicológico, registrar o histórico clínico e permitir o contato com você. Não vendemos nem compartilhamos seus dados com terceiros para fins comerciais.</p>

  <h2>4. Base legal e sigilo</h2>
  <p>O tratamento se apoia no seu consentimento e na execução do atendimento, sob sigilo profissional. O acesso aos dados é restrito à equipe autorizada (dono, tutores e estagiários), com registro de auditoria das ações realizadas.</p>

  <h2>5. Por quanto tempo guardamos</h2>
  <p>Por obrigação legal de guarda de prontuário (Resolução CFP nº 001/2009), o histórico clínico é mantido em arquivo por, no mínimo, o prazo previsto na norma. Ao pedir a remoção, seu cadastro sai da lista de atendimento ativa (arquivamento), mas o prontuário permanece guardado com acesso restrito, conforme a lei.</p>

  <h2>6. Seus direitos (LGPD)</h2>
  <p>Você pode solicitar, a qualquer momento: acesso aos seus dados, correção de informações incorretas, e a remoção do seu cadastro da lista de atendimento (respeitada a guarda legal de prontuário). Para exercer esses direitos, entre em contato pelo e-mail acima.</p>

  <h2>7. Segurança</h2>
  <p>Adotamos medidas técnicas de proteção: conexão criptografada (HTTPS), senhas protegidas com Argon2id, verificação em duas etapas disponível, controle de acesso por papéis, trilha de auditoria e transmissão segura ao banco de dados.</p>

  <h2>8. Cookies</h2>
  <p>Usamos apenas cookies essenciais para manter você conectado com segurança. Não usamos cookies de rastreamento ou publicidade.</p>
</div>

@endsection
