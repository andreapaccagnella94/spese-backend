@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Modifica conto</h3>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('accounts.update', $account->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nome conto</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $account->name) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Saldo</label>
                    <input type="number"
                           step="0.01"
                           name="balance"
                           class="form-control"
                           value="{{ old('balance', $account->balance) }}"
                           required>
                </div>

                <button class="btn btn-success">Aggiorna</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                    Indietro
                </a>
            </form>

        </div>
    </div>
</div>

@endsection
