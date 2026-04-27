<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    /**
     * Mostra il form per creare un nuovo accredito.
     */
    public function create()
    {
        $accounts = Account::where('user_id', Auth::id())->get();
        $categories = Category::all();

        return view('credits.create', compact('accounts', 'categories'));
    }

    /**
     * Salva un nuovo accredito.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Verifica che l'account appartenga all'utente loggato
        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Credit::create([
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'account_id' => $account->id,
            'category_id' => $validated['category_id'],
        ]);

        // Aggiorna il saldo dell'account (aggiungi l'accredito)
        $account->increment('balance', $validated['amount']);

        return redirect()->route('accounts.show', $account->id)
            ->with('success', 'Accredito creato con successo!');
    }

    /**
     * Crea un accredito usando l'AI simulata (da descrizione testuale).
     */
    public function storeWithAI(Request $request)
    {
        $validated = $request->validate([
            'ai_description' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
        ]);

        // Verifica che l'account appartenga all'utente loggato
        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $extracted = $this->extractDataFromDescription($validated['ai_description']);

        Credit::create([
            'amount' => $extracted['amount'],
            'description' => $extracted['description'],
            'date' => $extracted['date'],
            'account_id' => $account->id,
            'category_id' => $extracted['category_id'],
        ]);

        // Aggiorna il saldo dell'account (aggiungi l'accredito)
        $account->increment('balance', $extracted['amount']);

        return redirect()->route('accounts.show', $account->id)
            ->with('success', 'Accredito creato automaticamente dall\'AI!');
    }

    /**
     * AI simulata: estrae importo, categoria e descrizione da un testo.
     */
    private function extractDataFromDescription(string $description): array
    {
        $amount = 0;
        $categoryId = null;

        // Estrai importo (pattern: numeri con eventuale decimale)
        if (preg_match('/(\d+[,.]?\d*)\s?€?/', $description, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
        }

        // Estrai categoria dalla descrizione
        $categoryKeywords = [
            'stipendio' => 1,
            'salario' => 1,
            'pagamento' => 1,
            'bonifico' => 1,
            'alimentari' => 1,
            'supermercato' => 1,
            'spesa' => 1,
            'trasporti' => 2,
            'benzina' => 2,
            'carburante' => 2,
            'treno' => 2,
            'casa' => 3,
            'affitto' => 3,
            'bolletta' => 3,
            'luce' => 3,
            'gas' => 3,
            'svago' => 4,
            'cinema' => 4,
            'ristorante' => 4,
            'bar' => 4,
            'salute' => 5,
            'farmacia' => 5,
            'medico' => 5,
            'vestiti' => 6,
            'abbigliamento' => 6,
            'regali' => 7,
            'regalo' => 7,
        ];

        $descriptionLower = strtolower($description);
        foreach ($categoryKeywords as $keyword => $catId) {
            if (str_contains($descriptionLower, $keyword)) {
                $categoryId = $catId;
                break;
            }
        }

        // Default categoria
        if (!$categoryId) {
            $categoryId = 8; // Altro
        }

        // Pulisci descrizione rimuovendo l'importo
        $cleanDescription = trim(preg_replace('/\d+[,.]?\d*\s?€?/', '', $description));

        return [
            'amount' => $amount,
            'description' => $cleanDescription ?: 'Accredito generico',
            'date' => now()->format('Y-m-d'),
            'category_id' => $categoryId,
        ];
    }
}
