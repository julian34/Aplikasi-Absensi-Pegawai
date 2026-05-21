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
- Melihat daftar pegawai.
- Melihat atau mengelola riwayat absensi.
- Pengelolaan data pegawai.
- Penyimpanan data kehadiran pegawai pada database.

Sistem ini difokuskan pada kebutuhan dasar absensi pegawai dan dapat dikembangkan lebih lanjut dengan fitur tambahan seperti laporan PDF, rekap bulanan, validasi lokasi, QR Code, atau integrasi perangkat biometrik.

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

---

## Struktur Repository

Struktur utama repository:

```bash
Absesi-Pegawai-RPL/
├── Doc/
│   ├── ClassDiagram.drawio.png
│   └── Usecase.drawio.png
│
├── backend/
│   └── ...
│
├── frontend/
│   ├── docker/
│   ├── src/
│   │   └── main.js
│   ├── App.vue
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
│
├── docker-compose.yml
├── README.md
├── rec.md
└── .gitignore
```

---

## Struktur Folder Frontend yang Disarankan

## Struktur Folder Frontend yang Disarankan

Agar proyek Vue.js lebih rapi dan mudah dikembangkan, struktur frontend dapat disusun sebagai berikut:

```bash
frontend/
└── src/
    ├── assets/
    │   ├── css/
    │   ├── images/
    │   └── icons/
    │
    ├── components/
    │   ├── base/
    │   │   ├── BaseButton.vue
    │   │   ├── BaseInput.vue
    │   │   └── BaseModal.vue
    │   │
    │   ├── layout/
    │   │   ├── Sidebar.vue
    │   │   ├── Navbar.vue
    │   │   └── Footer.vue
    │   │
    │   └── attendance/
    │       ├── AttendanceCard.vue
    │       ├── AttendanceTable.vue
    │       └── AttendanceForm.vue
    │
    ├── views/
    │   ├── auth/
    │   │   └── LoginView.vue
    │   │
    │   ├── dashboard/
    │   │   └── DashboardView.vue
    │   │
    │   ├── employee/
    │   │   ├── EmployeeListView.vue
    │   │   └── EmployeeDetailView.vue
    │   │
    │   └── attendance/
    │       ├── AttendanceView.vue
    │       └── AttendanceHistoryView.vue
    │
    ├── router/
    │   └── index.js
    │
    ├── services/
    │   ├── api.js
    │   ├── authService.js
    │   ├── employeeService.js
    │   └── attendanceService.js
    │
    ├── stores/
    │   ├── authStore.js
    │   ├── employeeStore.js
    │   └── attendanceStore.js
    │
    ├── utils/
    │   ├── formatDate.js
    │   └── validation.js
    │
    ├── App.vue
    └── main.js
```

---

## Penjelasan Struktur Folder Frontend

### `assets/`

Folder untuk menyimpan file pendukung tampilan, seperti CSS, gambar, ikon, dan aset visual lain.

### `components/`

Folder untuk menyimpan komponen Vue yang dapat digunakan ulang. Contohnya tombol, input, modal, tabel absensi, dan komponen layout.

### `views/`

Folder untuk menyimpan halaman utama aplikasi yang terhubung langsung dengan route, seperti halaman login, dashboard, data pegawai, dan riwayat absensi.

### `router/`

Folder untuk konfigurasi navigasi menggunakan Vue Router.

### `services/`

Folder untuk komunikasi frontend dengan backend API. File pada folder ini berisi fungsi untuk login, mengambil data pegawai, menyimpan absensi, dan mengambil riwayat absensi.

### `stores/`

Folder untuk state management, misalnya status login, data user aktif, data pegawai, dan data absensi.

### `utils/`

Folder untuk fungsi bantuan umum, seperti format tanggal, validasi form, dan pengolahan data sederhana.

---

## Aktor Sistem

Berdasarkan use case diagram, aktor utama dalam sistem adalah:

| Aktor         | Deskripsi                                                                        |
| ------------- | -------------------------------------------------------------------------------- |
| Pegawai       | Pengguna yang melakukan login, logout, absensi datang, dan absensi pulang.       |
| Admin Absensi | Pengguna yang dapat melihat daftar pegawai dan memantau riwayat absensi.         |
| Super Admin   | Pengguna dengan hak akses lebih tinggi untuk mengelola data sistem dan pengguna. |

---

![Use Case Diagram](Doc/Usecase.drawio.png)
![Class Diagram](Doc/ClassDiagram.drawio.png)
![ERD](Doc/ERDiagram.drawio.png)

## Kebutuhan Fungsional

| Kode  | Kebutuhan Fungsional                                                       |
| ----- | -------------------------------------------------------------------------- |
| RF-01 | Sistem dapat melakukan login pengguna.                                     |
| RF-02 | Sistem dapat melakukan logout pengguna.                                    |
| RF-03 | Sistem dapat membedakan hak akses Pegawai, Admin Absensi, dan Super Admin. |
| RF-04 | Sistem dapat menampilkan dashboard sesuai role pengguna.                   |
| RF-05 | Sistem dapat menyimpan data absensi datang.                                |
| RF-06 | Sistem dapat menyimpan data absensi pulang.                                |
| RF-07 | Sistem dapat menampilkan daftar pegawai.                                   |
| RF-08 | Sistem dapat menampilkan riwayat absensi pegawai.                          |
| RF-09 | Sistem dapat menyimpan data pegawai.                                       |
| RF-10 | Sistem dapat mengubah data pegawai.                                        |
| RF-11 | Sistem dapat menghapus data pegawai jika diperlukan.                       |
| RF-12 | Sistem dapat memvalidasi input form sebelum data disimpan.                 |

---

## Kebutuhan Non-Fungsional

| Kode   | Kebutuhan Non-Fungsional                                           |
| ------ | ------------------------------------------------------------------ |
| RNF-01 | Sistem memiliki antarmuka yang mudah digunakan.                    |
| RNF-02 | Sistem dapat diakses melalui browser modern.                       |
| RNF-03 | Sistem menggunakan autentikasi untuk membatasi akses pengguna.     |
| RNF-04 | Sistem memiliki struktur kode yang modular.                        |
| RNF-05 | Sistem menggunakan REST API untuk komunikasi frontend dan backend. |
| RNF-06 | Sistem menyimpan data pada database relasional.                    |
| RNF-07 | Sistem dapat dijalankan secara lokal menggunakan Docker.           |
| RNF-08 | Sistem dapat dikembangkan untuk fitur laporan dan rekap absensi.   |

---

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

### Alur Melihat Daftar Pegawai

1. Admin Absensi atau Super Admin login ke sistem.
2. Admin membuka halaman daftar pegawai.
3. Sistem mengambil data pegawai dari backend.
4. Data pegawai ditampilkan pada tabel.

### Alur Melihat Riwayat Absensi

1. Admin membuka halaman riwayat absensi.
2. Sistem mengambil data riwayat absensi dari backend.
3. Admin dapat melihat data berdasarkan pegawai atau tanggal.
4. Sistem menampilkan riwayat absensi dalam bentuk tabel.

---

---

## Instalasi Menggunakan Docker

Pastikan Docker dan Docker Compose sudah terpasang.

Jalankan perintah berikut dari root project:

```bash
docker compose up -d --build
```

Service utama yang digunakan:

| Service    | Fungsi              | Port |
| ---------- | ------------------- | ---- |
| backend    | Laravel backend API | 9000 |
| web        | Vue.js frontend     | 5173 |
| db         | MySQL database      | 3306 |
| phpmyadmin | Database management | 8082 |

---

## Standar Penulisan Kode

Standar penulisan kode yang digunakan:

1. Nama file Vue menggunakan format `PascalCase`.
2. File service menggunakan format `camelCase`.
3. Komponen reusable diletakkan pada folder `components`.
4. Halaman utama diletakkan pada folder `views`.
5. Koneksi API diletakkan pada folder `services`.
6. Validasi form dilakukan sebelum request dikirim ke backend.
7. Proses bisnis utama tetap berada di backend.
8. Struktur kode dibuat modular agar mudah diuji dan dikembangkan.

---

## Manajemen Branch Git

Branch yang disarankan:

```bash
main        # branch utama atau rilis stabil
dev         # branch pengembangan
feature/*   # branch fitur baru
fix/*       # branch perbaikan bug
docs/*      # branch dokumentasi
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
