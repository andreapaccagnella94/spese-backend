@extends('layouts.app')

@section('content')

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tutte le categorie</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            + Aggiungi nuova categoria
        </a>
    </div>

    @if($categories->isEmpty())
        <div class="alert alert-info">
            Nessuna categoria trovata.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Spese</th>
                        <th>Accrediti</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->expenses_count }}</td>
                        <td>{{ $category->credits_count }}</td>
                        <td>
                            <div class="btn-group gap-2" role="group">
                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-info btn-sm">
                                    Dettagli
                                </a>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                    Modifica
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#eliminaCategoria-{{ $category->id }}">
                                    Elimina
                                </button>
                            </div>

                            <!-- Modal elimina -->
                            <div class="modal fade" id="eliminaCategoria-{{ $category->id }}" tabindex="-1" aria-labelledby="eliminaCategoriaLabel-{{ $category->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="eliminaCategoriaLabel-{{ $category->id }}">Elimina la categoria: {{ $category->name }}</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Vuoi eliminare la categoria "<strong>{{ $category->name }}</strong>"? Questa azione è definitiva.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="submit" class="btn btn-danger" value="Elimina definitivamente">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
