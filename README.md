# Decision Support System Using Fuzzy AHP

Sistem Pendukung Keputusan (SPK) ini dikembangkan menggunakan metode **Fuzzy Analytical Hierarchy Process (Fuzzy AHP)** untuk membantu pengambilan keputusan multi-kriteria secara lebih objektif dan terstruktur.

## 🧠 Metode yang Digunakan

Metode **Fuzzy AHP** adalah pengembangan dari metode AHP yang menggabungkan logika fuzzy untuk mengatasi ketidakpastian dan subjektivitas dalam penilaian kriteria. Sistem ini cocok untuk berbagai kasus pengambilan keputusan, seperti:

- Penentuan kelas santri
- Seleksi calon penerima bantuan
- Pemilihan lokasi bisnis
- Dll.

## 💻 Fitur Utama

- Input data kriteria dan alternatif
- Penilaian perbandingan berpasangan antar kriteria (pairwise comparison)
- Konversi nilai linguistik ke fuzzy number (TFN)
- Perhitungan bobot prioritas menggunakan Fuzzy AHP
- Perankingan alternatif berdasarkan bobot akhir
- Antarmuka berbasis web (PHP dan MySQL)

## 📷 Tampilan Antarmuka
> <img width="1344" height="646" alt="Screenshot 2024-10-25 212634" src="https://github.com/user-attachments/assets/2ff3e2c5-9f2d-4eaa-b721-170dacaaa49b" />


## ⚙️ Teknologi yang Digunakan

- PHP (Backend)
- MySQL (Database)
- HTML, CSS (Frontend)
- Bootstrap (UI Styling)
- JavaScript (Interaktif)

## 🗂️ Struktur Folder

```bash
Decision-Support-System-Using-Fuzzy-AHP/
├── config/               # Konfigurasi database
├── pages/                # Halaman-halaman sistem (input, hasil, dll.)
├── proses/               # File pemrosesan (fuzzy, perhitungan, dll.)
├── assets/               # CSS, JS, gambar
├── index.php             # Halaman utama
└── README.md             # Dokumentasi proyek
