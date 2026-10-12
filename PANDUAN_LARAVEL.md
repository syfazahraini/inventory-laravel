# Panduan memasang kode ke proyek inventory-laravel

1. Ekstrak `laravel_kode.zip`. Di dalamnya ada folder `app`, `database`, `public`, `resources`, `routes`, dan file `README.md`.
2. Buka `C:\xampp\htdocs\inventory-laravel`, lalu salin semua isi hasil ekstrak ke situ. Pilih **Replace** (timpa) untuk file yang namanya sama.
3. Buka `.env`, ubah bagian database menjadi MySQL (lihat README), dan tambahkan baris `APP_TIMEZONE=Asia/Jakarta`.
4. Nyalakan MySQL di XAMPP, pastikan database `inventory_db` sudah ada.
5. Di terminal VS Code (folder `inventory-laravel`): `php artisan migrate:fresh --seed`
6. Jalankan `php artisan serve`, buka http://127.0.0.1:8000, login dengan akun demo di README.
