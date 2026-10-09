# 📑 Dokumen Perancangan Sistem Informasi Management SDM (HRMS Enterprise)

---

## 1. Pendahuluan
**HRMS Enterprise** adalah sistem informasi manajemen sumber daya manusia berbasis web yang dirancang untuk merampingkan operasional HR, mencakup pengelolaan data pegawai, presensi *real-time*, dan manajemen pengajuan cuti. Sistem ini mendukung arsitektur *multi-user* dengan pembagian hak akses (role) antara **Admin HR** dan **Karyawan**.

---

## 2. Spesifikasi Teknologil & Arsitektur (Tech Stack)

| Komponen | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Language & Engine** | PHP Native (v8.x+) | Logika backend bisnis dan pengolahan data server-side |
| **Database Engine** | MySQL / MariaDB via PDO | Penyimpanan data relasional dengan *prepared statements* |
| **Database Tool** | phpMyAdmin | Manajemen GUI skema database `hrms_db` |
| **Frontend Styling** | Tailwind CSS (CDN) | Framework utility-first untuk desain antarmuka *Obsidian Dark Mode* |
| **Icons & Typography** | Google Material Symbols & Plus Jakarta Sans | Komponen estetika UI modern |
| **Web Server** | Apache (XAMPP / cPanel Hosting) | Web server HTTP lokal maupun hosting |

---

## 3. Struktur Folder & Berkas Proyek

```text
hrms-app/
├── config/
│   └── database.php           # Koneksi PDO ke MySQL (hrms_db)
├── includes/
│   ├── auth_check.php         # Middleware validasi sesi & pembatasan hak akses (role)
│   ├── header.php             # Komponen Sidebar navigasi & Top Header kustom
│   └── footer.php             # Komponen Footer & Skrip JS global
├── uploads/                   # Repositori penyimpanan berkas/foto pengguna
├── index.php                  # Halaman Login Multi-Role & Autentikasi
├── logout.php                 # Hancurkan Sesi (Session Destroy) & Redirect
├── admin-dashboard.php        # Panel Statistik Eksekutif HR (Admin)
├── employee-dashboard.php     # ESS (Employee Self-Service) Portal Karyawan
├── data-pegawai.php           # Manajemen Directory & Tabel Pegawai (Admin)
├── form-pegawai.php           # Formulir Tambah & Edit Pegawai (Admin)
├── kehadiran.php              # Rekapitulasi & Modul Check-In / Check-Out
├── pengajuan-cuti.php         # Permohonan Cuti (Karyawan) & Approvals (Admin)
└── laporan.php                # Rekap Audit SDM & Fungsi Cetak Laporan
```

---

## 4. Perancangan Basis Data (`hrms_db`)

### 4.1. Diagram Antar Tabel (Entity Relationship Diagram - ERD)
* **`users` (1) <---> (N) `kehadiran`**: Satu pengguna memiliki banyak catatan kehadiran.
* **`users` (1) <---> (N) `cuti`**: Satu pengguna dapat mengajukan banyak permohonan cuti.

### 4.2. Skema Struktur Tabel (Data Dictionary)

#### A. Tabel `users`
Mengakomodasi data identitas utama akun pengguna (Admin & Karyawan).
* **Primary Key**: `id`
* **Unique Constraints**: `nip`, `email`

| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT | AUTO_INCREMENT, PRIMARY KEY | ID unik pengguna |
| `nip` | VARCHAR(30) | UNIQUE, NOT NULL | Nomor Induk Pegawai |
| `nama_lengkap` | VARCHAR(100) | NOT NULL | Nama lengkap pegawai |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL | Alamat email resmi |
| `password` | VARCHAR(255) | NOT NULL | Password terenkripsi (BCrypt/Hash) |
| `role` | ENUM('admin','karyawan')| DEFAULT 'karyawan' | Peran & hak akses |
| `jabatan` | VARCHAR(50) | NULL | Posisi/Jabatan struktural |
| `departemen` | VARCHAR(50) | NULL | Divisi/Unit kerja |
| `no_telepon` | VARCHAR(20) | NULL | Nomor kontak |
| `alamat` | TEXT | NULL | Alamat domisili |
| `foto` | VARCHAR(255) | DEFAULT 'default.png' | Nama file foto profil |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu pendaftaran akun |

#### B. Tabel `kehadiran`
Menyimpan log presensi masuk dan keluar pegawai per tanggal.
* **Primary Key**: `id`
* **Foreign Key**: `user_id` $\rightarrow$ `users(id)` ON DELETE CASCADE

| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT | AUTO_INCREMENT, PRIMARY KEY | ID unik catatan presensi |
| `user_id` | INT | FOREIGN KEY, NOT NULL | ID pengaju presensi |
| `tanggal` | DATE | NOT NULL | Tanggal presensi (YYYY-MM-DD) |
| `jam_masuk` | TIME | NULL | Waktu Absen Masuk |
| `jam_keluar` | TIME | NULL | Waktu Absen Keluar |
| `status` | ENUM('Hadir','Izin','Sakit','Alpha') | DEFAULT 'Hadir' | Indikator kehadiran |
| `keterangan` | TEXT | NULL | Catatan tambahan |

#### C. Tabel `cuti`
Menampung permohonan dan riwayat persetujuan cuti pegawai.
* **Primary Key**: `id`
* **Foreign Key**: `user_id` $\rightarrow$ `users(id)` ON DELETE CASCADE

| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT | AUTO_INCREMENT, PRIMARY KEY | ID unik permohonan cuti |
| `user_id` | INT | FOREIGN KEY, NOT NULL | ID pemohon cuti |
| `jenis_cuti` | VARCHAR(50) | NOT NULL | Kategori (Sakit, Tahunan, dll) |
| `tanggal_mulai` | DATE | NOT NULL | Awal periode cuti |
| `tanggal_selesai` | DATE | NOT NULL | Akhir periode cuti |
| `alasan` | TEXT | NOT NULL | Alasan pengajuan |
| `status` | ENUM('Pending','Disetujui','Ditolak')| DEFAULT 'Pending' | Status keputusan Admin |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu pengajuan dibuat |

---

## 5. DDL Script (SQL Data Definition Language)

```sql
CREATE DATABASE IF NOT EXISTS `hrms_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hrms_db`;

-- 1. Skema Tabel Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nip` VARCHAR(30) UNIQUE NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'karyawan') NOT NULL DEFAULT 'karyawan',
  `jabatan` VARCHAR(50) DEFAULT NULL,
  `departemen` VARCHAR(50) DEFAULT NULL,
  `no_telepon` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT 'default.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Skema Tabel Kehadiran
CREATE TABLE IF NOT EXISTS `kehadiran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `jam_masuk` TIME DEFAULT NULL,
  `jam_keluar` TIME DEFAULT NULL,
  `status` ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') NOT NULL DEFAULT 'Hadir',
  `keterangan` TEXT DEFAULT NULL,
  CONSTRAINT `fk_kehadiran_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Skema Tabel Cuti
CREATE TABLE IF NOT EXISTS `cuti` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `jenis_cuti` VARCHAR(50) NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `alasan` TEXT NOT NULL,
  `status` ENUM('Pending', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cuti_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion Initial Seed Data (Data Awal Uji Coba)
-- Password Default Seed Account: 123456
INSERT INTO `users` (`nip`, `nama_lengkap`, `email`, `password`, `role`, `jabatan`, `departemen`) VALUES
('ADM001', 'Admin HR', 'admin.hr@perusahaan.co.id', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe115f/O65oW/M.a8Gq9rJp/N9E.Wf/Si', 'admin', 'HR Manager', 'Human Resource'),
('EMP001', 'Dimas Wicaksono', 'karyawan.staff@perusahaan.co.id', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe115f/O65oW/M.a8Gq9rJp/N9E.Wf/Si', 'karyawan', 'Software Engineer', 'IT & Development');
```

---

## 6. Alur Keamanan & Hak Akses (Authentication & Authorization Flow)

```text
[Pengguna Membuka Halaman Web]
              │
              ▼
   [Apakah Memiliki Sesi Session ID?]
        │                       │
       TIDAK                   YA
        │                       │
        ▼                       ▼
 [Redirect: index.php]   [Periksa Role di auth_check.php]
                                │
               ┌────────────────┴────────────────┐
               ▼                                 ▼
      [Role: Admin HR]                  [Role: Karyawan]
               │                                 │
   - admin-dashboard.php             - employee-dashboard.php
   - Kelola Data Pegawai             - Portal ESS (Self-Service)
   - Approval Cuti                   - Absen Masuk / Keluar
   - Laporan Audit SDM               - Pengajuan Cuti Mandiri
```

---

## 7. Panduan Presentasi & Hosting Produk

1. **Persiapan Database**: Eksport file `.sql` dari phpMyAdmin lokal dan import ke phpMyAdmin server cPanel/Hosting.
2. **Konfigurasi Server Production**: Sesuaikan kredensial server pada file `config/database.php` (`host`, `user`, `password`, `database_name`).
3. **Pemberihan URL (`.htaccess`)**: Untuk kebutuhan estetika presentasi, tambahkan berkas `.htaccess` di *root directory* agar ekstensi `.php` disembunyikan dari URL.
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^\.]+)$ $1.php [NC,L]
```