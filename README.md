# Portal Kampus Universitas Angolia (Latihan Akademik Terpadu)

Portal kampus fiktif modern bernuansa desain seperti portal universitas ternama (BINUS University), dilengkapi dengan sistem latihan interaktif untuk **Mahasiswa**, **Dosen**, dan **Administrator**.

---

## 🚀 Cara Menjalankan dengan Cepat (1-Klik)

Cukup **klik dua kali (double click)** file:
👉 **`jalankan_portal.bat`**

File script ini akan secara otomatis:
1. Mendeteksi PHP di komputer Anda (PATH sistem atau `C:\xampp\php\php.exe`).
2. Menjalankan built-in PHP web server di port `8000`.
3. Membuka browser langsung ke alamat: `http://localhost:8000`.

---

## 🔑 Akun Demo Sintetis untuk Latihan

| Peran (Role) | Username | Password | Deskripsi Fitur |
|---|---|---|---|
| **Mahasiswa** | `mahasiswa` | `mhs123` | Melihat KHS & IPK, mengunggah tugas kuliah (upload file), berdiskusi di forum |
| **Dosen** | `dosen` | `dosen123` | Memeriksa berkas tugas mahasiswa, memberikan skor/nilai & feedback, update nilai KHS |
| **Admin** | `admin` | `admin123` | Manajemen akun civitas (tambah akun mahasiswa/dosen baru, hapus akun, kelola data) |

> 💡 *Catatan:* Pada halaman login dan beranda, sudah tersedia tombol **Quick Login 1-Klik** untuk langsung berganti peran tanpa harus mengetik ulang kata sandi.

---

## 🌟 Fitur Utama Sistem

1. **Tampilan Modern (BINUS-Style Theme)**:
   - Banner hero interaktif dengan peringkat THE World University Rankings (#1 Private University in Indonesia, 1401-1600 Global Rank).
   - Top utility bar (Portal Mahasiswa, Dosen & Staf, Alumni, Karier).
   - Palet warna eksklusif (Deep Blue `#0054a6` dan Vibrant Orange `#ff7900`).
2. **Mahasiswa**:
   - Transkrip & KHS lengkap dengan bobot persentase Tugas (30%), UTS (35%), UAS (35%), dan kalkulasi IPK.
   - Modul upload tugas kuliah dengan form file attachment dan catatan mahasiswa.
   - Tampilan status tugas: *Sudah Mengunggah*, *Belum Mengumpulkan*, serta hasil evaluasi & nilai dari dosen.
3. **Dosen**:
   - Tabel penilaian tugas yang masuk secara real-time. Dosen dapat mengunduh berkas tugas dan mengisi nilai + catatan evaluasi.
   - Manajemen penilaian KHS mahasiswa untuk penentuan nilai akhir dan grade huruf (A, A-, B+, B, C).
4. **Administrator**:
   - CRUD Akun Civitas: Formulir pendaftaran akun baru lengkap dengan role, NIM/NIDN, jurusan, dan email.
   - Daftar tabel pengguna sistem dengan opsi hapus user.
5. **Forum Diskusi Akademik**:
   - Diskusi interaktif terhubung antar dosen dan mahasiswa per mata kuliah.
   - Fitur membuat topik pertanyaan dan membalas tanggapan secara real-time.
6. **Penyimpanan Data JSON**:
   - Tanpa perlu setup database MySQL/XAMPP yang rumit! Semua data tersimpan otomatis di `data/portal_data.json` dan file tugas tersimpan di direktori `uploads/`.
