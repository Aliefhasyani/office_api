## Stack Teknologi
* Framework: Laravel 11
* Database: MySQL
* Authentication: Laravel Sanctum & Socialite (OAuth 2.0)

---

## Panduan Instalasi dan Setup

### 1. Kebutuhan Sistem
* PHP >= 8.2
* Composer
* MySQL

### 2. Instalasi
Clone repositori ke lingkungan lokal dan masuk ke dalam direktori proyek:
```bash
git clone <repo-url>
cd office_api
```

Instal dependensi:
```bash
composer install
```

### 3. Setup Environment (.env)
Salin file environment dan *generate* application key:
```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi parameter database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=office_api
DB_USERNAME=root
DB_PASSWORD=
```

**Konfigurasi OAuth (Google)**
Tambahkan credentials dari Google Cloud Console ke dalam file `.env`:
```env
GOOGLE_CLIENT_ID=client_id_anda.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=client_secret_anda
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/login/google/callback
```

### 4. Migrasi Database dan Storage
Jalankan migrasi untuk membangun skema tabel:
```bash
php artisan migrate
```

Buat storage link agar dokumen lampiran cuti dapat diakses secara publik:
```bash
php artisan storage:link
```

Jalankan server aplikasi:
```bash
php artisan serve
```


