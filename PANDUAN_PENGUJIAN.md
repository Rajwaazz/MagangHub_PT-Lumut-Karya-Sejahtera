# Panduan Pengujian & Dokumentasi Coding Test
### Fitur: Alur Publikasi Artikel (Workflow Review & Approval) + Peningkatan UI/UX
**PT Lumut Karya Sejahtera**

---

## 📋 1. Ringkasan Fitur & Logika Bisnis

Sesuai instruksi soal, alur publikasi artikel menerapkan aturan berikut:
1. **Author Tidak Bisa Langsung Mempublikasikan Artikel**:
   - Author membuat artikel dengan status awal **`draft`**.
   - Author menekan tombol **"Ajukan Review"**, status artikel berubah menjadi **`pending`**.
2. **Admin Meninjau Antrean Review**:
   - Admin melihat daftar artikel berstatus `pending` di dashboard.
   - Admin dapat memilih:
     - **Setujui (Approve)** $\rightarrow$ status artikel menjadi **`published`** dan langsung tayang di portal blog publik (*Home*).
     - **Tolak (Reject)** $\rightarrow$ Admin wajib mengisi **alasan penolakan / catatan revisi**, dan status berubah menjadi **`rejected`**.
3. **Author Memperbaiki Artikel yang Ditolak**:
   - Artikel yang ditolak otomatis muncul kembali di dashboard Author dengan penanda merah dan kotak kutipan alasan penolakan dari Admin.
   - Author dapat menekan tombol **"Perbaiki Artikel"**, membaca catatan revisi di form edit, merevisi tulisan, lalu menekan **"Simpan & Ajukan Ulang Review"** (status kembali menjadi `pending`).
4. **Pemisahan Peran Tegas (*Separation of Concerns*)**:
   - Author adalah pembuat konten (*creator*).
   - Admin bertindak murni sebagai kurator dan penerbit (*editor-in-chief*), sehingga fitur penulisan artikel untuk Admin ditiadakan demi menjaga integritas alur.

---

## 🔐 2. Akun Pengujian (Demo Accounts)

Untuk mempermudah pengujian alur bolak-balik antara Author dan Admin, silakan gunakan kredensial berikut pada halaman login (`/site/login`):

| Peran (Role) | Username | Password | Otoritas & Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `ADM001` | `admin123` | Meninjau antrean artikel pending, Approve (*Publikasikan*), Reject (*Tolak dengan Alasan*), Hapus artikel, Kelola Akun |
| **Author** | `ATH001` | `author123` | Menulis artikel baru, Simpan Draft pribadi, Ajukan Review, Perbaiki artikel ditolak, Ajukan Ulang Review |

---

## ⚙️ 3. Panduan Instalasi & Menjalankan Proyek

### Prasyarat
- PHP >= 7.4 (diuji pada PHP 8.3 Laragon)
- MySQL / MariaDB
- Web server Apache / Nginx

### Langkah-Langkah:
1. **Konfigurasi Database**  
   Pastikan konfigurasi koneksi database pada file `config/db.php` telah sesuai dengan server lokal Anda:
   ```php
   return [
       'class' => 'yii\db\Connection',
       'dsn' => 'mysql:host=localhost;dbname=blog',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8',
   ];
   ```

2. **Jalankan Migrasi Database**  
   Buka terminal di root proyek dan jalankan:
   ```bash
   php yii migrate
   ```
   *Migrasi baru (`m261106_032949_add_status_and_reject_reason_to_post`) akan otomatis menambahkan kolom `status` dan `reject_reason` pada tabel `post` tanpa merusak data yang sudah ada.*

3. **Akses Aplikasi**  
   Buka peramban (browser) dan akses:
   ```
   http://localhost/blog/web/
   ```

---

## 🧪 4. Skenario Pengujian Alur (Step-by-Step)

### Skenario A: Author Menulis & Mengajukan Review
1. Login dengan akun Author (`ATH001` / `author123`).
2. Masuk ke menu **Artikel** $\rightarrow$ klik tombol **"+ Tulis Artikel Baru"**.
3. Masukkan judul dan konten artikel.
4. Klik **"Simpan sebagai Draft"** (status: *Draft*).
5. Pada daftar dashboard atau halaman detail, klik tombol **"Ajukan Review"** $\rightarrow$ status artikel berubah menjadi **Menunggu Review** (*Pending*).

### Skenario B: Admin Meninjau & Menolak (Reject) dengan Alasan
1. Keluar (*Logout*), lalu login dengan akun Admin (`ADM001` / `admin123`).
2. Masuk ke menu **Artikel** $\rightarrow$ klik tab **"Menunggu Review"**.
3. Klik tombol **"Tolak"** pada artikel yang bersangkutan.
4. Modal dialog penolakan akan muncul. Isikan alasan/catatan perbaikan (misal: *"Mohon tambahkan sumber referensi pada paragraf 2"*), lalu klik **"Kirim Penolakan"** $\rightarrow$ status artikel berubah menjadi **Ditolak** (*Rejected*).

### Skenario C: Author Memperbaiki & Mengajukan Ulang
1. Login kembali sebagai Author (`ATH001` / `author123`).
2. Buka menu **Artikel** $\rightarrow$ klik tab **"Ditolak"**.
3. Perhatikan kotak merah di bawah judul artikel yang menampilkan alasan penolakan dari Admin.
4. Klik tombol **"Edit"** atau **"Perbaiki Artikel"**. Di bagian atas formulir akan tampil kutipan catatan revisi admin.
5. Perbaiki isi konten, lalu klik tombol **"Simpan & Ajukan Ulang Review"** $\rightarrow$ status artikel kembali menjadi **Menunggu Review** (*Pending*).

### Skenario D: Admin Menyetujui (Approve) & Publikasi
1. Login kembali sebagai Admin (`ADM001` / `admin123`).
2. Buka tab **"Menunggu Review"** $\rightarrow$ klik tombol **"Setujui"**.
3. Status artikel berubah menjadi **Published**.
4. Buka halaman **Home** (Beranda Publik) $\rightarrow$ artikel kini resmi terbit dan dapat dibaca oleh seluruh pengunjung blog.

---

## 🎨 5. Peningkatan UI / UX yang Diterapkan

1. **Dashboard KPI Stat Cards**:
   - Kartu ringkasan jumlah status artikel interaktif (*Semua, Menunggu Review, Published, Ditolak, Draft*) yang dapat diklik langsung sebagai jalan pintas filter.
2. **Segmented Filter Tabs**:
   - Navigasi tab modern dengan *counter badge* live untuk memfilter data tanpa kebingungan.
3. **Pjax Seamless Reload (Smooth Refresh)**:
   - Filtering data, pergantian tab, dan pagination dibungkus dengan `yii\widgets\Pjax` sehingga perpindahan data berjalan instan tanpa kedip putih (*no full-page reload*).
4. **Top Progress Bar & Animasi Halus**:
   - Garis progres meluncur di bagian atas layar dan animasi *fade-in* halus untuk transisi halaman yang nyaman.
5. **Desain Bersih & Tipografi Modern**:
   - Menggunakan Google Fonts **Inter** dan ikon vektor **FontAwesome 6**.
   - Navbar modern (*Clean White*) dengan avatar inisial dan label peran.

