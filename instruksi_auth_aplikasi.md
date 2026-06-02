# Instruksi Penambahan Fitur Autentikasi (Login & Register)

Dokumen ini berisi panduan tingkat tinggi (high-level) untuk menambahkan fitur autentikasi pengguna pada aplikasi SanguKu. Fitur ini akan menggunakan Email dan Password dengan metode enkripsi MD5 (sesuai spesifikasi khusus) dan memastikan data transaksi setiap pengguna terisolasi (tidak tertukar).

> **Peringatan Keamanan (Catatan untuk Programmer)**:
> Laravel secara bawaan menggunakan algoritma hashing *Bcrypt* yang sangat aman. Penggunaan *MD5* untuk password sudah dianggap tidak aman di standar industri saat ini karena rentan terhadap serangan *brute-force* dan *rainbow tables*. Namun, untuk memenuhi instruksi spesifik pada tugas ini, panduan di bawah akan menjelaskan cara mengimplementasikan kustomisasi MD5 di Laravel.

---

## 1. Modifikasi Database (Migration)

### A. Tabel `users`
Gunakan migration bawaan Laravel untuk tabel `users`. Pastikan tabel tersebut memiliki kolom:
*   `id` (Primary Key)
*   `email` (String, Unique)
*   `password` (String) - *Akan digunakan untuk menyimpan hasil hash MD5*
*   `timestamps()`

### B. Modifikasi Tabel `transactions`
Untuk memastikan data tidak tertukar antar pengguna, tabel transaksi harus berelasi dengan tabel `users`.
*   Buat migration baru untuk menambahkan kolom ke tabel `transactions` (misal: `php artisan make:migration add_user_id_to_transactions_table`).
*   Tambahkan kolom `user_id` (Tipe: `foreignId` atau `unsignedBigInteger`).
*   Set foreign key constraint agar `user_id` merujuk ke kolom `id` di tabel `users`.

---

## 2. Pembaruan Model (Relasi & Fillable)

### A. Model `User` (`app/Models/User.php`)
*   Pastikan `email` dan `password` terdaftar di dalam properti `$fillable`.
*   Definisikan relasi **One-to-Many**: Buat method `transactions()` yang mereturn `$this->hasMany(Transaction::class);`.

### B. Model `Transaction` (`app/Models/Transaction.php`)
*   Tambahkan `user_id` ke dalam properti `$fillable` agar bisa diisi saat menyimpan data.
*   Definisikan relasi **Belongs-To**: Buat method `user()` yang mereturn `$this->belongsTo(User::class);`.

---

## 3. Logika Auth & Kustomisasi Hashing MD5

Karena Laravel menggunakan *Bcrypt* pada fitur Auth bawaannya (`Auth::attempt`), kita perlu melakukan autentikasi manual untuk mengecek password MD5. Buat sebuah controller khusus, misalnya `AuthController`.

### A. Proses Registrasi
*   Validasi input (pastikan email belum pernah terdaftar, dan password diisi).
*   Saat menyimpan user ke database, jangan gunakan `Hash::make()`. Gunakan fungsi asli PHP `md5()`.
    *   *Contoh:* `'password' => md5($request->password)`
*   Setelah berhasil disimpan, langsung login-kan pengguna tersebut menggunakan `Auth::login($user)` dan arahkan (redirect) ke halaman dashboard.

### B. Proses Login
*   Validasi input email dan password dari form.
*   Lakukan pengecekan manual:
    1.  Cari user berdasarkan email (`User::where('email', $request->email)->first()`).
    2.  Jika user ditemukan, cocokkan password dari form yang telah di-hash MD5 dengan password di database.
        *   *Contoh Logika:* `if ($user && $user->password === md5($request->password))`
    3.  Jika cocok, jalankan `Auth::login($user)` dan arahkan ke dashboard.
    4.  Jika gagal, arahkan kembali ke form login dengan pesan error (misal: "Email atau password salah").

### C. Proses Logout
*   Gunakan `Auth::logout()`, lalu *invalidate session*, dan arahkan kembali pengguna ke halaman form login.

---

## 4. Pembaruan Logika Transaksi (Isolasi Data)

Agar data tidak tertukar, pastikan setiap operasi (simpan dan baca) di `TransactionController` difilter berdasarkan pengguna yang sedang aktif (login).

### A. Menyimpan Transaksi (Method `storeVoice`)
*   Saat melakukan insert ke database (`Transaction::create(...)`), sisipkan data `user_id`.
*   Ambil ID user yang sedang login dengan fungsi bawaan Laravel: `Auth::id()`.

### B. Menampilkan Transaksi (Method `index` dan `exportPdf`)
*   Ubah *query builder* saat mengambil data transaksi dari database. Jangan ambil semua (all), melainkan difilter berdasarkan `user_id`.
*   *Sebelum:* `Transaction::orderBy('transaction_date', 'desc')->get()`
*   *Sesudah:* `Transaction::where('user_id', Auth::id())->orderBy('transaction_date', 'desc')->get()`
*   Pastikan kalkulasi total pemasukan, pengeluaran, dan saldo bersih juga hanya menghitung (sum) milik user terkait (tambahkan klausa `where('user_id', Auth::id())`).

---

## 5. Konfigurasi Routing & Middleware

Buka file `routes/web.php` dan kelompokkan rute untuk membatasi akses:

*   **Rute Publik (Middleware: `guest`)**:
    *   `GET /login`, `POST /login`
    *   `GET /register`, `POST /register`
    *   *Catatan:* Middleware `guest` akan memastikan orang yang sudah login tidak bisa membuka halaman login/register lagi.
*   **Rute Terproteksi (Middleware: `auth`)**:
    *   Pindahkan rute `/` (Dashboard), rute voice API (`POST /transactions/voice`), rute hapus transaksi, rute PDF, dan rute `POST /logout` ke dalam grup middleware `auth`.
    *   *Catatan:* Middleware ini akan menolak siapa pun yang belum login untuk masuk ke dashboard, dan mengarahkannya ke halaman login.

---

## 6. Desain Tampilan (View)

*   **Halaman Login & Register**: Buat file view baru (misal: `resources/views/auth/login.blade.php` dan `register.blade.php`). Gunakan desain *glassmorphism* bertema gelap (dark mode) agar senada dan tetap premium seperti desain dashboard sebelumnya.
*   **Header Dashboard**: Modifikasi file `dashboard.blade.php` yang sudah ada untuk menambahkan nama pengguna yang sedang login (misal: "Halo, Budi") dan sebuah tombol **Logout**.
