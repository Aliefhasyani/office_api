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

---
## Postman Documentation

Dokumentasi lengkap mengenai request, response, dan contoh penggunaan API ini dapat dilihat secara interaktif melalui link Postman berikut:

 [**Lihat Postman Documentation**](https://documenter.getpostman.com/view/45366378/2sBXqNmdZw) 

---
## Daftar Endpoint API

Semua endpoint memiliki prefix `/api`. 

## Panduan Pengujian OAuth (Google)

Alur pengujian otentikasi OAuth :
1. **Dapatkan Redirect URL:** Lakukan request ke `GET http://127.0.0.1:8000/api/login/google`.
2. **Login via Browser:** Salin dan buka URL tersebut pada browser.
3. **Persetujuan Otentikasi:** Pilih akun Google yang akan digunakan untuk Login.
4. **Dapatkan Token:** Setelah berhasil login, sistem akan mengembalikan JSON yang berisi profil user dan `access_token` Sanctum.
5. Gunakan `access_token` yang didapatkan tersebut sebagai `Bearer Token` pada header otorisasi pada saat mengakses endpoint API yang dilindungi.

---



### 1. Otentikasi & Akun
*   `POST /api/register`
    *   **Fungsi:** Mendaftarkan pengguna (karyawan/admin) baru.
*   `POST /api/login`
    *   **Fungsi:** Melakukan login konvensional menggunakan email dan password, mengembalikan access token.
*   `GET /api/login/{provider}` 
    *   **Fungsi:** Memulai alur otentikasi OAuth Google.
*   `GET /api/login/{provider}/callback`
    *   **Fungsi:** Menangani respons dari provider (Google) setelah user berhasil login, dan mendaftarkan user secara otomatis apabila belum ada, lalu mengembalikan access token.
*   `POST /api/logout` *(Membutuhkan Auth Token)*
    *   **Fungsi:** Melakukan logout, token pengguna saat ini akan dicabut (revoked).
*   `GET /api/user` *(Membutuhkan Auth Token)*
    *   **Fungsi:** Mendapatkan informasi profil pengguna yang saat ini sedang login beserta perannya (role).

### 2. Manajemen Karyawan (Employees)
*(Membutuhkan Auth Token)*
*   `GET /api/employees`
    *   **Fungsi:** Mengambil daftar semua karyawan yang ada di database.
*   `POST /api/employees`
    *   **Fungsi:** Menambahkan data karyawan baru.
*   `GET /api/employees/{id}`
    *   **Fungsi:** Mengambil detail informasi karyawan berdasarkan ID-nya.
*   `PUT /api/employees/{id}` atau `PATCH /api/employees/{id}`
    *   **Fungsi:** Memperbarui data karyawan yang sudah ada berdasarkan ID.
*   `DELETE /api/employees/{id}`
    *   **Fungsi:** Menghapus data karyawan berdasarkan ID.

### 3. Manajemen Cuti (Leaves)
*(Membutuhkan Auth Token)*
*   `GET /api/leaves`
    *   **Fungsi:** Mengambil daftar pengajuan cuti. 
    *   **Aturan Role:** Jika diakses oleh `admin`, akan menampilkan semua data pengajuan cuti karyawan. Jika diakses oleh `employee`, hanya menampilkan riwayat cuti miliknya sendiri.
*   `POST /api/leaves` *(Khusus Role: `employee`)*
    *   **Fungsi:** Membuat pengajuan cuti baru. Dapat menyertakan dokumen pendukung (lampiran / *attachment*).
*   `PATCH /api/leaves/{id}/status` *(Khusus Role: `admin`)*
    *   **Fungsi:** Menyetujui atau menolak (approve/reject) pengajuan cuti yang spesifik berdasarkan ID.


