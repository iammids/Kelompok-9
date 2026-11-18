<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    public const STATUSES = ['belum_bayar', 'verifikasi', 'lunas'];

    protected $fillable = [
        'lease_id',
        'periode_bulan',
        'nominal',
        'bukti_transfer',
        'status',
    ];

    protected function casts(): array
    {
        return ['nominal' => 'integer'];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /** Label periode berbahasa Indonesia, mis. "November 2026". */
    public static function periode(?Carbon $date = null): string
    {
        return ($date ?? Carbon::now())->copy()->locale('id')->translatedFormat('F Y');
    }

    public static function periodeSekarang(): string
    {
        return self::periode(Carbon::now());
    }

    protected function buktiUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->bukti_transfer
                ? Storage::disk('public')->url($this->bukti_transfer)
                : null
        );
    }

    /** Hapus file bukti dari disk (aman dipanggil jika file tidak ada). */
    public function hapusBukti(): void
    {
        if ($this->bukti_transfer) {
            Storage::disk('public')->delete($this->bukti_transfer);
        }
    }
}
