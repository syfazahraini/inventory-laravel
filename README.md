# SyfaBuild Laravel — Inventory Management System

Migrasi proyek **SyfaBuild** (PHP native, Pertemuan 4) ke **Laravel 12** untuk tugas Pertemuan 5 (Laravel Fundamental). Aturan bisnis dari P4 dipertahankan; yang berubah adalah struktur kode: Route → Controller → Model → Database → View (Blade).

## Fitur

- Login dan logout (Laravel `Auth`), role **Owner / Gudang / Kasir** lewat middleware `RoleMiddleware`
- Kategori, Supplier, Produk: CRUD memakai **resource controller**
- Produk ↔ Kategori (`belongsTo` / `hasMany`) dan Produk ↔ Supplier (`belongsToMany`, tabel pivot `product_supplier`)
- Daftar produk: **search, filter kategori, sorting (allowlist), pagination**
- Stok masuk, keluar, dan koreksi dengan **database transaction** + `lockForUpdate`
- Kasir: total dan kembalian tampil langsung, struk, riwayat penjualan
- Dashboard: nilai persediaan, stok menipis, penjualan hari ini / minggu ini / bulan ini
- Validasi form dengan pesan error, tampilan Blade dengan **layout** dan **component**

## Aturan bisnis

1. SKU produk unik; harga, jumlah, dan stok minimum tidak boleh negatif.
2. Barang keluar / penjualan tidak boleh melebihi stok, dan stok tidak boleh negatif.
3. Setiap perubahan stok dicatat di `stock_movements` (siapa, kapan, berapa, alasan).
4. Penjualan atomik: `sales`, `sale_items`, pengurangan stok, dan histori stok berhasil bersama atau dibatalkan bersama.
5. Pembayaran tidak boleh kurang dari total belanja.
6. Produk dengan histori tidak boleh dihapus; kategori yang dipakai produk dan supplier yang terhubung produk tidak boleh dihapus.
7. Stok tidak diubah lewat form edit produk, hanya lewat transaksi stok.
8. Akun nonaktif tidak bisa login; akses di luar role mendapat **403**.

## Alur MVC (request sampai response)

Contoh: Gudang menyimpan produk baru.

1. **Request**: browser mengirim `POST /products` dari form.
2. **Route** (`routes/web.php`): `Route::resource('products', ProductController::class)` mengarahkan ke `ProductController@store`. Middleware `auth` dan `RoleMiddleware:gudang` dicek lebih dulu.
3. **Controller** (`ProductController@store`): memvalidasi input (`$request->validate(...)`). Jika gagal, otomatis kembali ke form dengan pesan error.
4. **Model** (`Product`, `StockMovement`): menyimpan data lewat Eloquent di dalam `DB::transaction`.
5. **Database**: tabel dibuat oleh migration (`php artisan migrate`).
6. **Response**: `redirect()->route('products.index')->with('success', ...)`, lalu **View** Blade (`resources/views/products/index.blade.php`) menampilkan daftar dan pesan sukses.

## Instalasi

Prasyarat: PHP 8.2+, Composer, MySQL (XAMPP).

```bash
composer install
copy .env.example .env        # Windows (Linux/Mac: cp .env.example .env)
php artisan key:generate
```

Buat database kosong `inventory_db` di phpMyAdmin, lalu isi `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
APP_TIMEZONE=Asia/Jakarta
```

Jalankan migration dan seeder, lalu server:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun demo (dibuat seeder)

| Peran | Username | Password |
|---|---|---|
| Owner | owner | OwnerSyfa2026! |
| Gudang | gudang1 | GudangSyfa2026! |
| Kasir | kasir1 | KasirSyfa2026! |

Akun ini hanya untuk demo tugas.

## Struktur penting

```
app/Http/Controllers/    Auth, Dashboard, Category, Supplier, Product, Stock, Sale
app/Http/Middleware/     RoleMiddleware
app/Models/              User, Category, Product, Supplier, StockMovement, Sale, SaleItem
database/migrations/     struktur tabel
database/factories/      CategoryFactory, ProductFactory
database/seeders/        DatabaseSeeder
resources/views/         layouts, components, dan halaman Blade
routes/web.php           semua route
```

## Screenshot

Simpan gambar di `docs/screenshots/`:

| Bukti | File |
|---|---|
| Daftar route | `docs/screenshots/01-route-list.png` |
| Login | `docs/screenshots/02-login.png` |
| Dashboard | `docs/screenshots/03-dashboard.png` |
| Daftar produk (search, filter, sort, pagination) | `docs/screenshots/04-produk.png` |
| Form produk dengan error validasi | `docs/screenshots/05-validasi.png` |
| Produk berhasil disimpan | `docs/screenshots/06-produk-tersimpan.png` |
| Stok keluar melebihi stok ditolak | `docs/screenshots/07-stok-ditolak.png` |
| Kasir dengan total dan kembalian | `docs/screenshots/08-kasir.png` |
| Akses terlarang 403 | `docs/screenshots/09-akses-403.png` |
| Hasil `migrate:fresh --seed` | `docs/screenshots/10-migrate-seed.png` |

## Batasan

Belum dimigrasi dari P4: manajemen karyawan (tambah, ganti password, hapus) dan halaman laporan. Pembatalan penjualan juga belum tersedia.
