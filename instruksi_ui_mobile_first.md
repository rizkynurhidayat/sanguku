# Instruksi Perbaikan UI: Pendekatan Mobile-First

Dokumen ini berisi panduan tingkat tinggi (high-level) untuk memperbaiki antarmuka (UI) aplikasi SanguKu agar lebih ramah pengguna (user-friendly) di perangkat seluler (HP), mengingat mayoritas pengguna akan mengakses aplikasi ini melalui HP.

Prinsip **Mobile-First** berarti kita harus mendesain dan menulis kode CSS untuk layar kecil terlebih dahulu sebagai pengaturan bawaan (default). Setelah tampilan HP sempurna, barulah kita menambahkan aturan (*Media Queries*) untuk layar besar (Tablet/Desktop).

---

## 1. Konsep Dasar CSS Mobile-First

Bagi programmer junior, biasakan untuk membangun tata letak (layout) secara berurutan:
*   **Default CSS (Tanpa Media Query)**: Tulis CSS untuk layar HP di sini. Elemen biasanya memiliki lebar 100% (`width: 100%`) dan ditumpuk secara vertikal (contoh: `flex-direction: column`).
*   **Media Query Desktop (`@media (min-width: 768px)`)**: Tulis CSS untuk menimpa ukuran HP hanya ketika layar membesar. Di sinilah Anda mengubah layout vertikal menjadi bersebelahan (grid multi-kolom).

---

## 2. Penyesuaian Komponen Utama (High-Level)

### A. Layout Dasar & Area Sentuh (Touch Target)
*   **Margin & Padding**: Di layar HP, kurangi padding samping (kiri/kanan) pada *container* utama menjadi sekitar `16px` atau `1rem` agar ruang konten lebih lega.
*   **Ukuran Tombol**: Jari manusia butuh ruang yang cukup. Pastikan semua elemen yang bisa di-klik (tombol login, tombol rekam, tombol hapus) memiliki area sentuh (*touch target*) **minimal tinggi 44px hingga 48px**.

### B. Header & Navigasi
*   Di versi mobile, hindari meletakkan terlalu banyak teks di Header agar tidak menumpuk.
*   Teks sapaan panjang seperti *"Halo, [Nama User]"* dapat disingkat atau dikecilkan ukuran font-nya (`font-size`).
*   Pastikan Header selalu menggunakan `display: flex` dengan `justify-content: space-between; align-items: center;` agar Logo dan tombol Logout selalu berada rapi di sisi berlawanan.

### C. Kartu Ringkasan (Summary Cards)
*   Pada layar HP, kartu informasi (Saldo Bersih, Pemasukan, Pengeluaran) tidak boleh berjejer menyamping karena teks akan terpotong.
*   **Instruksi**: Buat kartu-kartu ini bertumpuk dari atas ke bawah.
    *   *Default*: `grid-template-columns: 1fr;` (1 kolom)
    *   *Min-width 768px*: `grid-template-columns: repeat(3, 1fr);` (3 kolom untuk desktop)

### D. Tombol Mikrofon (Prioritas Utama)
Karena merekam suara adalah fitur utama (*Core UX*):
*   Pastikan tombol rekam suara ukurannya cukup besar dan berada di posisi yang sangat jelas di layar HP.
*   *Opsional/Rekomendasi*: Untuk UX yang lebih modern, Anda bisa mempertimbangkan membuat bagian rekaman ini lengket (*sticky* di bagian bawah layar - `position: fixed; bottom: 20px;`) sehingga saat pengguna men-scroll panjang ke bawah melihat riwayat transaksi, tombol mikrofon tetap selalu bisa ditekan kapan saja tanpa harus *scroll* ke atas lagi.

### E. Tabel Riwayat Transaksi (Paling Krusial)
Tabel HTML biasa (`<table>`) sangat tidak responsif di HP karena memakan ruang horizontal yang panjang. Ada dua cara yang bisa programmer junior pilih:

1.  **Cara Cepat (Responsive Scroll)**:
    Bungkus tabel Anda dengan tag `<div>` baru, lalu berikan CSS: `overflow-x: auto; white-space: nowrap;`. Pengguna HP bisa menggeser (swipe) area tabel ke kiri dan kanan.
2.  **Cara UX Terbaik (Card Layout di Mobile)**:
    Gunakan CSS untuk mengubah tampilan tabel menjadi seperti sekumpulan kartu di HP.
    *   Sembunyikan header tabel (`<thead>`) di ukuran mobile (`display: none;`).
    *   Ubah setiap baris `<tr>` menjadi `display: block;` (agar berwujud kartu dengan border).
    *   Ubah isi kolom `<td>` menjadi `display: flex; justify-content: space-between;` (beri label teks bantuan secara manual sebelum data jika diperlukan).
    *   Pada layar desktop (`@media (min-width: 768px)`), kembalikan properti display-nya ke format tabel asli (`display: table-row;`, `display: table-cell;`).

---

## 3. Langkah Eksekusi

1.  Buka file view terkait (`dashboard.blade.php`, `auth/login.blade.php`, `auth/register.blade.php`).
2.  Cari tag `<style>` atau file CSS eksternal.
3.  Ubah seluruh penulisan CSS yang berpatokan pada ukuran desktop menjadi ukuran mobile (lebarkan width ke 100%, posisikan kolom ke bawah).
4.  Tambahkan *breakpoint* `@media (min-width: 768px) { ... }` di bagian terbawah kode CSS Anda, lalu isi dengan gaya khusus untuk layar komputer (seperti grid 3 kolom atau tabel normal).
5.  Uji coba hasil pekerjaan dengan menekan tombol **F12** (Developer Tools) di browser (Chrome/Edge), nyalakan fitur **Device Toolbar** (ikon HP/Tablet), pilih ukuran seperti iPhone atau Samsung Galaxy, dan pastikan tidak ada teks yang tumpah atau tombol yang sulit di-tap jari.
