# Sistem Absensi Pegawai - RPL

## Deskripsi Proyek

**Sistem Absensi Pegawai** adalah aplikasi web yang dikembangkan untuk mendukung proses pencatatan kehadiran pegawai secara digital. Sistem ini dirancang sebagai proyek **Rekayasa Perangkat Lunak (RPL)** dengan menerapkan tahapan analisis kebutuhan, perancangan sistem, implementasi, pengujian, dan dokumentasi.

Aplikasi ini memiliki fitur utama seperti autentikasi pengguna, absensi datang, absensi pulang, melihat daftar pegawai, serta pengelolaan riwayat absensi. Sistem dikembangkan menggunakan arsitektur **frontend dan backend terpisah**, dengan Vue.js sebagai frontend dan Laravel sebagai backend API.

---

## Tujuan Pengembangan

Tujuan pengembangan sistem ini adalah:

1. Membangun aplikasi absensi pegawai berbasis web.
2. Menerapkan konsep Rekayasa Perangkat Lunak dalam proses pengembangan sistem.
3. Menyediakan fitur pencatatan absensi datang dan absensi pulang.
4. Menyediakan fitur pengelolaan data pegawai.
5. Menyediakan fitur riwayat absensi untuk kebutuhan monitoring.
6. Menghasilkan dokumentasi perangkat lunak yang sistematis dan mudah dipahami.

---

## Ruang Lingkup Sistem

Ruang lingkup sistem mencakup:

- Login pengguna.
- Logout pengguna.
- Absensi datang.
- Absensi pulang.
- Melihat riwayat absensi.
- Pengelolaan data pegawai.

Sistem ini difokuskan pada kebutuhan dasar absensi pegawai dan dapat dikembangkan lebih lanjut dengan fitur tambahan.

---

## Teknologi yang Digunakan

### Frontend

- Vue.js 3
- Vite
- JavaScript
- Axios
- HTML
- CSS

### Backend

- PHP 8.2
- Laravel
- REST API

### Database

- MySQL 8.0

### Tools Pendukung

- Docker
- Docker Compose
- phpMyAdmin
- Git
- GitHub
- Draw.io untuk dokumentasi diagram

## Aktor Sistem

Berdasarkan use case diagram, aktor utama dalam sistem adalah:

| Aktor   | Deskripsi                                                                  |
| ------- | -------------------------------------------------------------------------- |
| Pegawai | Pengguna yang melakukan login, logout, absensi datang, dan absensi pulang. |

---

![Use Case Diagram](Doc/Usecase.drawio.png)
![Class Diagram](Doc/ClassDiagram.drawio.png)
![ERD](Doc/ERDiagram.drawio.png)

## Alur Kerja Sistem

### Alur Login

1. Pengguna membuka halaman login.
2. Pengguna memasukkan email dan password.
3. Sistem mengirim data login ke backend.
4. Backend memvalidasi kredensial.
5. Jika valid, sistem memberikan akses ke dashboard.
6. Jika tidak valid, sistem menampilkan pesan kesalahan.

### Alur Absensi Datang

1. Pegawai login ke sistem.
2. Pegawai membuka halaman absensi.
3. Pegawai menekan tombol absensi datang.
4. Sistem mencatat tanggal dan waktu kedatangan.
5. Data absensi disimpan ke database.
6. Sistem menampilkan status berhasil.

### Alur Absensi Pulang

1. Pegawai login ke sistem.
2. Pegawai membuka halaman absensi.
3. Pegawai menekan tombol absensi pulang.
4. Sistem mencatat waktu pulang.
5. Sistem memperbarui data absensi pada tanggal yang sama.
6. Sistem menampilkan status berhasil.

### Alur Melihat Riwayat Absensi

1. Pegawa membuka halaman riwayat absensi.
2. Sistem mengambil data riwayat absensi dari backend.
3. Sistem menampilkan riwayat absensi dalam bentuk tabel.

---

### Pengujian Sistem

![CFG](Doc/cfg.drawio.png)

## Pengujian Unit Absensi

| Kode Test | Skenario Pengujian                                                 | Expected Result                            | Hasil |
| --------- | ------------------------------------------------------------------ | ------------------------------------------ | ----- |
| UT-01     | Status masuk tepat waktu jika absen sebelum atau sama dengan 07.45 | Sistem menetapkan status `tepat_waktu`     | Lulus |
| UT-02     | Status masuk terlambat jika absen setelah 07.45                    | Sistem menetapkan status `terlambat`       | Lulus |
| UT-03     | Status pulang sesuai jam jika pulang jam 16.00 atau lebih          | Sistem menetapkan status `sesuai_jam`      | Lulus |
| UT-04     | Status pulang cepat jika pulang sebelum 16.00                      | Sistem menetapkan status `pulang_cepat`    | Lulus |
| UT-05     | Status akhir hadir jika masuk tepat waktu                          | Sistem menetapkan status akhir `hadir`     | Lulus |
| UT-06     | Status akhir terlambat jika status masuk terlambat                 | Sistem menetapkan status akhir `terlambat` | Lulus |

## Feature Test Absensi Pegawai

| Kode Test | Skenario Pengujian                                    | Expected Result                                  | Hasil |
| --------- | ----------------------------------------------------- | ------------------------------------------------ | ----- |
| FT-AB-01  | Pegawai dapat melihat absensi hari ini                | Sistem menampilkan data absensi hari ini         | Lulus |
| FT-AB-02  | Pegawai dapat melakukan absen datang                  | Sistem menyimpan jam masuk pegawai               | Lulus |
| FT-AB-03  | Pegawai tidak dapat absen datang dua kali             | Sistem menolak absensi datang ganda              | Lulus |
| FT-AB-04  | Pegawai tidak dapat absen pulang sebelum absen datang | Sistem menolak absensi pulang                    | Lulus |
| FT-AB-05  | Pegawai dapat absen pulang setelah absen datang       | Sistem menyimpan jam pulang pegawai              | Lulus |
| FT-AB-06  | Pegawai tidak dapat absen pulang dua kali             | Sistem menolak absensi pulang ganda              | Lulus |
| FT-AB-07  | Endpoint absensi ditolak jika belum login             | Sistem mengembalikan status tidak terautentikasi | Lulus |

---

## Feature Test Login Pegawai

| Kode Test | Skenario Pengujian                                 | Expected Result                                          | Hasil |
| --------- | -------------------------------------------------- | -------------------------------------------------------- | ----- |
| FT-LG-01  | Pegawai login menggunakan email dan password valid | Sistem menerima login dan menghasilkan token autentikasi | Lulus |
| FT-LG-02  | Pegawai login menggunakan NIP dan password valid   | Sistem menerima login dan menghasilkan token autentikasi | Lulus |
| FT-LG-03  | Login gagal jika password salah                    | Sistem menolak login dan menampilkan pesan kesalahan     | Lulus |
| FT-LG-04  | Login gagal jika input kosong                      | Sistem menolak login dan menampilkan pesan validasi      | Lulus |

## Instalasi Menggunakan Docker

Pastikan Docker dan Docker Compose sudah terpasang.

Jalankan perintah berikut dari root project:

```bash
docker compose up -d --build
```

Service utama yang digunakan:

| Service    | Fungsi              | Port |
| ---------- | ------------------- | ---- |
| backend    | Laravel backend API | 8000 |
| web        | Vue.js frontend     | 5173 |
| db         | MySQL database      | 3306 |
| phpmyadmin | Database management | 8082 |

---

## Manajemen Branch Git

Branch yang disarankan:

```bash
main        # branch utama atau rilis stabil
dev         # branch pengembangan
feature/*   # branch fitur baru
fix/*       # branch perbaikan bug
```

Contoh pesan commit:

```bash
feat: add employee attendance feature
feat: add employee list page
fix: resolve login validation issue
docs: add use case and class diagram documentation
refactor: improve frontend folder structure
test: add attendance feature test scenario
```

---

## Kesimpulan

Sistem Absensi Pegawai merupakan aplikasi web yang dirancang untuk membantu proses pencatatan kehadiran pegawai secara digital. Sistem ini menerapkan konsep Rekayasa Perangkat Lunak melalui analisis kebutuhan, perancangan use case, perancangan class diagram, implementasi frontend dan backend, serta pengujian sistem.

Dengan struktur frontend dan backend yang terpisah, sistem ini lebih mudah dikembangkan, diuji, dan dipelihara. Proyek ini juga dapat diperluas menjadi sistem absensi yang lebih lengkap dengan fitur laporan, validasi lokasi, QR Code, dan dashboard analitik.
