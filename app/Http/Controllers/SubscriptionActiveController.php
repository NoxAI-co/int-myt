<?php

namespace App\Http\Controllers;

use App\Mail\PaymentProofUploaded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionActiveController extends Controller
{
    public function sendNotificationEmail(Request $request){
        \Illuminate\Support\Facades\Log::info("sadsad");
        $request->validate([
            'paymentProof' => 'required|image|max:2048', // Validar que sea una imagen y no supere los 2 MB
        ]);

        // Guardar el archivo
        $filePath = $request->file('paymentProof')->store('payment_proofs', 'public');

        // Enviar correo
        Mail::to('johancar991@gmail.com')->send(new PaymentProofUploaded($filePath));

        return response()->json(['message' => 'Comprobante cargado correctamente.']);
    }
}