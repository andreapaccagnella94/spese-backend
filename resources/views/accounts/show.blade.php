@extends('layouts.app')

@section('content')

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $account->name }}</h1>
        <div>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                Torna ai conti
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                - Spesa
            </a>
            <a href="{{ route('credits.create') }}" class="btn btn-success">
                + Accredito
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Informazioni conto</h5>
            <p class="mb-1">
                <strong>Saldo:</strong>
                <span class="{{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($account->balance, 2, ',', '.') }} €
                </span>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3 class="mb-3 text-danger">Spese</h3>

            @if($account->expenses->isEmpty())
                <div class="alert alert-info">
                    Nessuna spesa presente in questo conto.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="table-danger">
                                <tr>
                                    <th>Data</th>
                                    <th>Importo</th>
                                    <th>Categoria</th>
                                    <th>Descrizione</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                                        <td class="text-danger">
                                            - {{ number_format($expense->amount, 2, ',', '.') }} €
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $expense->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $expense->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <h3 class="mb-3 text-success">Accrediti</h3>

            @if($account->credits->isEmpty())
                <div class="alert alert-info">
                    Nessun accredito presente in questo conto.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>Data</th>
                                    <th>Importo</th>
                                    <th>Categoria</th>
                                    <th>Descrizione</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->credits as $credit)
                                    <tr>
                                        <td>{{ $credit->date->format('d/m/Y') }}</td>
                                        <td class="text-success">
                                            + {{ number_format($credit->amount, 2, ',', '.') }} €
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $credit->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $credit->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
