# Kasir App

Aplikasi kasir berbasis web untuk membantu toko mengelola penjualan, stok, pembelian, pelanggan, pemasok, dan laporan dari satu tempat. Aplikasi mendukung beberapa cabang serta membedakan akses admin dan kasir.

## Latar Belakang

Operasional toko mencakup banyak data dan kegiatan harian. Jika pencatatan penjualan, persediaan, dan pembelian dilakukan secara terpisah, pemilik dan staf dapat mengalami beberapa kendala:

- **Transaksi sulit dipantau.** Pencatatan manual membuat pencarian riwayat transaksi dan pembuatan laporan memerlukan waktu.
- **Stok tidak selalu akurat.** Perubahan stok akibat penjualan, barang masuk, atau penyesuaian perlu dicatat secara konsisten.
- **Data operasional tersebar.** Informasi produk, kategori, pelanggan, pemasok, serta cabang lebih sulit dikelola ketika berada di tempat berbeda.
- **Proses kerja perlu dibatasi sesuai peran.** Admin mengelola konfigurasi dan pengguna, sedangkan kasir berfokus pada transaksi dan operasional toko.

Kasir App menyatukan kegiatan tersebut dalam satu aplikasi. Data dapat dikelola dari dashboard, transaksi tercatat beserta detailnya, dan laporan dapat diekspor sesuai kebutuhan.

## Fitur

### Admin

- Dashboard ringkasan operasional.
- Kelola pengguna dan cabang.
- Kelola kategori, produk, pelanggan, dan pemasok.
- Kelola stok serta pembelian barang.
- Lihat dan kelola transaksi, termasuk proses pengembalian dana.
- Lihat laporan dan ekspor laporan ke CSV atau PDF.
- Atur informasi toko, tarif pajak, mata uang, dan catatan struk.

### Kasir

- Buka dan tutup shift.
- Cari produk dan pelanggan saat melakukan checkout.
- Catat transaksi penjualan dan lihat struk.
- Lihat transaksi, stok, dan laporan sesuai akses yang diberikan.
- Terima notifikasi operasional.

### API

- Login menggunakan token, melihat profil, logout, melihat produk, checkout, dan melihat shift aktif tersedia melalui API.

## Teknologi

- PHP dan Laravel
- Blade, Vite, dan JavaScript
- SQLite secara default; MySQL dapat digunakan melalui konfigurasi database Laravel
- API berbasis token untuk kebutuhan klien POS

## Persyaratan

- PHP sesuai versi yang disyaratkan oleh `composer.json` dan ekstensi Laravel yang diperlukan
- Composer
- Node.js dan npm
- SQLite atau MySQL

## Menjalankan Aplikasi

Clone repository dan pasang dependensi:

```sh
git clone <URL-REPOSITORY> kasir-app
cd kasir-app
composer install
npm install
```

Salin konfigurasi contoh jika `.env` belum tersedia, lalu buat application key:

```sh
cp .env.example .env
php artisan key:generate
```

Di Windows PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti `cp`. Jika `.env` sudah ada, jangan menimpanya. Jalankan `php artisan key:generate` hanya bila `APP_KEY` belum terisi.

Konfigurasi bawaan memakai SQLite. Pastikan file database tersedia, kemudian jalankan migrasi dan data contoh:

```sh
php artisan migrate --seed
```

Untuk memakai MySQL, buat database dan sesuaikan variabel `DB_*` di `.env`, lalu jalankan perintah migrasi dan seeder yang sama.

Build aset dan jalankan aplikasi:

```sh
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Untuk pengembangan frontend, jalankan `npm run dev` di terminal terpisah.

## Akun Demo

Seeder membuat akun berikut dengan kata sandi awal `password`:

| Peran | Email |
| --- | --- |
| Admin | `admin@kasir.com` |
| Kasir | `kasir@kasir.com` |

Seeder juga menambahkan contoh cabang, kategori, produk, pelanggan, pemasok, dan pengaturan toko. Ganti kata sandi demo sebelum aplikasi digunakan di lingkungan nyata.

## Konfigurasi

Pengaturan dasar aplikasi berada di `.env`, termasuk:

- `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL` untuk identitas dan mode aplikasi.
- `DB_CONNECTION` dan variabel `DB_*` untuk koneksi database.
- `SESSION_DRIVER`, `CACHE_STORE`, dan `QUEUE_CONNECTION` untuk penyimpanan sesi, cache, dan antrean.

Pengaturan toko seperti nama, alamat, nomor telepon, email, tarif pajak, mata uang, dan footer struk dikelola melalui halaman **Pengaturan** dan disimpan di database. Jangan menyimpan kredensial produksi di repository.

## Perintah Berguna

```sh
php artisan serve       # Jalankan server lokal
npm run dev             # Jalankan Vite dalam mode pengembangan
npm run build           # Build aset frontend
php artisan migrate     # Jalankan migrasi database
php artisan db:seed     # Isi data contoh
php artisan test        # Jalankan test suite
```

## Deployment

- Gunakan `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` yang sesuai.
- Arahkan document root web server ke direktori `public`.
- Pastikan `storage` dan `bootstrap/cache` dapat ditulis oleh web server.
- Gunakan kredensial database dan kata sandi yang kuat untuk semua akun.
- Build aset frontend dengan `npm run build` sebelum deployment.

---

Kasir App — pengelolaan penjualan dan operasional toko dalam satu aplikasi.
