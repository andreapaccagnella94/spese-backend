@extends('layouts.app')

@section('content')

<div class="container">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>I tuoi conti</h1>
    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
        + Nuovo conto
    </a>
</div>

@if($accounts->isEmpty())
    <div class="alert alert-info">
        Nessun conto trovato.
    </div>
@else
    <div class="row">
        @foreach($accounts as $account)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title">
                            {{ $account->name }}
                        </h5>

                        <p class="card-text">
                            Saldo:
                            <strong class="{{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $account->balance }} €
                            </strong>
                        </p>

                         <a href="{{ route('accounts.show', $account->id) }}"
                           class="btn btn-primary btn-sm">
                            Visualizza
                        </a>

                        <a href="{{ route('accounts.edit', $account->id) }}"
                           class="btn btn-warning btn-sm">
                            Modifica
                        </a>

                        <form method="POST"
                              action="{{ route('accounts.destroy', $account->id) }}"
                              class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Sei sicuro?')">
                                Elimina
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
</div>


@endsection