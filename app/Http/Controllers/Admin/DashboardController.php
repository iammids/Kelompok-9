<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $periode = Payment::periodeSekarang();

        // Satu query agregat untuk semua hitungan status kamar.
        $kamar = Room::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', [
            'periode' => $periode,
            'kamarTersedia' => (int) ($kamar['tersedia'] ?? 0),
            'kamarTerisi' => (int) ($kamar['terisi'] ?? 0),
            'kamarPerbaikan' => (int) ($kamar['perbaikan'] ?? 0),
            // Pemasukan = tagihan LUNAS untuk periode bulan berjalan.
            'pemasukan' => (int) Payment::where('status', 'lunas')
                ->where('periode_bulan', $periode)
                ->sum('nominal'),
            'menungguVerifikasi' => Payment::where('status', 'verifikasi')->count(),
            'keluhanAktif' => Complaint::whereIn('status', ['pending', 'diproses'])->count(),
            'tagihanVerifikasi' => Payment::with('lease.user', 'lease.room')
                ->where('status', 'verifikasi')->latest()->limit(5)->get(),
            'keluhanTerbaru' => Complaint::with('user', 'room')
                ->whereIn('status', ['pending', 'diproses'])->latest()->limit(5)->get(),
        ]);
    }
}
