<?php
require_once __DIR__ . '/db.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universitas Angolia - Fostering and Empowering</title>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230054a6'><path d='M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z'/></svg>">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- TOP MINI BAR (BINUS STYLE) -->
    <div class="top-util-bar">
        <div class="top-util-container">
            <a href="portal.php"><i class="fa-solid fa-graduation-cap"></i> Portal Mahasiswa</a>
            <a href="portal.php"><i class="fa-solid fa-chalkboard-user"></i> Dosen & Staf</a>
            <a href="#tentang"><i class="fa-solid fa-users"></i> Orang Tua</a>
            <a href="#alumni"><i class="fa-solid fa-user-group"></i> Alumni</a>
            <a href="#karir"><i class="fa-solid fa-briefcase"></i> Karier</a>
            <a href="#kontak"><i class="fa-solid fa-phone"></i> Hubungi Kami</a>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <header class="navbar-main">
        <div class="nav-container">
            <a href="index.php" class="brand-wrapper">
                <div class="brand-logo-badge">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="brand-text">
                    <h1>UNIVERSITAS ANGOLIA</h1>
                    <span>People . Innovation . Excellence</span>
                </div>
            </a>

            <ul class="nav-menu">
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="#tentang">Tentang Kami</a></li>
                <li><a href="#program">Program Studi</a></li>
                <li><a href="#prestasi">Penelitian & Publikasi</a></li>
                <li><a href="#multikampus">Multi-Kampus</a></li>
                <li><a href="#penerimaan">Penerimaan</a></li>
            </ul>

            <div class="nav-actions">
                <?php if ($user): ?>
                    <a href="portal.php" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($user['nama']) ?>
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Akun
                    </a>
                    <a href="login.php" class="btn btn-secondary btn-sm">
                        MULAI DARI SINI <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <!-- HERO REPLIKA PERSIS GAMBAR BINUS -->
        <section class="hero-portal">
            <div class="hero-glow-sphere"></div>
            
            <div class="hero-container">
                <div class="hero-welcome-badge">
                    <span>SELAMAT DATANG DI</span>
                    <h2>UNIVERSITAS ANGOLIA</h2>
                </div>

                <div class="hero-the-rank">
                    <span class="the-tag">THE</span>
                    <span style="font-size:0.9rem; font-weight:700; letter-spacing:0.5px;">World University Rankings 2027</span>
                    <i class="fa-solid fa-award" style="color:#ffd700;"></i>
                </div>

                <div class="hero-ranking-cards">
                    <div class="rank-card-box">
                        <div class="rank-num">#1</div>
                        <div class="rank-label">Private Universities in Indonesia</div>
                    </div>
                    <div class="rank-card-box">
                        <div class="rank-num" style="font-size: 2.8rem; padding-top: 8px;">1401 - 1600</div>
                        <div class="rank-label">Global Rank Worldwide</div>
                    </div>
                    <div class="rank-card-box">
                        <div class="rank-num">98.4%</div>
                        <div class="rank-label">Graduate Employability Rate</div>
                    </div>
                </div>

                <!-- PORTAL SIMULASI LATIHAN AKSES CEPAT -->
                <div style="margin-top: 50px;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: #e2e8f0; margin-bottom: 8px;">
                        <i class="fa-solid fa-door-open" style="color: var(--secondary);"></i> Portal Latihan Akademik & Interaksi Kampus
                    </h3>
                    <p style="color: #94a3b8; font-size: 0.95rem; max-width: 650px; margin: 0 auto 20px;">
                        Pilih peran sintetis di bawah ini untuk langsung mencoba fitur Mahasiswa (unggah tugas & forum), Dosen (penilaian), atau Admin (kelola user).
                    </p>

                    <div class="quick-login-grid">
                        <!-- Mahasiswa -->
                        <div class="quick-login-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                <span class="role-badge role-mhs"><i class="fa-solid fa-user-graduate"></i> Mahasiswa</span>
                                <span style="font-size:0.75rem; color:#64748b;">NIM: 2401019001</span>
                            </div>
                            <h4 style="font-weight:700; margin-bottom:4px;">Bintang Arya Nugraha</h4>
                            <p style="font-size:0.83rem; color:#64748b; margin-bottom:14px;">Melihat KHS nilai, upload berkas tugas kuliah, dan berdiskusi dengan dosen.</p>
                            <a href="login.php?quick=mahasiswa" class="btn btn-primary btn-sm" style="width:100%;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sebagai Mahasiswa
                            </a>
                        </div>

                        <!-- Dosen -->
                        <div class="quick-login-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                <span class="role-badge role-dosen"><i class="fa-solid fa-chalkboard-user"></i> Dosen Pengampu</span>
                                <span style="font-size:0.75rem; color:#64748b;">NIDN: 0412088501</span>
                            </div>
                            <h4 style="font-weight:700; margin-bottom:4px;">Prof. Dr. Ir. Rendy Anggara</h4>
                            <p style="font-size:0.83rem; color:#64748b; margin-bottom:14px;">Memeriksa submission tugas mahasiswa, memberi nilai angka, serta menjawab forum.</p>
                            <a href="login.php?quick=dosen" class="btn btn-secondary btn-sm" style="width:100%;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sebagai Dosen
                            </a>
                        </div>

                        <!-- Admin -->
                        <div class="quick-login-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                <span class="role-badge role-admin"><i class="fa-solid fa-shield-halved"></i> Administrator</span>
                                <span style="font-size:0.75rem; color:#64748b;">ID: ADM-001</span>
                            </div>
                            <h4 style="font-weight:700; margin-bottom:4px;">Dr. H. Anggoro Pratama</h4>
                            <p style="font-size:0.83rem; color:#64748b; margin-bottom:14px;">Kelola akun sintetis (tambah, edit, hapus) mahasiswa, dosen, serta mata kuliah.</p>
                            <a href="login.php?quick=admin" class="btn btn-outline btn-sm" style="width:100%; border-color:#94a3b8;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sebagai Admin
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- FITUR UTAMA & INFORMASI AKADEMIK -->
        <section id="program" style="max-width: 1280px; margin: 60px auto 30px; padding: 0 24px;">
            <div style="text-align: center; margin-bottom: 45px;">
                <span style="font-weight:700; color:var(--secondary); text-transform:uppercase; letter-spacing:1px; font-size:0.85rem;">Keunggulan Akademik</span>
                <h2 style="font-family:var(--font-heading); font-size:2rem; font-weight:800; color:#0f172a; margin-top:5px;">Fakultas & Program Studi Terdepan</h2>
                <div style="width: 60px; height: 3px; background: var(--primary); margin: 12px auto 0;"></div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
                <div class="card" style="border-top: 4px solid var(--primary);">
                    <div style="font-size: 2rem; color: var(--primary); margin-bottom: 12px;"><i class="fa-solid fa-microchip"></i></div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 8px;">School of Computer Science</h3>
                    <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 15px;">Fokus pada Artificial Intelligence, Software Engineering, Cloud Computing, Cyber Security, dan Big Data Architecture terakreditasi internasional.</p>
                    <span style="font-size:0.85rem; font-weight:700; color:var(--primary);">Akreditasi Unggul • ABET Accredited</span>
                </div>

                <div class="card" style="border-top: 4px solid var(--secondary);">
                    <div style="font-size: 2rem; color: var(--secondary); margin-bottom: 12px;"><i class="fa-solid fa-chart-line"></i></div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 8px;">School of Information Systems</h3>
                    <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 15px;">Transformasi proses bisnis digital, enterprise architecture, ERP systems, dan integrasi analitik intelijen bisnis masa kini.</p>
                    <span style="font-size:0.85rem; font-weight:700; color:var(--secondary);">Akreditasi Unggul • AACSB Member</span>
                </div>

                <div class="card" style="border-top: 4px solid var(--accent-emerald);">
                    <div style="font-size: 2rem; color: var(--accent-emerald); margin-bottom: 12px;"><i class="fa-solid fa-robot"></i></div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 8px;">Faculty of Engineering & Robotics</h3>
                    <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 15px;">Teknik Komputer, Otomasi Industri, Internet of Things (IoT), serta robotika modern berstandar industri global 4.0.</p>
                    <span style="font-size:0.85rem; font-weight:700; color:var(--accent-emerald);">Akreditasi Unggul • IABEE Certified</span>
                </div>
            </div>
        </section>

        <!-- BERITA & PENGUMUMAN KAMPUS -->
        <section id="prestasi" style="background:#f1f5f9; padding: 50px 0;">
            <div style="max-width: 1280px; margin: 0 auto; padding: 0 24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; flex-wrap:wrap; gap:15px;">
                    <div>
                        <h2 style="font-family:var(--font-heading); font-size:1.8rem; font-weight:800; color:#0f172a;">Pengumuman & Kabar Kampus Angolia</h2>
                        <p style="color:#64748b; font-size:0.92rem;">Update informasi akademik, beasiswa, dan prestasi terbaru</p>
                    </div>
                    <a href="portal.php" class="btn btn-outline btn-sm">Lihat Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(350px, 1fr)); gap:24px;">
                    <div class="card">
                        <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                            <span class="the-tag">PRESTASI 2026</span>
                            <span style="font-size:0.82rem; color:#64748b;"><i class="fa-regular fa-clock"></i> 12 Oktober 2026</span>
                        </div>
                        <h4 style="font-weight:700; font-size:1.15rem; margin-bottom:8px; color:#0f172a;">
                            Dies Natalis ke-45: Universitas Angolia Kokohkan Posisi Kampus Riset Swasta #1
                        </h4>
                        <p style="font-size:0.9rem; color:#64748b;">
                            Capaian Times Higher Education 2027 menjadi bukti nyata komitmen Angolia dalam melahirkan riset berdaya guna bagi peradaban dunia.
                        </p>
                    </div>

                    <div class="card">
                        <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                            <span class="the-tag" style="background:#0284c7;">BEASISWA</span>
                            <span style="font-size:0.82rem; color:#64748b;"><i class="fa-regular fa-clock"></i> 10 Oktober 2026</span>
                        </div>
                        <h4 style="font-weight:700; font-size:1.15rem; margin-bottom:8px; color:#0f172a;">
                            Program Beasiswa Unggulan Prestasi & AI Future Leader Batch Ganjil
                        </h4>
                        <p style="font-size:0.9rem; color:#64748b;">
                            Dapatkan beasiswa penuh 100% biaya studi hingga lulus serta kesempatan magang di top tech companies Silicon Valley dan Singapura.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER RESMI UNIVERSITAS ANGOLIA -->
    <footer class="site-footer" id="tentang">
        <div class="footer-container">
            <div>
                <div class="brand-wrapper" style="margin-bottom: 16px;">
                    <div class="brand-logo-badge" style="width:40px; height:40px; font-size:18px;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="brand-text">
                        <h3 style="color:#fff; font-family:var(--font-heading); font-size:1.2rem; font-weight:800;">UNIVERSITAS ANGOLIA</h3>
                        <span style="color:var(--secondary); font-size:0.75rem; letter-spacing:1px;">EXCELLENCE & INNOVATION</span>
                    </div>
                </div>
                <p style="font-size:0.88rem; line-height:1.7; color:#94a3b8;">
                    Universitas Angolia berkomitmen membangun generasi unggul berdaya saing global melalui kurikulum adaptif, teknologi modern, dan ekosistem riset kolaboratif.
                </p>
            </div>

            <div>
                <h4 style="color:#fff; font-weight:700; margin-bottom:14px; font-size:1rem;">Kampus Kami</h4>
                <ul style="list-style:none; font-size:0.88rem; display:flex; flex-direction:column; gap:8px;">
                    <li><a href="#">Angolia @ Kemanggisan</a></li>
                    <li><a href="#">Angolia @ Alam Sutera</a></li>
                    <li><a href="#">Angolia @ Bekasi</a></li>
                    <li><a href="#">Angolia @ Bandung</a></li>
                    <li><a href="#">Angolia @ Semarang</a></li>
                </ul>
            </div>

            <div>
                <h4 style="color:#fff; font-weight:700; margin-bottom:14px; font-size:1rem;">Tautan Cepat</h4>
                <ul style="list-style:none; font-size:0.88rem; display:flex; flex-direction:column; gap:8px;">
                    <li><a href="login.php">Portal Akademik</a></li>
                    <li><a href="portal.php">Unggah Tugas Kuliah</a></li>
                    <li><a href="portal.php">Transkrip & KHS Nilai</a></li>
                    <li><a href="portal.php">Forum Diskusi</a></li>
                    <li><a href="login.php">Manajemen Akun</a></li>
                </ul>
            </div>

            <div id="kontak">
                <h4 style="color:#fff; font-weight:700; margin-bottom:14px; font-size:1rem;">Hubungi Kami</h4>
                <p style="font-size:0.88rem; line-height:1.6; color:#94a3b8; margin-bottom:10px;">
                    Jl. Angolia Raya No. 45, Jakarta Barat, 11480<br>
                    Telp: (021) 534-5830<br>
                    Email: admisi@angolia.ac.id
                </p>
                <div style="display:flex; gap:12px; font-size:1.1rem; color:#cbd5e1;">
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                &copy; 2026 Universitas Angolia. Hak Cipta Dilindungi Undang-Undang. Portal Latihan Mahasiswa, Dosen & Admin.
            </div>
            <div style="display:flex; gap:16px;">
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
                <a href="#">Pedoman Akademik</a>
            </div>
        </div>
    </footer>

</body>
</html>
