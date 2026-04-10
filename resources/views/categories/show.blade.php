@extends('layouts.app')

@section('content')

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $category->name }}</h1>
        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                Torna alle categorie
            </a>
            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                Modifica
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3 class="mb-3 text-danger">Spese</h3>

            @if($category->expenses->isEmpty())
                <div class="alert alert-info">
                    Nessuna spesa in questa categoria.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead class="table-danger">
                                <tr>
                                    <th>Data</th>
                                    <th>Importo</th>
                                    <th>Descrizione</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                                        <td class="text-danger">
                                            - {{ number_format($expense->amount, 2, ',', '.') }} €
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

            @if($category->credits->isEmpty())
                <div class="alert alert-info">
                    Nessun accredito in questa categoria.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead class="table-success">
                                <tr>
                                    <th>Data</th>
                                    <th>Importo</th>
                                    <th>Descrizione</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->credits as $credit)
                                    <tr>
                                        <td>{{ $credit->date->format('d/m/Y') }}</td>
                                        <td class="text-success">
                                            + {{ number_format($credit->amount, 2, ',', '.') }} €
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
