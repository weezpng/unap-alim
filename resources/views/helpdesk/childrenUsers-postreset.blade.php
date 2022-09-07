@extends('layout.master')

@section('title','HELPDESK: Reset de conta')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Helpdesk</li>
<li class="breadcrumb-item active">Reset de conta</li>
<li class="breadcrumb-item active">Associar Children Users</li>
@endsection

@section('page-content')

<div class="col-md-12">
  <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
          <div class="d-flex justify-content-between">
            <h5 class="modal-title" id="exampleModalLabel">Associar Children Users</h5>
          </div>
      </div>
      <div class="card-body">

        <table class="table table-head-fixed">
          <thead>
            <tr>
              <th style="width: 10%; text-align: left;">NIM</th>
              <th style="width: 15%; text-align: left;">Nome</th>
              <th style="width: 10%; text-align: left;">Posto</th>
              <th style="width: 30%; text-align: left;">Grupo \ Subgrupo </th>
              <th>Parent</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($childrenUsers as $key => $user)
            <tr>
              <td>{{ $user['childID'] }}</td>
              <td>{{ $user['childNome'] }}</td>
              <td>{{ $user['childPosto'] }}</td>
              <td>
              @if ($user['childGroup'])
                ID <b>{{ $user['childGroup'] }}</b>
              @else
                GERAL
              @endif
              @if ($user['childSubGroup'])
                \ ID <b>{{ $user['childSubGroup'] }}</b>
              @else
               \ GERAL
              @endif
              </td>
              <td>
                <form id="associateUser" name="associateUser" method="post" action="">
                  @csrf
                  <input type="hidden" value="{{ $user['childID'] }}" id="childID" name="childID">
                  <input required data-id="" type="number" class="form-control" id="parentToAdd" name="parentToAdd" placeholder="NIM"
                    maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" style="width: 80%;display: inline-flex;">
                  <button type="submit" class="btn btn-primary" style="margin-left: .5rem; margin-top: -0.3rem; width: 15%;">Associar</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
