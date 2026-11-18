<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), Payment::STATUSES, true)
            ? $request->query('status')
            : null;
        $q = trim((string) $request->query('q', ''));

        $payments = Payment::with('lease.user', 'lease.room')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('periode_bulan', 'like', "%{$q}%")
                    ->orWhereHas('lease.user', fn ($u) => $u->where('name', 'like', "%{$q}%"))
            ))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.payments.index', compact('payments', 'status', 'q'));
    }

    public function create(): View
    {
        return view('admin.payments.create', [
            'leases' => Lease::aktif()->with('user', 'room')->get(),
            'periodeDefault' => Payment::periodeSekarang(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        Payment::create($request->validated() + ['status' => 'belum_bayar']);

        return redirect()->route('admin.payments.index')->with('success', 'Tagihan berhasil dibuat.');
    }

    public function edit(Payment $payment): View
    {
        $payment->load('lease.user', 'lease.room');

        return view('admin.payments.edit', compact('payment'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): RedirectResponse
    {
        if ($payment->status === 'lunas') {
            return back()->with('error', 'Tagihan yang sudah lunas tidak dapat diubah.');
        }

        $payment->update($request->validated());

        return redirect()->route('admin.payments.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->hapusBukti();
        $payment->delete();

        return redirect()->route('admin.payments.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    /** Buat tagihan periode berjalan untuk semua kontrak aktif yang belum punya tagihannya. */
    public function generate(): RedirectResponse
    {
        $periode = Payment::periodeSekarang();

        $dibuat = DB::transaction(function () use ($periode) {
            $jumlah = 0;

            foreach (Lease::aktif()->with('room')->get() as $lease) {
                $payment = Payment::firstOrCreate(
                    ['lease_id' => $lease->id, 'periode_bulan' => $periode],
                    ['nominal' => $lease->room->harga_bulanan, 'status' => 'belum_bayar']
                );

                if ($payment->wasRecentlyCreated) {
                    $jumlah++;
                }
            }

            return $jumlah;
        });

        return back()->with(
            'success',
            $dibuat > 0
                ? "{$dibuat} tagihan periode {$periode} berhasil dibuat."
                : "Semua kontrak aktif sudah memiliki tagihan periode {$periode}."
        );
    }

    public function verify(Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'verifikasi') {
            return back()->with('error', 'Hanya tagihan berstatus "verifikasi" yang bisa disetujui.');
        }

        $payment->update(['status' => 'lunas']);

        return back()->with('success', 'Pembayaran diverifikasi dan ditandai lunas.');
    }

    /** Tolak bukti: hapus file agar penghuni bisa mengunggah ulang. */
    public function reject(Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'verifikasi') {
            return back()->with('error', 'Hanya tagihan berstatus "verifikasi" yang bisa ditolak.');
        }

        $payment->hapusBukti();
        $payment->update(['status' => 'belum_bayar', 'bukti_transfer' => null]);

        return back()->with('success', 'Bukti ditolak. Status kembali ke belum bayar.');
    }
}
