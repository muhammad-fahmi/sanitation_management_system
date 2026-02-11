# PROJECT_OVERVIEW — Bionic Backend

## 🔍 Ringkasan singkat

- Framework: **CodeIgniter 4** (PHP ^8.1)
- Waktu & Lokalisasi: timezone `Asia/Jakarta`, locales `en`/`id`
- Otentikasi: **JWT** berbasis EdDSA (session-bound) di `app/Libraries/JwtService.php`
- Peran: **admin**, **operator**, **verifikator**
- DB: default MySQLi (kredensial kosong di `app/Config/Database.php`), testing pakai SQLite in-memory

---

## 📂 Struktur & statistik cepat

- Controllers utama: `app/Controllers` (4 file + `Admin/` subfolder)
  - `BaseController.php`, `Auth.php`, `Operator.php`, `Verifikator.php`
  - `app/Controllers/Admin/` (10 controllers: `Action`, `Dashboard`, `Item`, `Permission`, `Role`, `RolePermission`, `Room`, `Task`, `User`, `UserRole`)
- Models: `app/Models` (11)
  - `ActionModel`, `ItemModel`, `PermissionModel`, `RolePermissionModel`, `RolesModel`, `RoomModel`, `TaskSubmissionActionsModel`, `TaskSubmissionAttachmentsModel`, `TaskSubmissionItemsModel`, `TaskSubmissionModel`, `UserModel`, `UserRoleModel`
- Libraries: `app/Libraries/JwtService.php`
- Migrations: `app/Database/Migrations` (13 files: Users, Roles, Permissions, UserRoles, RolePermissions, Rooms, Items, Actions, TaskSubmissions, TaskSubmissionItems, TaskSubmissionActions, TaskSubmissionAttachments, TaskSubmissionHistories)
- Views: `app/Views` (layout, auth, admin, operator, verifikator, errors)
- Tests: `tests/` (unit `HealthTest.php`, database tests, session example)

---

## 🔧 File inti dan peran singkat

- `app/Libraries/JwtService.php`
  - Encode/Decode JWT EdDSA; menyimpan token & public key di session (`jwt`, `jwt_public_key`). Perlu ekstensi `sodium`.
- `app/Controllers/Auth.php`
  - Login/logout flow; memvalidasi user & menyimpan JWT. Role mapping ke `admin|operator|verifikator`.
- `app/Controllers/BaseController.php`
  - Inisialisasi `JwtService` untuk semua controller; memuat data penanda revisi ke session bila user operator.
- `app/Models/TaskSubmissionModel.php`
  - Logika kompleks untuk query datatable, penghitungan totals, dan retrieval detail pengiriman tugas.
- `app/Config/Routes.php`
  - Route group untuk `auth` dan `admin` (operator/verifikator group dikomentari, meski kelasnya ada).

---

## ⚙️ Setup & Perintah penting

1. Install dependencies:
   - `composer install`
2. Konfigurasi environment:
   - Salin `.env` contoh dan set DB credentials (`app.baseURL`, DB host/user/pass/db)
3. Jalankan migrasi & seed (jika ada seeders):
   - `php spark migrate`
   - `php spark db:seed <SeederName>`
4. Jalankan server untuk development:
   - `php spark serve --port=8080` atau atur virtual host ke `public/`
5. Jalankan test suite:
   - `composer test` atau `./vendor/bin/phpunit`

---

## ⚠️ Gotchas & catatan

- JWT EdDSA membutuhkan ekstensi sodium dan token disimpan dalam session sehingga kehilangan session membuat token tidak bisa didecode.
- `app/Config/Database.php` default memiliki kredensial kosong — pastikan diisi sebelum migrasi.
- Beberapa routes untuk `operator` dan `verifikator` dikomentari; cek apakah sengaja atau perlu diaktifkan.

---

## ✅ Rekomendasi langkah berikutnya

- Tambahkan file `docs/PROJECT_OVERVIEW.md` (sudah dibuat) dan perbarui bila ada perubahan struktur.
- Buat `SEEDERS.md` jika banyak seeders atau contoh data yang perlu dipersiapkan.
- Tambah dokumentasi singkat untuk `JwtService` (contoh penggunaan, dependensi sodium).

---

## Lampiran: Daftar file penting (path singkat)

- `app/Controllers/BaseController.php`
- `app/Controllers/Auth.php`
- `app/Controllers/Operator.php`
- `app/Controllers/Verifikator.php`
- `app/Controllers/Admin/*` (Action, Dashboard, Item, Permission, Role, RolePermission, Room, Task, User, UserRole)
- `app/Models/*` (lihat folder)
- `app/Libraries/JwtService.php`
- `app/Config/Routes.php`
- `app/Database/Migrations/*`
- `tests/*`

---

_Dokumen ini dihasilkan otomatis dari pemindaian workspace pada saat ini. Untuk perincian lebih lanjut (diagram ER, flow auth, atau daftar endpoint lengkap), beri tahu saya bagian mana yang ingin Anda perluas._
