<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Account;
use App\Models\Category;
use App\Models\TransactionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\tesseract_ocr\TesseractOCR;

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
     * Crea un accredito usando l'AI (OCR da immagine o testo).
     */
    public function storeWithAI(Request $request)
    {
        $validated = $request->validate([
            'transaction_image' => 'nullable|image|mimes:jpeg,png,gif|max:5120',
            'ai_description' => 'nullable|string|max:255',
            'account_id' => 'required|exists:accounts,id',
        ]);

        // Verifica che l'account appartenga all'utente loggato
        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            // Priorità all'immagine OCR, fallback su descrizione testuale
            if ($request->hasFile('transaction_image')) {
                $extracted = $this->extractDataFromImage($request->file('transaction_image'), $account->id);
            } elseif (!empty($validated['ai_description'])) {
                $extracted = $this->extractDataFromDescription($validated['ai_description']);
            } else {
                return back()->withErrors(['ai_description' => 'Fornire un\'immagine o una descrizione']);
            }

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
        } catch (\RuntimeException $e) {
            return back()->withErrors(['transaction_image' => $e->getMessage()]);
        }
    }

    /**
     * Estrae dati da un'immagine usando Tesseract OCR.
     */
    private function extractDataFromImage($image, int $accountId): array
    {
        // Salva l'immagine temporaneamente
        $path = $image->store('transaction_images', 'public');
        $fullPath = storage_path('app/public/' . $path);

        try {
            // Esegui OCR con Tesseract
            $ocr = TesseractOCR::image($fullPath)
                ->lang('ita');

            $text = $ocr->run();

            // Estrai dati dal testo
            $extracted = $this->parseOCRText($text);

            // Salva il record TransactionImage
            TransactionImage::create([
                'path' => $path,
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize(),
                'transaction_type' => 'credit',
                'account_id' => $accountId,
                'extracted_text' => $text,
                'extracted_amount' => $extracted['amount'],
                'category_id' => $extracted['category_id'],
                'extracted_date' => $extracted['date'],
                'extracted_description' => $extracted['description'],
                'is_processed' => true,
            ]);

            return $extracted;
        } catch (\Exception $e) {
            // Elimina l'immagine se l'OCR fallisce
            Storage::disk('public')->delete($path);

            throw new \RuntimeException(
                'OCR non disponibile. Verifica che Tesseract sia installato: ' . $e->getMessage()
            );
        }
    }

    /**
     * Parse del testo estratto dall'OCR per estrarre dati strutturati.
     */
    private function parseOCRText(string $text): array
    {
        $amount = 0;
        $categoryId = null;
        $date = now()->format('Y-m-d');
        $description = 'Transazione da OCR';

        // Estrai importo (pattern: numeri con eventuale decimale, con o senza €)
        if (preg_match('/[€]?\s*(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})?)\s?[€]?/', $text, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
        } elseif (preg_match('/(\d+[,.]?\d*)\s?€/', $text, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
        }

        // Estrai data (pattern: DD/MM/YYYY o DD-MM-YYYY)
        if (preg_match('/(\d{2})[\/\-](\d{2})[\/\-](\d{4})/', $text, $matches)) {
            $date = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        } elseif (preg_match('/(\d{4})[\/\-](\d{2})[\/\-](\d{2})/', $text, $matches)) {
            $date = "{$matches[1]}-{$matches[2]}-{$matches[3]}";
        }

        // Estrai categoria dalla descrizione
        $categoryKeywords = [
            'stipendio' => 1,
            'salario' => 1,
            'pagamento' => 1,
            'bonifico' => 1,
            'accredito' => 1,
            'alimentari' => 1,
            'supermercato' => 1,
            'spesa' => 1,
            'trasporti' => 2,
            'benzina' => 2,
            'carburante' => 2,
            'treno' => 2,
            'bus' => 2,
            'metro' => 2,
            'casa' => 3,
            'affitto' => 3,
            'bolletta' => 3,
            'luce' => 3,
            'gas' => 3,
            'enel' => 3,
            'acegas' => 3,
            'svago' => 4,
            'cinema' => 4,
            'ristorante' => 4,
            'bar' => 4,
            'pizza' => 4,
            'cena' => 4,
            'pranzo' => 4,
            'salute' => 5,
            'farmacia' => 5,
            'medico' => 5,
            'ospedale' => 5,
            'vestiti' => 6,
            'abbigliamento' => 6,
            'h&m' => 6,
            'zara' => 6,
            'regali' => 7,
            'regalo' => 7,
        ];

        $textLower = strtolower($text);
        foreach ($categoryKeywords as $keyword => $catId) {
            if (str_contains($textLower, $keyword)) {
                $categoryId = $catId;
                break;
            }
        }

        // Default categoria
        if (!$categoryId) {
            $categoryId = 8; // Altro
        }

        // Pulisci descrizione: prendi le prime parole significative
        $cleanText = trim(preg_replace('/[€]\s*\d+[,.]?\d*/', '', $text));
        $cleanText = trim(preg_replace('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', '', $cleanText));
        $cleanText = trim(preg_replace('/\s+/', ' ', $cleanText));

        if (!empty($cleanText)) {
            $description = substr($cleanText, 0, 255);
        }

        return [
            'amount' => $amount,
            'description' => $description,
            'date' => $date,
            'category_id' => $categoryId,
        ];
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
            'bus' => 2,
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
