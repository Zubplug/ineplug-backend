<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class WalletTransferController extends Controller
{
    public function transfer(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'pin' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $sender = Auth::user();

        if (!Hash::check($request->pin, $sender->transaction_pin)) {
            return response()->json(['message' => 'Invalid PIN'], 403);
        }

        $recipient = User::where('phone', $request->recipient)
                         ->orWhere('id', $request->recipient)
                         ->first();

        if (!$recipient) {
            return response()->json(['message' => 'Recipient not found'], 404);
        }

        if ($recipient->id === $sender->id) {
            return response()->json(['message' => 'You cannot transfer to yourself'], 400);
        }

        if ($sender->wallet_balance < $request->amount) {
            return response()->json(['message' => 'Insufficient wallet balance'], 400);
        }

        $amount = $request->amount;
        $reference = 'INEPLUG-TX-' . time();

        $sender->wallet_balance -= $amount;
        $sender->save();

        $recipient->wallet_balance += $amount;
        $recipient->save();

        // Save transaction
        $transaction = Transaction::create([
            'user_id' => $sender->id,
            'amount' => $amount,
            'fee' => 0,
            'total' => $amount,
            'reference' => $reference,
            'account_number' => $recipient->phone,
            'bank_code' => 'INEPLUG',
            'status' => 'SUCCESS',
            'channel' => 'IneplugTransfer',
        ]);

        // Generate PDF receipt
        $data = [
            'reference' => $reference,
            'sender' => $sender->first_name . ' ' . $sender->last_name,
            'recipient' => $recipient->first_name . ' ' . $recipient->last_name,
            'amount' => $amount,
            'note' => $request->note ?? '',
            'phone' => $recipient->phone,
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.ineplug_receipt', $data);
        $filename = "ineplug-transfer-" . $reference . ".pdf";
        Storage::put("public/receipts/" . $filename, $pdf->output());
        $receiptUrl = url("/storage/receipts/" . $filename);

        return response()->json([
            'message' => 'Transfer successful',
            'reference' => $reference,
            'recipient_name' => $recipient->first_name . ' ' . $recipient->last_name,
            'recipient_phone' => $recipient->phone,
            'amount' => $amount,
            'type' => 'INEPLUG',
            'balance' => $sender->wallet_balance,
            'note' => $request->note ?? null,
            'receipt_url' => $receiptUrl
        ]);
    }
}
