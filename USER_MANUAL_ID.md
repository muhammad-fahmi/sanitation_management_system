# Panduan Penggunaan Aplikasi Bionic Backend

## Daftar Isi

1. [Tujuan Dokumen](#1-tujuan-dokumen)
2. [Ruang Lingkup](#2-ruang-lingkup)
3. [Role dan Hak Akses](#3-role-dan-hak-akses)
4. [Alur Umum Penggunaan](#4-alur-umum-penggunaan)
   - [4.1 Sebelum Memulai](#41-sebelum-memulai)
   - [4.2 Login](#42-login)
   - [4.3 Logout](#43-logout)
   - [4.4 Arti Status Task](#44-arti-status-task)
5. [Panduan Admin](#5-panduan-admin)
   - [5.1 Dashboard Admin](#51-dashboard-admin)
   - [5.2 Manajemen User](#52-manajemen-user)
   - [5.3 Manajemen Master Task](#53-manajemen-master-task)
   - [5.4 Checklist Akhir Admin](#54-checklist-akhir-admin)
6. [Panduan Operator](#6-panduan-operator)
   - [6.1 Dashboard Operator](#61-dashboard-operator)
   - [6.2 Submit Task](#62-submit-task)
   - [6.3 Menangani Revisi](#63-menangani-revisi)
   - [6.4 Batalkan Submission](#64-batalkan-submission)
7. [Panduan Verifikator](#7-panduan-verifikator)
   - [7.1 Review Data Submission](#71-review-data-submission)
   - [7.2 Verifikasi Per Task](#72-verifikasi-per-task)
   - [7.3 Verifikasi Massal](#73-verifikasi-massal)
   - [7.4 Rekapitulasi dan Export](#74-rekapitulasi-dan-export)
8. [Troubleshooting](#8-troubleshooting)
9. [FAQ](#9-faq)
10. [Catatan Fitur WIP](#10-catatan-fitur-wip)
11. [Kontrol Revisi Dokumen](#11-kontrol-revisi-dokumen)

---

## 1. Tujuan Dokumen

Dokumen ini adalah panduan penggunaan aplikasi untuk tiga peran utama: Admin, Operator, dan Verifikator. Fokus panduan adalah alur operasional harian, bukan instalasi teknis.

---

## 2. Ruang Lingkup

Panduan ini mencakup:

1. Login dan logout.
2. Alur penggunaan fitur utama per role.
3. Arti status task.
4. Troubleshooting umum untuk pengguna.

Panduan ini **tidak** mencakup:

1. Instalasi server, database, atau deployment.
2. Konfigurasi environment.
3. Referensi API untuk developer.

---

## 3. Role dan Hak Akses

| Role | Tanggung Jawab |
|------|---------------|
| **Admin** | Mengelola user, lokasi, item, action checklist, serta memantau ringkasan dashboard. |
| **Operator** | Mengerjakan task kebersihan, melakukan scan item, submit hasil pekerjaan, dan menindaklanjuti revisi. |
| **Verifikator** | Meninjau submission operator, melakukan verifikasi atau revisi, serta melihat ringkasan laporan. |

---

## 4. Alur Umum Penggunaan

### 4.1 Sebelum Memulai

1. Pastikan Anda memiliki akun aktif.
2. Pastikan role akun Anda sesuai tugas kerja.
3. Gunakan browser yang stabil dan koneksi internet memadai.

---

### 4.2 Login

**Langkah:**

1. Buka halaman login aplikasi.
2. Masukkan username dan password.
3. Klik tombol **Login**.
4. Sistem akan mengarahkan Anda ke dashboard sesuai role.

**Hasil yang diharapkan:**

| Role | Halaman Tujuan |
|------|---------------|
| Admin | Halaman Admin |
| Operator | Halaman Operator |
| Verifikator | Halaman Verifikator |

---

### 4.3 Logout

**Langkah:**

1. Klik menu **Logout**.
2. Sistem mengakhiri sesi login.
3. Anda kembali ke halaman login.

---

### 4.4 Arti Status Task

| Status | Arti |
|--------|------|
| `pending` | Task sudah disubmit operator dan menunggu verifikasi. |
| `verified` | Task sudah disetujui verifikator. |
| `revisi` | Task dikembalikan verifikator untuk diperbaiki operator. |
| `revised` | Task sudah diperbaiki operator dan menunggu verifikasi ulang. |

---

## 5. Panduan Admin

### 5.1 Dashboard Admin

**Tujuan:** Melihat ringkasan data operasional.

**Langkah:**

1. Login sebagai Admin.
2. Buka halaman Dashboard Admin.
3. Periksa statistik utama seperti jumlah user, jumlah lokasi, dan ringkasan aktivitas.

> **Catatan:** Gunakan dashboard sebagai indikator cepat. Validasi detail tetap dilakukan dari halaman manajemen data.

---

### 5.2 Manajemen User

**Tujuan:** Menambah, mengubah, dan menghapus akun pengguna.

**Tambah User:**

1. Buka menu **Manajemen User**.
2. Klik **Tambah User**.
3. Isi data wajib: nama, username, password, dan role.
4. Klik **Simpan**.

**Edit User:**

1. Cari user pada tabel.
2. Klik **Edit** pada baris yang sesuai.
3. Ubah data yang diperlukan.
4. Klik **Simpan**.

**Hapus User:**

1. Cari user pada tabel.
2. Klik **Hapus** pada baris yang sesuai.
3. Konfirmasi penghapusan pada dialog yang muncul.

**Hasil yang diharapkan:** Perubahan muncul pada tabel user secara langsung.

---

### 5.3 Manajemen Master Task

**Urutan manajemen data:** Location → Item → Action

> **Prinsip penting:**
> - Item harus berada dalam Location.
> - Action harus berada dalam Item.
> - Perubahan atau penghapusan parent data dapat berdampak pada data turunannya.

**Langkah umum:**

1. Buka menu **Manajemen Task**.
2. Tambahkan atau pilih **Location**.
3. Tambahkan **Item** untuk Location tersebut.
4. Tambahkan **Action** untuk Item tersebut.
5. Lakukan edit atau hapus sesuai kebutuhan dengan memperhatikan relasi data.

---

### 5.4 Checklist Akhir Admin

Sebelum mengakhiri sesi, pastikan:

- [ ] Data user terbaru sudah tersimpan.
- [ ] Struktur Location → Item → Action sudah konsisten.
- [ ] Tabel menampilkan data sesuai perubahan yang dilakukan.

---

## 6. Panduan Operator

### 6.1 Dashboard Operator

**Tujuan:** Melihat daftar lokasi kerja dan status submission hari ini.

**Langkah:**

1. Login sebagai Operator.
2. Buka dashboard operator.
3. Tinjau daftar lokasi yang ditampilkan beserta status masing-masing.
4. Pilih lokasi kerja untuk memulai task.

---

### 6.2 Submit Task

**Langkah:**

1. Dari dashboard, pilih lokasi yang akan dikerjakan.
2. Masuk ke halaman scan lokasi tersebut.
3. Pilih atau scan item yang akan dikerjakan.
4. Buka form checklist action.
5. Tandai action yang sudah dilakukan.
6. Klik **Kirim** untuk submit.

**Hasil yang diharapkan:** Status task menjadi `pending`, menunggu verifikasi.

---

### 6.3 Menangani Revisi

**Langkah:**

1. Buka halaman **Revisi**.
2. Pilih task dengan status `revisi`.
3. Baca catatan perbaikan dari verifikator dengan saksama.
4. Lakukan perbaikan sesuai catatan.
5. Jika tersedia, unggah bukti revisi (foto).
6. Klik **Kirim Ulang**.

**Hasil yang diharapkan:** Status berubah menjadi `revised` dan task masuk antrean verifikasi ulang.

---

### 6.4 Batalkan Submission

Gunakan fitur ini jika submission salah kirim atau perlu ditarik sebelum diproses lebih lanjut.

**Langkah:**

1. Temukan submission yang ingin dibatalkan.
2. Klik **Batalkan**.
3. Konfirmasi pembatalan.

> **Catatan:** Setelah dibatalkan, task perlu disubmit ulang jika masih ingin diproses.

---

## 7. Panduan Verifikator

### 7.1 Review Data Submission

**Langkah:**

1. Login sebagai Verifikator.
2. Buka halaman verifikasi.
3. Gunakan filter **lokasi** dan **tanggal** untuk menyempurnakan tampilan data.
4. Cari task yang ingin ditinjau.
5. Klik baris task untuk membuka detail.

---

### 7.2 Verifikasi Per Task

**Approve task:**

1. Buka detail task.
2. Periksa data checklist dan informasi pendukung.
3. Klik **Approve** atau **Verify**.

**Kembalikan untuk revisi:**

1. Buka detail task.
2. Klik **Revisi**.
3. Isi catatan revisi yang jelas dan operasional agar operator dapat langsung bertindak.
4. Klik **Simpan**.

---

### 7.3 Verifikasi Massal

**Prasyarat:**

1. Filter lokasi dan tanggal sudah dikonfigurasi dengan benar.
2. Task pada filter sudah ditinjau secara sampling atau sesuai SOP internal tim.

**Langkah:**

1. Terapkan filter lokasi dan tanggal.
2. Klik **Verify All**.
3. Pastikan status semua task berubah sesuai aksi.

> **Catatan:** Gunakan fitur ini dengan hati-hati. Aksi Verify All tidak dapat dibatalkan massal.

---

### 7.4 Rekapitulasi dan Export

**Langkah:**

1. Buka menu **Laporan Rekapitulasi**.
2. Pilih rentang data yang dibutuhkan (lokasi, tanggal).
3. Tinjau ringkasan yang ditampilkan.
4. Klik **Export** jika laporan perlu disimpan atau dibagikan.

---

## 8. Troubleshooting

### 8.1 Sesi Login Berakhir

**Gejala:** Anda tiba-tiba diarahkan kembali ke halaman login.

**Tindakan:**

1. Login ulang menggunakan username dan password.
2. Jika masalah berulang dalam waktu singkat, hubungi Admin untuk pengecekan akun.

---

### 8.2 Data Tidak Muncul di Tabel

**Tindakan:**

1. Cek filter tanggal dan lokasi — pastikan rentangnya sesuai.
2. Hapus semua filter lalu cari ulang.
3. Pastikan data sudah benar-benar disubmit sebelumnya.

---

### 8.3 Status Tidak Berubah Setelah Aksi

**Tindakan:**

1. Refresh halaman dan periksa kembali status.
2. Pastikan aksi terakhir sudah dikonfirmasi (tidak tertutup sebelum tersimpan).
3. Ulangi proses aksi jika diperlukan.

---

## 9. FAQ

**Q: Apakah Operator bisa memverifikasi task sendiri?**
A: Tidak. Verifikasi hanya dapat dilakukan oleh Verifikator.

**Q: Apakah Admin bisa mengubah role user yang sudah ada?**
A: Ya, melalui fitur Edit di menu Manajemen User.

**Q: Kapan fitur Verify All aman digunakan?**
A: Saat filter data sudah tepat dan proses review internal (sampling) sudah terpenuhi.

**Q: Apa yang terjadi jika operator membatalkan submission yang sudah pending?**
A: Submission dihapus dan harus disubmit ulang jika masih dibutuhkan.

**Q: Apakah verifikator bisa melihat riwayat revisi?**
A: Ya, detail task menampilkan catatan revisi beserta riwayat status sebelumnya.

---

## 10. Catatan Fitur WIP

Fitur-fitur berikut masih dalam pengembangan dan mungkin belum berfungsi sepenuhnya:

1. **Manajemen Shift** — Penugasan shift operator dan rotasi shift belum tersedia secara penuh.
2. **Alur verifikasi lanjutan** — Beberapa alur verifikasi tambahan masih dalam proses pengembangan.
3. **Pelacakan kode unik (QR)** — Fitur pelacakan berbasis kode unik tersedia namun perilakunya dapat berbeda tergantung konfigurasi sistem.

---

## 11. Kontrol Revisi Dokumen

| Field | Nilai |
|-------|-------|
| Versi | Draft 1 |
| Tanggal | 27 April 2026 |
| Status | Siap review internal |
| Dibuat oleh | GitHub Copilot |
