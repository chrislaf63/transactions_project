<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Laravel\Sanctum\HasApiTokens;

class TransactionController extends Controller
{
    use HasApiTokens;

    /**
     * @OA\Get(
     *     path="/api/transactions",
     *     summary="Get all transactions",
     *     description="Get all transactions",
     *     operationId="index",
     *     tags={"Transactions"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();
        return $transactions;
    }

    /**
     * @OA\Post(
     *     path="/api/transactions",
     *     summary="Create a transaction",
     *     description="Create a transaction",
     *     operationId="store",
     *     tags={"Transactions"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *     required=true,
     *     description="Pass transaction data",
     *     @OA\JsonContent(
     *     required={"user_id","name","amount","type"},
     *     @OA\Property(property="user_id", type="integer", format="int64", example="1"),
     *     @OA\Property(property="name", type="string", example="Salary"),
     *     @OA\Property(property="amount", type="number", format="float", example="1000.00"),
     *     @OA\Property(property="type", type="string", example="credit"),
     *     )
     *    ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

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

        return response()->json(['Status' => 'transaction réussie',
            'solde' => $user->balance],201);
    }

    /**
     * @OA\Get(
     *     path="/api/transactions/{id}",
     *     summary="Get a transaction",
     *     description="Get a transaction",
     *     operationId="show",
     *     tags={"Transactions"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of transaction to return",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *    )
     *  ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     *
     */

    public function show($id)
    {
        $loggedUser = Auth::id();
        $transaction = Transaction::find($id)->where('user_id', $loggedUser);
        return $transaction;
    }

    /**
     * @OA\Put(
     *     path="/api/transactions/{id}",
     *     summary="Update a transaction",
     *     description="Update a transaction",
     *     operationId="update",
     *     tags={"Transactions"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of transaction to update",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *   )
     * ),
     *     @OA\RequestBody(
     *     required=true,
     *     description="Pass transaction data",
     *     @OA\JsonContent(
     *     required={"user_id","name","amount","type"},
     *     @OA\Property(property="user_id", type="integer", format="int64", example="1"),
     *     @OA\Property(property="name", type="string", example="Salary"),
     *     @OA\Property(property="amount", type="number", format="float", example="1000.00"),
     *     @OA\Property(property="type", type="string", example="credit"),
     *     )
     *   ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/api/transactions/{id}",
     *     summary="Delete a transaction",
     *     description="Delete a transaction",
     *     operationId="destroy",
     *     tags={"Transactions"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of transaction to delete",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     *  )
     * ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=422, description="Unprocessable Entity")
     * )
     *
     */

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
