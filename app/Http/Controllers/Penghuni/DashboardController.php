<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $lease = $user->activeLease()->with('room')->first();

        $tagihanBulanIni = $lease
            ? $lease->payments()->where('periode_bulan', Payment::periodeSekarang())->first()
            : null;

        return view('penghuni.dashboard', [
            'lease' => $lease,
            'periode' => Payment::periodeSekarang(),
            'tagihanBulanIni' => $tagihanBulanIni,
            'keluhan' => $user->complaints()->with('room')->latest()->limit(5)->get(),
        ]);
    }
}
