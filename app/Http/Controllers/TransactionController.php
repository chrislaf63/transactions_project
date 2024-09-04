<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();
        return $transactions;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'numeric'],
            'name' => ['required', 'max:255'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'in:credit,debit'],
        ]);
        $transaction = Transaction::create($request->all());
        $type = $transaction->type;
        $amount = $transaction->amount;
        $user = $transaction->user;
        if($type == 'credit') {
            $user->balance += $amount;
        } else {
            $user->balance -= $amount;
        }
        $user->save();

        return response()->json(['transaction' => $transaction,
            'montant' => $amount,
            'Utilisateur' => $user,
            'solde' => $user->balance],201);
    }

    public function show($id)
    {
        $loggedUser = Auth::id();
        $transaction = Transaction::find($id)->where('user_id', $loggedUser);
        return $transaction;
    }

    public function update(Request $request, $id)
    {
        $loggedUser = Auth::id();
        if($loggedUser != $request->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $transaction = Transaction::find($id);
        $transaction->update($request->all());
        return $transaction;
    }

    public function destroy($id)
    {
        $loggedUser = Auth::id();
        $transaction = Transaction::find($id);
        if($loggedUser != $transaction->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted'], 200);
    }
}
