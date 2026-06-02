# Instruksi Pembuatan Aplikasi Pencatatan Keuangan Berbasis Suara

Dokumen ini berisi panduan tingkat tinggi (high-level) untuk membangun aplikasi pencatatan keuangan pribadi menggunakan input suara. Panduan ini dirancang agar mudah dipahami dan diimplementasikan oleh programmer junior dengan tetap menjaga standar kode yang baik (maintainable) menggunakan arsitektur MVC.

## 1. Spesifikasi & Teknologi Utama
*   **Backend Framework**: Laravel (PHP)
*   **Database**: MySQL
*   **Frontend**: Blade Template Engine (Bawaan Laravel) + Vanilla JavaScript (untuk Web Audio API)
*   **Speech-to-Text (STT)**: API stt.ai (atau opsi alternatif seperti Google Cloud Speech-to-Text / OpenAI Whisper).
*   **PDF Generator**: Package `barryvdh/laravel-dompdf` (Rekomendasi standar di Laravel).

---

## 2. Struktur Database (Model)
Kita perlu membuat sebuah tabel untuk menyimpan data transaksi.

*   **Tabel**: `transactions`
*   **Kolom**:
    *   `id` (Primary Key, Auto Increment)
    *   `type` (Enum: 'income' untuk pemasukan, 'expense' untuk pengeluaran)
    *   `amount` (Decimal / BigInteger) - Nominal uang
    *   `description` (Text) - Hasil transkrip suara asli atau keterangan transaksi
    *   `transaction_date` (Date / Timestamp) - Waktu transaksi
    *   `timestamps` (created_at, updated_at - bawaan Laravel)

> **Instruksi MVC**: Buat Migration dan Model `Transaction` menggunakan command artisan (`php artisan make:model Transaction -m`).

---

## 3. Alur Kerja Aplikasi & Logika MVC

### A. Perekaman Suara (Frontend -> View)
1.  Buat sebuah halaman antarmuka (misalnya `record.blade.php`) yang memiliki tombol "Mulai Rekam" dan "Berhenti Rekam".
2.  Gunakan **MediaRecorder API** (JavaScript bawaan browser) untuk menangkap suara dari microphone pengguna.
3.  Setelah selesai merekam, JavaScript akan mengirimkan file audio (berupa Blob, misal format `.wav` atau `.webm`) ke endpoint backend Laravel menggunakan `fetch` atau `axios` (AJAX).

### B. Pemrosesan Suara & Ekstraksi Data (Backend -> Controller)
Buat sebuah fungsi di dalam Controller (misal `TransactionController@storeVoice`) yang akan menangani proses berikut:

1.  **Terima File Audio**: Menerima request file audio dari frontend.
2.  **Kirim ke STT API**: Gunakan HTTP Client bawaan Laravel (`Illuminate\Support\Facades\Http`) untuk mengirim audio tersebut ke layanan pihak ketiga (stt.ai).
3.  **Terima Transkrip Teks**: Dapatkan balasan berupa teks dari API STT (misal: "Saya membeli makan siang 50000").
4.  **Logika Ekstraksi (Rule-based If/Else)**:
    Lakukan pengecekan manual (parsing) pada teks transkrip untuk menentukan `type` dan `amount`.
    *   **Menentukan Tipe (`type`)**:
        *   Jika kalimat mengandung kata: `beli`, `membeli`, `bayar`, `keluar`, `jajan` -> `type = 'expense'` (Pengeluaran).
        *   Jika kalimat mengandung kata: `dapat`, `menerima`, `terima`, `gaji`, `dikasih` -> `type = 'income'` (Pemasukan).
    *   **Menentukan Nominal (`amount`)**:
        *   Gunakan Regular Expression (Regex) di PHP (`preg_match`) untuk mengekstrak angka saja dari kalimat teks tersebut (misal: ekstrak `50000` dari "membeli makan siang 50000").


### C. Penyimpanan Data (Backend -> Model)
1.  Setelah `type`, `amount`, dan teks asli didapatkan, gunakan Eloquent Model untuk menyimpannya ke database.
    ```php
    Transaction::create([
        'type' => $type_hasil_ekstraksi,
        'amount' => $amount_hasil_ekstraksi,
        'description' => $teks_asli_dari_stt,
        'transaction_date' => now(),
    ]);
    ```
2.  Kembalikan respon sukses (JSON) ke frontend agar UI bisa memberi tahu pengguna bahwa transaksi berhasil dicatat.

### D. Fitur Riwayat & Export PDF (Controller & View)
1.  **Halaman Riwayat (`TransactionController@index`)**:
    *   Ambil data dari model (`Transaction::latest()->get()`).
    *   Kirim data tersebut ke view `history.blade.php`.
    *   Tampilkan dalam bentuk tabel HTML.
2.  **Export PDF (`TransactionController@exportPdf`)**:
    *   Ambil data transaksi.
    *   Load data tersebut ke sebuah view template khusus PDF (misal `pdf.blade.php`).
    *   Gunakan Facade dari package dompdf (misal `Pdf::loadView(...)`).
    *   Kembalikan dengan metode `->download('laporan_keuangan.pdf')`.
    *   Letakkan tombol "Export PDF" di halaman riwayat yang mengarah ke route fungsi ini.

---

## 4. Rangkuman Langkah Pengerjaan untuk Programmer Junior

1.  **Setup Environment**: Install Laravel, buat database MySQL, sesuaikan `.env`.
2.  **Install Dependencies**: Install `barryvdh/laravel-dompdf` via composer.
3.  **Database & Model**: Jalankan `php artisan make:model Transaction -m`. Isi migration sesuai spesifikasi di atas, lalu jalankan `php artisan migrate`.
4.  **Routing**: Definisikan Routes (`routes/web.php`):
    *   `GET /` -> Menampilkan halaman utama / riwayat.
    *   `GET /record` -> Menampilkan form rekam suara.
    *   `POST /api/record` -> Memproses upload audio & logika STT.
    *   `GET /export-pdf` -> Memproses download PDF.
5.  **Controller**: Buat `TransactionController`. Tulis fungsi untuk mereturn View, memproses API call ke `stt.ai`, logika `if/else`, dan logika download PDF.
6.  **Views**: Buat halaman layout dasar, buat halaman tabel riwayat, halaman rekam (dengan JS MediaRecorder), dan layout PDF.
7.  **Testing**: Coba rekam suara secara lokal, pastikan API stt.ai merespon dengan benar, pastikan logika regex mengekstrak angka dengan tepat, dan cek apakah file terdownload dengan rapi sebagai PDF.
