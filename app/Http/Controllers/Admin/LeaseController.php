<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeaseRequest;
use App\Http\Requests\Admin\UpdateLeaseRequest;
use App\Models\Lease;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['aktif', 'selesai'], true)
            ? $request->query('status')
            : null;

        $leases = Lease::with(['user', 'room'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('tanggal_masuk')
            ->paginate(10)
            ->withQueryString();

        return view('admin.leases.index', compact('leases', 'status'));
    }

    public function create(): View
    {
        return view('admin.leases.create', [
            'rooms' => Room::tersedia()->orderBy('nomor_kamar')->get(),
            // Hanya penghuni yang belum punya kontrak aktif.
            'users' => User::where('role', User::ROLE_PENGHUNI)
                ->whereDoesntHave('activeLease')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreLeaseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Lintas tabel (leases + rooms) => satu transaksi; kamar dikunci agar
        // dua admin tidak bisa menyewakan kamar yang sama bersamaan.
        DB::transaction(function () use ($data) {
            $room = Room::lockForUpdate()->findOrFail($data['room_id']);

            if ($room->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'room_id' => 'Kamar tidak tersedia untuk disewa.',
                ]);
            }

            if (Lease::aktif()->where('user_id', $data['user_id'])->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'Penghuni ini masih memiliki kontrak aktif.',
                ]);
            }

            Lease::create($data + ['status' => 'aktif']);
            $room->update(['status' => 'terisi']);
        });

        return redirect()->route('admin.leases.index')->with('success', 'Kontrak sewa berhasil dibuat.');
    }

    public function edit(Lease $lease): View
    {
        $lease->load(['user', 'room']);

        return view('admin.leases.edit', compact('lease'));
    }

    public function update(UpdateLeaseRequest $request, Lease $lease): RedirectResponse
    {
        $lease->update($request->validated());

        return redirect()->route('admin.leases.index')->with('success', 'Kontrak sewa berhasil diperbarui.');
    }

    /** Checkout: akhiri sewa, kamar kembali tersedia. */
    public function checkout(Lease $lease): RedirectResponse
    {
        if ($lease->status !== 'aktif') {
            return back()->with('error', 'Kontrak ini sudah berakhir.');
        }

        DB::transaction(function () use ($lease) {
            $lease->update([
                'status' => 'selesai',
                'tanggal_keluar' => now()->toDateString(),
            ]);
            $lease->room()->update(['status' => 'tersedia']);
        });

        return redirect()->route('admin.leases.index')
            ->with('success', 'Sewa diakhiri. Kamar kembali berstatus tersedia.');
    }

    public function destroy(Lease $lease): RedirectResponse
    {
        DB::transaction(function () use ($lease) {
            if ($lease->status === 'aktif') {
                $lease->room()->update(['status' => 'tersedia']);
            }

            // Tagihan ikut terhapus (cascade); bersihkan file buktinya.
            $lease->payments->each->hapusBukti();
            $lease->delete();
        });

        return redirect()->route('admin.leases.index')->with('success', 'Kontrak sewa berhasil dihapus.');
    }
}
