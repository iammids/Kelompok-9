<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Complaint extends Model
{
    /** Urutan alur progres; dipakai untuk memvalidasi transisi status. */
    public const STATUSES = ['pending', 'diproses', 'selesai'];

    protected $fillable = [
        'user_id',
        'room_id',
        'judul',
        'deskripsi',
        'foto',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->foto ? Storage::disk('public')->url($this->foto) : null
        );
    }

    public function hapusFoto(): void
    {
        if ($this->foto) {
            Storage::disk('public')->delete($this->foto);
        }
    }
}
