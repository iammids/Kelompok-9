<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penghuni\UploadProofRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        // Hanya tagihan milik kontrak penghuni yang sedang login.
        $payments = Payment::with('lease.room')
            ->whereHas('lease', fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate(10);

        return view('penghuni.payments.index', compact('payments'));
    }

    public function upload(UploadProofRequest $request, Payment $payment): RedirectResponse
    {
        // Otorisasi kepemilikan: cegah penghuni mengunggah ke tagihan orang lain (IDOR).
        abort_unless($payment->lease->user_id === $request->user()->id, 403);

        if ($payment->status !== 'belum_bayar') {
            return back()->with('error', 'Bukti hanya bisa diunggah untuk tagihan berstatus belum bayar.');
        }

        $path = $request->file('bukti')->store('bukti-bayar', 'public');

        $payment->update([
            'bukti_transfer' => $path,
            'status' => 'verifikasi',
        ]);

        return back()->with('success', 'Bukti transfer terunggah. Menunggu verifikasi admin.');
    }
}
