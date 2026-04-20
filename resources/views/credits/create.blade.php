@extends('layouts.app')

@section('content')

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nuovo accredito</h1>
        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
            Torna ai conti
        </a>
    </div>

    <div class="row">
        <!-- Form manuale -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Inserimento manuale</h5>
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

                    <form method="POST" action="{{ route('credits.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Conto</label>
                            <select name="account_id" class="form-select" required>
                                <option value="">Seleziona conto</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Importo (€)</label>
                            <input type="number"
                                   step="0.01"
                                   name="amount"
                                   class="form-control"
                                   value="{{ old('amount') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date"
                                   name="date"
                                   class="form-control"
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Seleziona categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrizione</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"
                                      required>{{ old('description') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Salva accredito</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form AI OCR -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Inserimento con OCR</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Carica uno screenshot o notifica dalla tua app bancaria.<br>
                        L'AI estrarrà automaticamente importo, data e categoria.
                    </p>

                    <form method="POST" action="{{ route('credits.store.ai') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Conto</label>
                            <select name="account_id" class="form-select" required>
                                <option value="">Seleziona conto</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Immagine transazione</label>
                            <input type="file"
                                   name="transaction_image"
                                   class="form-control"
                                   accept="image/*"
                                   required>
                            <small class="text-muted">Formati supportati: JPG, PNG, GIF (max 5MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Oppure descrizione testuale</label>
                            <textarea name="ai_description"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Es: stipendio bonifico 1500€">{{ old('ai_description') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Estrai con OCR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
