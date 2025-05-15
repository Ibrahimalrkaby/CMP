<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeeController extends Controller
{
    // Store payment receipt and fee details
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,pdf|max:2048', 
        ]);

        $receiptPath = $request->file('receipt_image')->store('receipts', 'public');

        $fee = Fee::create([
            'student_id' => $request->student_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'receipt_image' => $receiptPath,
        ]);

        return response()->json([
            'message' => 'Fee payment successfully recorded',
            'data' => $fee
        ]);
    }

    // Get all fees for a specific student
    public function getFees($student_id)
    {
        $fees = Fee::where('student_id', $student_id)->get();
        return response()->json($fees);
    }
}
