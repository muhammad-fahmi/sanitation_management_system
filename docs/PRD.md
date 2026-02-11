# PROJECT REQUIREMENT DOCUMENT (PRD) — Bionic Backend

## 1. Identitas Proyek

- **Nama Proyek:** Bionic Backend
- **Framework:** CodeIgniter 4 (PHP ^8.1)
- **Timezone:** Asia/Jakarta
- **Bahasa Utama:** Indonesia (`id`) & Inggris (`en`)
- **Database:** MySQLi (Produksi), SQLite (Testing)

## 2. Ringkasan Eksekutif

Bionic adalah sistem manajemen tugas operasional yang memfasilitasi pelaporan, verifikasi, dan administrasi tugas berbasis ruang dan item. Sistem ini menggunakan arsitektur MVC (Model-View-Controller) dengan otentikasi berbasis JWT yang terikat pada sesi server (Session-Bound JWT) untuk keamanan tinggi.

## 3. Aktor & Peran (User Roles)

Sistem memiliki 3 peran utama yang diatur melalui RBAC (Role-Based Access Control):

1.  **Admin**
    - Memiliki akses penuh ke seluruh sistem.
    - Bertanggung jawab atas manajemen pengguna (Users), peran (Roles), izin (Permissions).
    - Mengelola data master: Ruangan (Rooms), Barang (Items), dan Aksi (Actions).
    - Melihat dashboard statistik.

2.  **Operator**
    - Pengguna lapangan yang melakukan pelaporan tugas.
    - Dapat membuat pengajuan tugas (`TaskSubmission`).
    - Mengisi detail item, melakukan aksi (checklist/tindakan), dan mengunggah lampiran bukti.

3.  **Verifikator**
    - Bertanggung jawab memvalidasi pekerjaan Operator.
    - Dapat melihat daftar pengajuan tugas.
    - Melakukan persetujuan (Approve) atau penolakan (Reject) terhadap tugas.

## 4. Spesifikasi Teknis & Stack

- **Backend Core:** CodeIgniter 4.
- **Security Library:** `Sodium` extension (wajib) untuk tanda tangan digital EdDSA.
- **Authentication:**
  - Custom `JwtService` (`app/Libraries/JwtService.php`).
  - Token disimpan di Session server, bukan hanya di sisi klien (Stateful JWT).
  - Algoritma: EdDSA (Ed25519).
- **Frontend Assets:** Menggunakan library pihak ketiga seperti TinyMCE/CKEditor (untuk input teks kaya) dan jQuery Validation.

## 5. Fitur Fungsional

### A. Otentikasi & Otorisasi

- **Login/Logout:** Validasi kredensial user, generate JWT EdDSA, simpan public key di session.
- **Session Check:** Setiap request ke controller yang diproteksi harus memvalidasi keberadaan token di session.
- **RBAC:** Middleware/Filter untuk mengecek apakah user memiliki `permission` atau `role` tertentu sebelum mengakses rute Admin.

### B. Manajemen Tugas (Task Submission)

- **Struktur Tugas:** Satu `TaskSubmission` terdiri dari banyak `TaskSubmissionItems`.
- **Detail Item:** Setiap item memiliki `TaskSubmissionActions` (tindakan yang dilakukan) dan `TaskSubmissionAttachments` (bukti foto/dokumen).
- **History:** Setiap perubahan status tugas dicatat dalam `TaskSubmissionHistories`.

### C. Manajemen Master Data (Admin)

- **Rooms:** Lokasi pengerjaan tugas.
- **Items:** Objek yang dikerjakan dalam ruangan.
- **Actions:** Daftar tindakan standar yang bisa dilakukan pada item.

## 6. Struktur Data (Skema Database)

Berdasarkan migrasi yang ada, entitas utama meliputi:

- `users`: Data akun (email, password hash, status).
- `auth_groups` / `roles`: Definisi peran (Admin, Operator, Verifikator).
- `auth_permissions`: Hak akses granular.
- `rooms`: Data ruangan.
- `items`: Data barang/aset.
- `actions`: Data jenis tindakan.
- `task_submissions`: Header transaksi pengajuan.
- `task_submission_items`: Detail barang dalam pengajuan.
- `task_submission_actions`: Detail tindakan pada barang tersebut.
- `task_submission_attachments`: File bukti pendukung.

## 7. Aturan Bisnis (Business Rules)

1.  **Keamanan Token:** Jika session server hilang (misal server restart atau session timeout), JWT yang dipegang klien menjadi tidak valid (karena public key validasi ada di session).
2.  **Alur Verifikasi:** Tugas yang disubmit Operator berstatus _Pending_. Verifikator mengubah status menjadi _Approved_ atau _Rejected_.
3.  **Audit Trail:** Segala perubahan status tugas harus masuk ke tabel history.

## 8. Panduan Pengembangan (Development Guidelines)

- **Controller:** Gunakan `BaseController` untuk inisialisasi service umum (`JwtService`).
- **Routing:**
  - Group `/admin` untuk fitur manajemen.
  - Group `/auth` untuk login/logout.
  - (To-Do) Aktifkan route group `/operator` dan `/verifikator` yang saat ini dikomentari di `Routes.php`.
- **Testing:** Gunakan SQLite in-memory untuk unit testing agar cepat. Pastikan `HealthTest` lulus.

---

_Dokumen ini digunakan sebagai acuan utama (Source of Truth) untuk pengembangan fitur dan perbaikan bug oleh AI Assistant._
