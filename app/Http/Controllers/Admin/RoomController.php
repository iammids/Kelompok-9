<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), Room::STATUSES, true)
            ? $request->query('status')
            : null;
        $q = trim((string) $request->query('q', ''));

        $rooms = Room::with('activeLease.user') // eager loading: hindari N+1
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('nomor_kamar', 'like', "%{$q}%")
                    ->orWhere('tipe', 'like', "%{$q}%")
            ))
            ->orderBy('nomor_kamar')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rooms.index', compact('rooms', 'status', 'q'));
    }

    public function create(): View
    {
        return view('admin.rooms.create', ['room' => new Room(['status' => 'tersedia'])]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Status "terisi" hanya boleh lewat pembuatan kontrak sewa.
        if ($data['status'] === 'terisi') {
            return back()->withInput()->withErrors([
                'status' => 'Status "terisi" diatur otomatis lewat kontrak sewa.',
            ]);
        }

        Room::create($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $data = $request->validated();
        $punyaKontrakAktif = $room->activeLease()->exists();

        // Jaga konsistensi status kamar dengan kontrak aktif.
        if ($punyaKontrakAktif && $data['status'] !== 'terisi') {
            return back()->withInput()->withErrors([
                'status' => 'Kamar masih punya kontrak aktif. Akhiri sewa (checkout) dulu sebelum mengubah status.',
            ]);
        }

        if (! $punyaKontrakAktif && $data['status'] === 'terisi') {
            return back()->withInput()->withErrors([
                'status' => 'Status "terisi" diatur otomatis lewat kontrak sewa.',
            ]);
        }

        $room->update($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->leases()->exists()) {
            return back()->with('error', 'Kamar tidak dapat dihapus karena memiliki riwayat kontrak sewa.');
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }
}
