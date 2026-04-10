@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
    <div class="card-header">
        <h3>Crea nuovo conto</h3>
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

        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nome conto</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Saldo iniziale</label>
                <input type="number"
                       step="0.01"
                       name="balance"
                       class="form-control"
                       value="{{ old('balance') }}">
            </div>

            <button class="btn btn-success">Salva</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                Indietro
            </a>
        </form>

    </div>
</div>
</div>

@endsection