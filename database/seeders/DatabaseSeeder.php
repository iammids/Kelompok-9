<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // forceCreate: kolom `role` sengaja tidak mass-assignable di model.
            $admin = User::forceCreate([
                'name' => 'Pemilik Kos',
                'email' => 'admin@kos.test',
                'no_hp' => '081200000000',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password'),
            ]);

            $penghuni = collect([
                ['Budi Santoso', 'budi@kos.test', '081211111111'],
                ['Siti Aminah', 'siti@kos.test', '081222222222'],
                ['Andi Pratama', 'andi@kos.test', '081233333333'],
            ])->map(fn ($u) => User::forceCreate([
                'name' => $u[0],
                'email' => $u[1],
                'no_hp' => $u[2],
                'role' => User::ROLE_PENGHUNI,
                'password' => Hash::make('password'),
            ]));

            $rooms = collect([
                ['101', 'Non-AC', 600000, 'Kasur, lemari, kipas angin, kamar mandi luar'],
                ['102', 'Non-AC', 600000, 'Kasur, lemari, kipas angin, kamar mandi luar'],
                ['103', 'AC', 850000, 'Kasur, lemari, AC, kamar mandi dalam'],
                ['104', 'AC', 850000, 'Kasur, lemari, AC, kamar mandi dalam'],
                ['201', 'AC', 900000, 'Kasur, lemari, AC, meja belajar, kamar mandi dalam'],
                ['202', 'VIP', 1200000, 'Kasur king, AC, TV, water heater, meja kerja, kamar mandi dalam'],
                ['203', 'VIP', 1200000, 'Kasur king, AC, TV, water heater, meja kerja, kamar mandi dalam'],
                ['204', 'Non-AC', 650000, 'Kasur, lemari, kipas angin, balkon'],
            ])->mapWithKeys(fn ($r) => [$r[0] => Room::create([
                'nomor_kamar' => $r[0],
                'tipe' => $r[1],
                'harga_bulanan' => $r[2],
                'fasilitas' => $r[3],
                'status' => 'tersedia',
            ])]);

            $rooms['204']->update(['status' => 'perbaikan']);

            // Kontrak aktif untuk 3 penghuni; kamar otomatis "terisi".
            $assignments = [
                [$penghuni[0], $rooms['101']],
                [$penghuni[1], $rooms['103']],
                [$penghuni[2], $rooms['202']],
            ];

            $leases = [];
            foreach ($assignments as [$user, $room]) {
                $leases[] = Lease::create([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'tanggal_masuk' => Carbon::now()->subMonths(2)->startOfMonth(),
                    'status' => 'aktif',
                ]);
                $room->update(['status' => 'terisi']);
            }

            // Berkas contoh agar tombol "lihat bukti" bisa dicoba (butuh `storage:link`).
            Storage::disk('public')->put(
                'bukti-bayar/contoh-bukti.txt',
                'Berkas contoh bukti transfer (data dummy seeder).'
            );

            $bulanLalu = Payment::periode(Carbon::now()->subMonthNoOverflow());
            $bulanIni = Payment::periodeSekarang();
            $statusBulanIni = ['belum_bayar', 'verifikasi', 'lunas'];

            foreach ($leases as $i => $lease) {
                $nominal = $lease->room->harga_bulanan;

                Payment::create([
                    'lease_id' => $lease->id,
                    'periode_bulan' => $bulanLalu,
                    'nominal' => $nominal,
                    'status' => 'lunas',
                ]);

                $status = $statusBulanIni[$i];
                Payment::create([
                    'lease_id' => $lease->id,
                    'periode_bulan' => $bulanIni,
                    'nominal' => $nominal,
                    'status' => $status,
                    'bukti_transfer' => $status === 'belum_bayar' ? null : 'bukti-bayar/contoh-bukti.txt',
                ]);
            }

            Complaint::create([
                'user_id' => $penghuni[0]->id,
                'room_id' => $rooms['101']->id,
                'judul' => 'Kipas angin berisik',
                'deskripsi' => 'Kipas di kamar 101 berbunyi keras saat kecepatan sedang dan tinggi.',
                'status' => 'pending',
            ]);

            Complaint::create([
                'user_id' => $penghuni[1]->id,
                'room_id' => $rooms['103']->id,
                'judul' => 'AC kurang dingin',
                'deskripsi' => 'AC menyala tetapi suhu ruangan tidak turun sejak tiga hari lalu.',
                'status' => 'diproses',
            ]);
        });
    }
}
