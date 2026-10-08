# KasirApp

Sistem **Point of Sale (POS) / Kasir** berbasis Laravel untuk toko, warung, minimarket, dan multi-cabang.

Siap dipakai di lingkungan nyata: shift kasir, stok, pembelian ke supplier, laporan, notifikasi, dan API mobile.

---

## Fitur

### Operasional
- Login dengan role **Admin** & **Kasir**
- **Shift kasir** — buka/tutup shift, modal awal, hitung uang laci, selisih kas
- **POS** — cari/scan barcode, keranjang, diskon, PPN, metode bayar (tunai, transfer, QRIS, lainnya)
- **Pelanggan** — master data + pencarian di kasir
- **Transaksi** — riwayat, detail, cetak struk, **refund** (stok dikembalikan)

### Inventori
- Produk & kategori (CRUD, gambar, soft delete, stok minimal)
- **Mutasi stok** — masuk, keluar, penyesuaian (stock opname) + audit trail
- **Supplier** & **Pembelian (PO)** — draft / terima barang / batal, stok otomatis update

### Multi Cabang
- Master cabang
- User dan produk dapat dikaitkan ke cabang

### Laporan & Notifikasi
- Dashboard statistik + grafik 7 hari + alert stok menipis
- Laporan penjualan (filter tanggal, top produk, metode bayar)
- **Export CSV** & **Export PDF**
- Notifikasi in-app (stok menipis)

### Admin
- Pengaturan toko (nama, alamat, telepon, PPN, footer struk)
- Manajemen pengguna

### API Mobile
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/api/login` | Login, dapat token |
| GET | `/api/me` | Profil user |
| POST | `/api/logout` | Logout |
| GET | `/api/products?q=` | Daftar/cari produk |
| POST | `/api/checkout` | Checkout transaksi |
| GET | `/api/shifts/current` | Shift aktif |

Header: `Authorization: Bearer {token}`

---

## Stack

- PHP 8.2+ / Laravel 11+
- Bootstrap 5.3 + Bootstrap Icons
- Chart.js
- SQLite (default) atau MySQL/MariaDB

---

## Instalasi

### Requirement
- PHP 8.2+ dengan ekstensi: `mbstring`, `xml`, `curl`, `zip`, `sqlite3` atau `pdo_mysql`, `gd`, `bcmath`
- Composer
- Node.js (opsional, jika build asset Vite)

### Langkah

```bash
# Clone
git clone <url-repo-anda> kasir-app
cd kasir-app

# Dependency
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Database — pilih salah satu:

# A) SQLite (cepat untuk development)
touch database/database.sqlite
# pastikan di .env: DB_CONNECTION=sqlite

# B) MySQL (disarankan production)
# Edit .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kasir
# DB_USERNAME=root
# DB_PASSWORD=secret

# Migrasi + data contoh
php artisan migrate --seed

# Storage untuk gambar produk
php artisan storage:link

# Jalankan
php artisan serve
```

Buka: [http://localhost:8000](http://localhost:8000)

### Akun demo

| Role  | Email             | Password  |
|-------|-------------------|-----------|
| Admin | admin@kasir.com   | password  |
| Kasir | kasir@kasir.com   | password  |

---

## Cara pakai (ringkas)

### Kasir harian
1. Login sebagai **Kasir**
2. **Shift Kasir** → Buka Shift (isi modal awal)
3. **Kasir / POS** → scan/pilih produk → bayar → cetak struk
4. Akhir hari → **Tutup Shift** (hitung uang di laci)

### Pembelian
1. **Supplier** → tambah supplier
2. **Pembelian** → buat PO → centang *terima sekarang* atau Terima nanti
3. Stok produk otomatis bertambah saat diterima

### Admin
- **Pengaturan** → nama toko, PPN, footer struk
- **Cabang**, **Pengguna**, **Laporan** (+ export CSV/PDF)
- **Notifikasi** → pantau stok menipis

---

## Struktur folder penting

```
app/
  Http/Controllers/     # Web + Api/
  Models/
  Http/Middleware/      # Admin, ApiToken
database/
  migrations/
  seeders/
resources/views/        # Blade UI
routes/
  web.php
  api.php
```

---

## Production checklist

- [ ] Ganti password akun default
- [ ] `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] Gunakan MySQL/MariaDB (bukan SQLite)
- [ ] HTTPS (SSL)
- [ ] Backup database rutin
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] `php artisan storage:link`
- [ ] Sesuaikan pengaturan toko

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Contoh API

```bash
# Login
curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"kasir@kasir.com","password":"password"}'

# Produk
curl -s http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN"

# Checkout
curl -s -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items":[{"product_id":1,"quantity":2}],
    "paid":50000,
    "payment_method":"cash"
  }'
```

---

## Lisensi

Proyek ini dibuat untuk pembelajaran dan penggunaan operasional toko.  
Silakan modifikasi sesuai kebutuhan.

---

**KasirApp** — dari kasir harian sampai multi-cabang & API mobile.
