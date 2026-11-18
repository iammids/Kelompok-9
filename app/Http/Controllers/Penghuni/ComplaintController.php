<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penghuni\ComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $complaints = $request->user()->complaints()
            ->with('room')
            ->latest()
            ->paginate(10);

        return view('penghuni.complaints.index', compact('complaints'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->user()->activeLease()->exists()) {
            return redirect()->route('penghuni.complaints.index')
                ->with('error', 'Anda belum memiliki kontrak aktif, jadi belum bisa membuat keluhan.');
        }

        return view('penghuni.complaints.create', ['complaint' => new Complaint()]);
    }

    public function store(ComplaintRequest $request): RedirectResponse
    {
        $lease = $request->user()->activeLease()->first();

        if (! $lease) {
            return redirect()->route('penghuni.complaints.index')
                ->with('error', 'Anda belum memiliki kontrak aktif.');
        }

        $data = $request->safe()->only(['judul', 'deskripsi']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-keluhan', 'public');
        }

        // user_id & room_id diambil dari sesi/kontrak, bukan dari input form.
        Complaint::create($data + [
            'user_id' => $request->user()->id,
            'room_id' => $lease->room_id,
            'status' => 'pending',
        ]);

        return redirect()->route('penghuni.complaints.index')->with('success', 'Keluhan berhasil dikirim.');
    }

    public function edit(Complaint $complaint): View|RedirectResponse
    {
        $this->authorizeOwner($complaint);

        if ($complaint->status !== 'pending') {
            return redirect()->route('penghuni.complaints.index')
                ->with('error', 'Keluhan yang sudah diproses tidak dapat diubah.');
        }

        return view('penghuni.complaints.edit', compact('complaint'));
    }

    public function update(ComplaintRequest $request, Complaint $complaint): RedirectResponse
    {
        $this->authorizeOwner($complaint);

        if ($complaint->status !== 'pending') {
            return redirect()->route('penghuni.complaints.index')
                ->with('error', 'Keluhan yang sudah diproses tidak dapat diubah.');
        }

        $data = $request->safe()->only(['judul', 'deskripsi']);

        if ($request->hasFile('foto')) {
            $complaint->hapusFoto();
            $data['foto'] = $request->file('foto')->store('foto-keluhan', 'public');
        }

        $complaint->update($data);

        return redirect()->route('penghuni.complaints.index')->with('success', 'Keluhan berhasil diperbarui.');
    }

    /** Hapus = batalkan keluhan; hanya untuk yang masih pending agar riwayat penanganan tetap utuh. */
    public function destroy(Complaint $complaint): RedirectResponse
    {
        $this->authorizeOwner($complaint);

        if ($complaint->status !== 'pending') {
            return back()->with('error', 'Hanya keluhan berstatus pending yang bisa dihapus.');
        }

        $complaint->hapusFoto();
        $complaint->delete();

        return redirect()->route('penghuni.complaints.index')->with('success', 'Keluhan dihapus.');
    }

    private function authorizeOwner(Complaint $complaint): void
    {
        abort_unless($complaint->user_id === auth()->id(), 403);
    }
}
