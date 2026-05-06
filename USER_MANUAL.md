# Panduan Penggunaan Aplikasi Bionic Backend

## Daftar Isi | Table of Contents

1. [Tujuan Dokumen | Document Purpose](#1-tujuan-dokumen--document-purpose)
2. [Ruang Lingkup | Scope](#2-ruang-lingkup--scope)
3. [Role dan Hak Akses | Roles and Access Rights](#3-role-dan-hak-akses--roles-and-access-rights)
4. [Alur Umum Penggunaan | General Usage Flow](#4-alur-umum-penggunaan--general-usage-flow)
   - [4.1 Sebelum Memulai | Before You Start](#41-sebelum-memulai--before-you-start)
   - [4.2 Login](#42-login)
   - [4.3 Logout](#43-logout)
   - [4.4 Arti Status Task | Task Status Meaning](#44-arti-status-task--task-status-meaning)
5. [Panduan Admin | Admin Guide](#5-panduan-admin--admin-guide)
   - [5.1 Dashboard Admin](#51-dashboard-admin--admin-dashboard)
   - [5.2 Manajemen User | User Management](#52-manajemen-user--user-management)
   - [5.3 Manajemen Master Task | Task Master Management](#53-manajemen-master-task--task-master-management)
   - [5.4 Checklist Akhir Admin](#54-checklist-akhir-admin--admin-end-of-session-checklist)
6. [Panduan Operator | Operator Guide](#6-panduan-operator--operator-guide)
   - [6.1 Dashboard Operator](#61-dashboard-operator--operator-dashboard)
   - [6.2 Submit Task](#62-submit-task--submit-a-task)
   - [6.3 Menangani Revisi | Handling Revisions](#63-menangani-revisi--handling-revisions)
   - [6.4 Batalkan Submission | Cancel Submission](#64-batalkan-submission--cancel-submission)
7. [Panduan Verifikator | Verifikator Guide](#7-panduan-verifikator--verifikator-guide)
   - [7.1 Review Data Submission](#71-review-data-submission--submission-review)
   - [7.2 Verifikasi Per Task | Per-Task Verification](#72-verifikasi-per-task--per-task-verification)
   - [7.3 Verifikasi Massal | Bulk Verification](#73-verifikasi-massal--bulk-verification)
   - [7.4 Rekapitulasi dan Export | Recap and Export](#74-rekapitulasi-dan-export--recap-and-export)
8. [Troubleshooting](#8-troubleshooting)
9. [FAQ](#9-faq)
10. [Catatan Fitur WIP | WIP Feature Notes](#10-catatan-fitur-wip--wip-feature-notes)
11. [Kontrol Revisi Dokumen | Document Revision Control](#11-kontrol-revisi-dokumen--document-revision-control)

---

## 1. Tujuan Dokumen | Document Purpose

### Indonesia

Dokumen ini adalah panduan penggunaan aplikasi untuk tiga peran utama: Admin, Operator, dan Verifikator. Fokus panduan adalah alur operasional harian, bukan instalasi teknis.

### English

This document is a user guide for three main roles: Admin, Operator, and Verifikator. It focuses on daily operational workflows, not technical setup.

---

## 2. Ruang Lingkup | Scope

### Indonesia

Panduan ini mencakup:

1. Login dan logout.
2. Alur penggunaan fitur utama per role.
3. Arti status task.
4. Troubleshooting umum untuk pengguna.

Panduan ini **tidak** mencakup:

1. Instalasi server, database, atau deployment.
2. Konfigurasi environment.
3. Referensi API untuk developer.

### English

This guide covers:

1. Login and logout.
2. Main feature workflows by role.
3. Task status definitions.
4. Common end-user troubleshooting.

This guide does **not** cover:

1. Server, database, or deployment setup.
2. Environment configuration.
3. Developer API reference.

---

## 3. Role dan Hak Akses | Roles and Access Rights

### Indonesia

| Role | Tanggung Jawab |
|------|---------------|
| **Admin** | Mengelola user, lokasi, item, action checklist, serta memantau ringkasan dashboard. |
| **Operator** | Mengerjakan task kebersihan, melakukan scan item, submit hasil pekerjaan, dan menindaklanjuti revisi. |
| **Verifikator** | Meninjau submission operator, melakukan verifikasi atau revisi, serta melihat ringkasan laporan. |

### English

| Role | Responsibilities |
|------|-----------------|
| **Admin** | Manages users, locations, items, checklist actions, and monitors dashboard summaries. |
| **Operator** | Executes cleaning tasks, scans items, submits work results, and handles revision requests. |
| **Verifikator** | Reviews operator submissions, verifies or requests revisions, and checks report summaries. |

---

## 4. Alur Umum Penggunaan | General Usage Flow

### 4.1 Sebelum Memulai | Before You Start

### Indonesia

1. Pastikan Anda memiliki akun aktif.
2. Pastikan role akun Anda sesuai tugas kerja.
3. Gunakan browser yang stabil dan koneksi internet memadai.

### English

1. Ensure you have an active account.
2. Ensure your account role matches your job function.
3. Use a stable browser and internet connection.

---

### 4.2 Login

### Indonesia

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

### English

**Steps:**

1. Open the application login page.
2. Enter your username and password.
3. Click **Login**.
4. The system redirects you to the dashboard based on your role.

**Expected result:**

| Role | Redirected To |
|------|--------------|
| Admin | Admin page |
| Operator | Operator page |
| Verifikator | Verifikator page |

---

### 4.3 Logout

### Indonesia

**Langkah:**

1. Klik menu **Logout**.
2. Sistem mengakhiri sesi login.
3. Anda kembali ke halaman login.

### English

**Steps:**

1. Click **Logout**.
2. The system ends your session.
3. You are redirected back to the login page.

---

### 4.4 Arti Status Task | Task Status Meaning

### Indonesia

| Status | Arti |
|--------|------|
| `pending` | Task sudah disubmit operator dan menunggu verifikasi. |
| `verified` | Task sudah disetujui verifikator. |
| `revisi` | Task dikembalikan verifikator untuk diperbaiki operator. |
| `revised` | Task sudah diperbaiki operator dan menunggu verifikasi ulang. |

### English

| Status | Meaning |
|--------|---------|
| `pending` | The task has been submitted by the operator and is waiting for verification. |
| `verified` | The task has been approved by the verifikator. |
| `revisi` | The task was returned by the verifikator for operator correction. |
| `revised` | The task has been corrected by the operator and is waiting for re-verification. |

---

## 5. Panduan Admin | Admin Guide

### 5.1 Dashboard Admin | Admin Dashboard

### Indonesia

**Tujuan:** Melihat ringkasan data operasional.

**Langkah:**

1. Login sebagai Admin.
2. Buka halaman Dashboard Admin.
3. Periksa statistik utama seperti jumlah user, jumlah lokasi, dan ringkasan aktivitas.

> **Catatan:** Gunakan dashboard sebagai indikator cepat. Validasi detail tetap dilakukan dari halaman manajemen data.

### English

**Purpose:** View operational summary data.

**Steps:**

1. Log in as Admin.
2. Open the Admin Dashboard.
3. Review key statistics such as user totals, location count, and activity summaries.

> **Note:** Use the dashboard as a quick indicator. Validate details from data management pages.

---

### 5.2 Manajemen User | User Management

### Indonesia

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

### English

**Purpose:** Create, update, and delete user accounts.

**Add User:**

1. Open the **User Management** menu.
2. Click **Add User**.
3. Fill required fields: name, username, password, and role.
4. Click **Save**.

**Edit User:**

1. Find the user in the table.
2. Click **Edit** on the target row.
3. Update required fields.
4. Click **Save**.

**Delete User:**

1. Find the user in the table.
2. Click **Delete** on the target row.
3. Confirm deletion in the dialog.

**Expected result:** Changes appear in the user table immediately.

---

### 5.3 Manajemen Master Task | Task Master Management

### Indonesia

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

### English

**Data management order:** Location → Item → Action

> **Important principles:**
> - An Item must belong to a Location.
> - An Action must belong to an Item.
> - Deleting or modifying parent data can affect child records.

**General steps:**

1. Open the **Task Management** menu.
2. Add or select a **Location**.
3. Add **Items** under that Location.
4. Add **Actions** under that Item.
5. Edit or delete as needed while respecting data relationships.

---

### 5.4 Checklist Akhir Admin | Admin End-of-Session Checklist

### Indonesia

Sebelum mengakhiri sesi, pastikan:

- [ ] Data user terbaru sudah tersimpan.
- [ ] Struktur Location → Item → Action sudah konsisten.
- [ ] Tabel menampilkan data sesuai perubahan yang dilakukan.

### English

Before ending your session, ensure:

- [ ] Latest user data is saved.
- [ ] Location → Item → Action structure is consistent.
- [ ] Tables reflect all changes made.

---

## 6. Panduan Operator | Operator Guide

### 6.1 Dashboard Operator | Operator Dashboard

### Indonesia

**Tujuan:** Melihat daftar lokasi kerja dan status submission hari ini.

**Langkah:**

1. Login sebagai Operator.
2. Buka dashboard operator.
3. Tinjau daftar lokasi yang ditampilkan beserta status masing-masing.
4. Pilih lokasi kerja untuk memulai task.

### English

**Purpose:** View assigned work locations and today's submission status.

**Steps:**

1. Log in as Operator.
2. Open the operator dashboard.
3. Review the displayed location list along with their current status.
4. Select a work location to start tasks.

---

### 6.2 Submit Task | Submit a Task

### Indonesia

**Langkah:**

1. Dari dashboard, pilih lokasi yang akan dikerjakan.
2. Masuk ke halaman scan lokasi tersebut.
3. Pilih atau scan item yang akan dikerjakan.
4. Buka form checklist action.
5. Tandai action yang sudah dilakukan.
6. Klik **Kirim** untuk submit.

**Hasil yang diharapkan:** Status task menjadi `pending`, menunggu verifikasi.

### English

**Steps:**

1. From the dashboard, select the target location.
2. Open the scan page for that location.
3. Select or scan the item to process.
4. Open the action checklist form.
5. Mark all completed actions.
6. Click **Submit**.

**Expected result:** Task status becomes `pending`, waiting for verification.

---

### 6.3 Menangani Revisi | Handling Revisions

### Indonesia

**Langkah:**

1. Buka halaman **Revisi**.
2. Pilih task dengan status `revisi`.
3. Baca catatan perbaikan dari verifikator dengan saksama.
4. Lakukan perbaikan sesuai catatan.
5. Jika tersedia, unggah bukti revisi (foto).
6. Klik **Kirim Ulang**.

**Hasil yang diharapkan:** Status berubah menjadi `revised` dan task masuk antrean verifikasi ulang.

### English

**Steps:**

1. Open the **Revision** page.
2. Select a task with `revisi` status.
3. Read the correction notes from the verifikator carefully.
4. Perform the required corrections.
5. Upload revision evidence (photo) if available.
6. Click **Resubmit**.

**Expected result:** Status becomes `revised` and the task enters the re-verification queue.

---

### 6.4 Batalkan Submission | Cancel Submission

### Indonesia

Gunakan fitur ini jika submission salah kirim atau perlu ditarik sebelum diproses lebih lanjut.

**Langkah:**

1. Temukan submission yang ingin dibatalkan.
2. Klik **Batalkan**.
3. Konfirmasi pembatalan.

> **Catatan:** Setelah dibatalkan, task perlu disubmit ulang jika masih ingin diproses.

### English

Use this feature when a submission was sent incorrectly or must be withdrawn before further processing.

**Steps:**

1. Find the submission to cancel.
2. Click **Cancel**.
3. Confirm cancellation.

> **Note:** After cancellation, the task must be resubmitted if it still needs processing.

---

## 7. Panduan Verifikator | Verifikator Guide

### 7.1 Review Data Submission | Submission Review

### Indonesia

**Langkah:**

1. Login sebagai Verifikator.
2. Buka halaman verifikasi.
3. Gunakan filter **lokasi** dan **tanggal** untuk menyempurnakan tampilan data.
4. Cari task yang ingin ditinjau.
5. Klik baris task untuk membuka detail.

### English

**Steps:**

1. Log in as Verifikator.
2. Open the verification page.
3. Apply **location** and **date** filters to narrow down the data.
4. Search for target tasks.
5. Click a task row to open its details.

---

### 7.2 Verifikasi Per Task | Per-Task Verification

### Indonesia

**Approve task:**

1. Buka detail task.
2. Periksa data checklist dan informasi pendukung.
3. Klik **Approve** atau **Verify**.

**Kembalikan untuk revisi:**

1. Buka detail task.
2. Klik **Revisi**.
3. Isi catatan revisi yang jelas dan operasional agar operator dapat langsung bertindak.
4. Klik **Simpan**.

### English

**Approve a task:**

1. Open task details.
2. Review checklist data and supporting information.
3. Click **Approve** or **Verify**.

**Request revision:**

1. Open task details.
2. Click **Revision**.
3. Add clear and actionable revision notes so the operator can act immediately.
4. Click **Save**.

---

### 7.3 Verifikasi Massal | Bulk Verification

### Indonesia

**Prasyarat:**

1. Filter lokasi dan tanggal sudah dikonfigurasi dengan benar.
2. Task pada filter sudah ditinjau secara sampling atau sesuai SOP internal tim.

**Langkah:**

1. Terapkan filter lokasi dan tanggal.
2. Klik **Verify All**.
3. Pastikan status semua task berubah sesuai aksi.

> **Catatan:** Gunakan fitur ini dengan hati-hati. Aksi Verify All tidak dapat dibatalkan massal.

### English

**Prerequisites:**

1. Location and date filters are correctly set.
2. Tasks in the current filter have been sampled or reviewed based on internal SOP.

**Steps:**

1. Apply location and date filters.
2. Click **Verify All**.
3. Confirm that all task statuses are updated correctly.

> **Note:** Use this feature carefully. Bulk Verify All cannot be undone in bulk.

---

### 7.4 Rekapitulasi dan Export | Recap and Export

### Indonesia

**Langkah:**

1. Buka menu **Laporan Rekapitulasi**.
2. Pilih rentang data yang dibutuhkan (lokasi, tanggal).
3. Tinjau ringkasan yang ditampilkan.
4. Klik **Export** jika laporan perlu disimpan atau dibagikan.

### English

**Steps:**

1. Open the **Recap Report** menu.
2. Select the required data range (location, date).
3. Review the displayed summary.
4. Click **Export** if the report needs to be saved or shared.

---

## 8. Troubleshooting

### 8.1 Sesi Login Berakhir | Session Expired

### Indonesia

**Gejala:** Anda tiba-tiba diarahkan kembali ke halaman login.

**Tindakan:**

1. Login ulang menggunakan username dan password.
2. Jika masalah berulang dalam waktu singkat, hubungi Admin untuk pengecekan akun.

### English

**Symptom:** You are suddenly redirected to the login page.

**Actions:**

1. Log in again using your credentials.
2. If the issue repeats quickly, contact Admin for account verification.

---

### 8.2 Data Tidak Muncul di Tabel | Data Not Appearing in Table

### Indonesia

**Tindakan:**

1. Cek filter tanggal dan lokasi — pastikan rentangnya sesuai.
2. Hapus semua filter lalu cari ulang.
3. Pastikan data sudah benar-benar disubmit sebelumnya.

### English

**Actions:**

1. Check date and location filters — ensure the range is correct.
2. Clear all filters and search again.
3. Confirm that the data was actually submitted beforehand.

---

### 8.3 Status Tidak Berubah Setelah Aksi | Status Not Updated After Action

### Indonesia

**Tindakan:**

1. Refresh halaman dan periksa kembali status.
2. Pastikan aksi terakhir sudah dikonfirmasi (tidak tertutup sebelum tersimpan).
3. Ulangi proses aksi jika diperlukan.

### English

**Actions:**

1. Refresh the page and recheck the status.
2. Ensure your last action was confirmed (dialog not closed before saving).
3. Repeat the action process if needed.

---

## 9. FAQ

### Indonesia

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

### English

**Q: Can an Operator verify their own tasks?**
A: No. Verification is performed only by the Verifikator.

**Q: Can Admin change the role of an existing user?**
A: Yes, via the Edit feature in the User Management menu.

**Q: When is it safe to use Verify All?**
A: When filters are correctly set and internal review (sampling) requirements have been met.

**Q: What happens if an operator cancels a pending submission?**
A: The submission is removed and must be resubmitted if still needed.

**Q: Can the verifikator view revision history?**
A: Yes, task details show revision notes and previous status history.

---

## 10. Catatan Fitur WIP | WIP Feature Notes

### Indonesia

Fitur-fitur berikut masih dalam pengembangan dan mungkin belum berfungsi sepenuhnya:

1. **Manajemen Shift** — Penugasan shift operator dan rotasi shift belum tersedia secara penuh.
2. **Alur verifikasi lanjutan** — Beberapa alur verifikasi tambahan masih dalam proses pengembangan.
3. **Pelacakan kode unik (QR)** — Fitur pelacakan berbasis kode unik tersedia namun perilakunya dapat berbeda tergantung konfigurasi sistem.

### English

The following features are still under development and may not be fully functional:

1. **Shift Management** — Operator shift assignment and rotation are not yet fully available.
2. **Advanced verification flows** — Some additional verification workflows are still being developed.
3. **Unique code (QR) tracking** — Unique code-based tracking is available but may behave differently depending on system configuration.

---

## 11. Kontrol Revisi Dokumen | Document Revision Control

| Field | Value |
|-------|-------|
| Versi / Version | Draft 1 |
| Tanggal / Date | 27 April 2026 |
| Status | Siap review internal / Ready for internal review |
| Dibuat oleh / Created by | GitHub Copilot |
