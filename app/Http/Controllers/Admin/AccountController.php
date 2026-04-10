<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())->get();

        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
        ]);

        Account::create([
            'name' => $validated['name'],
            'balance' => $validated['balance'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'Conto creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $account = Account::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['expenses.category', 'credits.category'])
            ->firstOrFail();

        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $account = Account::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
        ]);

        $account = Account::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Conto aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = Account::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Conto eliminato con successo!');
    }
}
