<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateComplaintStatusRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), Complaint::STATUSES, true)
            ? $request->query('status')
            : null;

        $complaints = Complaint::with('user', 'room')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.complaints.index', compact('complaints', 'status'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load('user', 'room');

        return view('admin.complaints.show', compact('complaint'));
    }

    /** Alur progres hanya maju: pending -> diproses -> selesai. */
    public function update(UpdateComplaintStatusRequest $request, Complaint $complaint): RedirectResponse
    {
        $saatIni = array_search($complaint->status, Complaint::STATUSES, true);
        $tujuan = array_search($request->validated('status'), Complaint::STATUSES, true);

        if ($tujuan <= $saatIni) {
            return back()->with('error', 'Status keluhan hanya bisa dimajukan (pending → diproses → selesai).');
        }

        $complaint->update(['status' => $request->validated('status')]);

        return back()->with('success', 'Status keluhan diperbarui.');
    }
}
