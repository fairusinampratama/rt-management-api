# RT Management API

Backend API untuk aplikasi pengelolaan administrasi RT. Aplikasi ini menangani data penghuni, rumah, penempatan penghuni rumah, pembayaran iuran, pengeluaran, dan laporan keuangan.

## Teknologi

- PHP 8.2 sampai 8.4
- Laravel 12
- Composer
- MySQL
- Laravel Sanctum

## Instalasi

1. Clone repository.

   ```bash
   git clone https://github.com/fairusinampratama/rt-management-api.git
   cd rt-management-api
   ```

2. Install dependency PHP.

   ```bash
   composer install
   ```

3. Buat file environment.

   ```bash
   cp .env.example .env
   ```

4. Atur koneksi database MySQL di `.env`.

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rt_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Generate application key.

   ```bash
   php artisan key:generate
   ```

6. Buat symbolic link untuk akses file upload.

   ```bash
   php artisan storage:link
   ```

7. Jalankan migrasi dan seeder.

   ```bash
   php artisan migrate --seed
   ```

8. Jalankan server lokal.

   ```bash
   php artisan serve
   ```

API berjalan di `http://127.0.0.1:8000`.

## Akun Default

Seeder membuat akun admin berikut:

```text
Email: rt@example.com
Password: password
```

## Endpoint Utama

- `POST /api/login`
- `POST /api/logout`
- `GET /api/user`
- `apiResource /api/rumah`
- `apiResource /api/penghuni`
- `apiResource /api/pembayaran`
- `apiResource /api/pengeluaran`
- `apiResource /api/rumah-penghuni`
- `GET /api/laporan/summary`
- `GET /api/laporan/grafik`
- `GET /api/laporan/pemasukan`
- `GET /api/laporan/pengeluaran`
- `GET /api/laporan/saldo`

## Testing

```bash
composer test
```

Jika menggunakan environment lokal baru, pastikan `.env` sudah dibuat dan `APP_KEY` sudah terisi sebelum menjalankan aplikasi.
