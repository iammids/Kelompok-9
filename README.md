# Sistem Manajemen Kos / Kontrakan

Aplikasi web (Laravel 12 + Breeze Blade + Tailwind CSS) untuk mengelola kamar, kontrak sewa,
tagihan bulanan, dan keluhan fasilitas, dengan dua role: **Admin** (pemilik kos) dan **Penghuni**.

> **Status repo:** repo ini berisi **kode aplikasi saja** (migration, model, seeder, middleware,
> controller, Form Request, route, view, README, `.env.example`), bukan seluruh kerangka Laravel
> (`vendor/`, `artisan`, `config/`, dst.). Kode ditulis di lingkungan yang tidak bisa mengunduh
> paket Composer, sehingga **belum pernah dijalankan** (`migrate:fresh --seed` dan uji manual belum
> dilakukan). Sintaks PHP sudah di-lint dan nama route sudah dicocokkan, tetapi lakukan uji
> menyeluruh setelah instalasi di bawah.

## Instalasi lokal

Prasyarat: PHP 8.2+ (ekstensi `pdo_sqlite` atau `pdo_mysql`), Composer, Node.js 18+, Git.

```bash
# 1. Buat proyek Laravel baru + Breeze (Blade)
composer create-project laravel/laravel kos-app
cd kos-app
composer require laravel/breeze --dev
php artisan breeze:install blade      # pilih opsi default (tanpa dark mode/test juga boleh)

# 2. Salin isi repo ini ke dalam proyek (TIMPA file yang sudah ada).
#    Wajib dilakukan SETELAH breeze:install karena menimpa routes/web.php,
#    layouts/navigation.blade.php, app/Models/User.php, dan DatabaseSeeder.php.
git clone -b feat/sistem-manajemen-kos https://github.com/iammids/Kelompok-9 ../kelompok-9
cp -R ../kelompok-9/app ../kelompok-9/database ../kelompok-9/resources ../kelompok-9/routes .
cp ../kelompok-9/.env.example .env.example

# 3. Konfigurasi
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # jika memakai SQLite (default)
# Untuk MySQL: ubah blok DB_* di .env lalu buat database `manajemen_kos`.

# 4. Database, storage, dan aset
php artisan storage:link
php artisan migrate:fresh --seed
npm install && npm run build          # atau `npm run dev` saat pengembangan

# 5. Jalankan
php artisan serve                     # http://127.0.0.1:8000
```

`APP_URL` di `.env` harus sama dengan alamat `php artisan serve`; jika tidak, link bukti transfer
dan foto keluhan tidak akan terbuka.

## Akun dummy (password semua: `password`)

| Role     | Nama          | Email            | Kamar |
|----------|---------------|------------------|-------|
| Admin    | Pemilik Kos   | admin@kos.test   | -     |
| Penghuni | Budi Santoso  | budi@kos.test    | 101   |
| Penghuni | Siti Aminah   | siti@kos.test    | 103   |
| Penghuni | Andi Pratama  | andi@kos.test    | 202   |

Seeder juga membuat 8 kamar (101–104, 201–204; kamar 204 berstatus perbaikan), tagihan bulan lalu
(lunas) dan bulan ini (belum bayar / verifikasi / lunas), serta 2 keluhan contoh.
Ganti semua password sebelum dipakai di luar lingkungan lokal.

## Fitur

**Admin**
- Dashboard: kamar tersedia vs terisi, pemasukan bulan ini, tagihan menunggu verifikasi, keluhan aktif.
- Kamar: tambah, lihat (filter status + pencarian), edit, hapus (diblokir jika punya riwayat kontrak).
- Kontrak sewa: buat kontrak untuk kamar `tersedia` (kamar otomatis `terisi`), edit tanggal,
  **Checkout** (kontrak `selesai`, kamar kembali `tersedia`), hapus.
- Tagihan: tambah manual, **Generate Bulan Ini** untuk semua kontrak aktif, lihat bukti transfer,
  **Verifikasi** (`lunas`) atau **Tolak** (kembali `belum_bayar`, file bukti dihapus), edit, hapus.
- Keluhan: lihat semua laporan dan majukan status `pending → diproses → selesai`.

**Penghuni**
- Dashboard: kamar yang ditempati, status tagihan bulan berjalan, daftar keluhan pribadi.
- Tagihan: riwayat tagihan dan unggah bukti (`jpg`, `png`, `pdf`, maks. 2 MB) ke
  `storage/app/public/bukti-bayar`; status otomatis menjadi `verifikasi`.
- Keluhan: buat (dengan foto), edit selama `pending`, hapus/batalkan selama `pending`.

## Keputusan desain & asumsi

- **Role** tidak ada di `$fillable` model `User`, sehingga registrasi publik selalu menghasilkan
  `penghuni` dan tidak bisa dimanipulasi lewat form. Akun admin hanya dari seeder.
- **Middleware role** (`App\Http\Middleware\RoleMiddleware`) dipasang langsung lewat nama kelas di
  `routes/web.php`, jadi `bootstrap/app.php` tidak perlu diubah.
- **Transaksi:** pembuatan kontrak, checkout, hapus kontrak, dan generate tagihan memakai
  `DB::transaction()`; kamar dikunci (`lockForUpdate`) saat kontrak dibuat agar tidak disewakan ganda.
- **Pemasukan bulan ini** = jumlah `nominal` tagihan berstatus `lunas` dengan `periode_bulan` bulan
  berjalan (skema tidak punya kolom tanggal bayar).
- **Satu penghuni hanya boleh punya satu kontrak aktif**, dan satu kontrak hanya satu tagihan per
  periode (unique `lease_id` + `periode_bulan`).
- **Status kamar** `terisi` hanya berubah lewat kontrak/checkout, bukan dari form kamar, agar data
  kamar dan kontrak tetap sinkron.
- Penghuni hanya dapat mengakses tagihan dan keluhan miliknya sendiri (cek kepemilikan → 403).
- Zona waktu mengikuti `config/app.php` (default UTC); ubah ke `Asia/Jakarta` jika perlu agar
  tanggal "hari ini" sesuai waktu lokal.
- Pesan validasi memakai bahasa Inggris bawaan Laravel (nama field sudah dalam bahasa Indonesia).

## Struktur singkat

```
app/Http/Controllers/{Admin,Penghuni}/   controller per role
app/Http/Requests/{Admin,Penghuni}/      Form Request (validasi + otorisasi)
app/Http/Middleware/RoleMiddleware.php   pembatas akses role
app/Models/                              User, Room, Lease, Payment, Complaint
database/migrations/                     users (+no_hp, role), rooms, leases, payments, complaints
database/seeders/DatabaseSeeder.php      data dummy
resources/views/{admin,penghuni}/        halaman Blade
routes/web.php                           route admin/penghuni + autentikasi Breeze
```
