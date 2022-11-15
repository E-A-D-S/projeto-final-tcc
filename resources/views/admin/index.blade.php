@extends('layout')
@section('title','dashboard' )
@section('content')

<div class="nav px-5 d-flex align-items-center justify-content-between">
  <a href="{{ route('paciente.home') }}"><img src="/img/ulbra.png" alt="" width="100" height="100"></a>
  <form action="{{route('paciente.index')}}" class="d-flex" role="search" method="GET">
  <input class="form-control me-2 " style="width: 500px; padding: 10px;" type="search" name="search" placeholder="Buscar" aria-label="Search">
  <button class="btn btn-warning" type="submit">Buscar</button>
  </form>
  <a href="{{ route('paciente.home') }}" class="text-white">Voltar</a>
</div>

<div class="text-center">
  <h1 class="py-3">dashboard/<a href="/paciente/permission">Permission</a></h1>
  <h4>Pacientes Cadastrados</h4>
</div>
<div class="px-5">
  <table class="table table-dark table-striped">
      <thead>
        <tr>
          <th scope="col">Nome</th>
          <th scope="col">idade</th>
          <th scope="col">cidade</th>
          <th scope="col">motivo</th>
          <th scope="col" class="text-center">ações</th>
  
        </tr>
      </thead>
      <tbody>
  
        @foreach($patient as $patient)
        <tr>
          <td>
            <p>{{ $patient->name }}</p>
          </td>
          <td>
            <?php
              $data = $patient->birth_date;

              // separando yyyy, mm, ddd
              list($ano, $mes, $dia) = explode('-', $data);

              // data atual
              $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
              // Descobre a unix timestamp da data de nascimento do fulano
              $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

              // cálculo
              $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
            ?>
            <p>{{ $idade }}</p>
          </td>
          <td>
            <p>{{ $patient->city }}</p>
          </td>
          <td>
            <p>{{ $patient->consultation }}</p>
          </td>
          <td class="text-center">
            <form action="{{route('paciente.destroy',$patient->id)}}" method="post">
              @csrf
              @method('delete')
              <button type="submit" class="btn btn-danger">deletar</button>
              <a href="{{route('paciente.edit',$patient->id)}}" class="btn btn-primary">editar</a>
              <a href="{{route('paciente.view',$patient->id)}}" class="btn btn-info">Visualizar</a>
              <a href="{{route('paciente.generatePdf',$patient->id)}}" target="_blank" class="btn btn-warning">imprimir</a>
          </td>
        </tr>
        @endforeach
  
      </tbody>
    </table>
</div>

<style>
  .nav {
    background-color: #585dd6;
  }
</style>
@endsection