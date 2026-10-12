<?php
require_once __DIR__ . '/db.php';
$user = check_auth();
$data = load_data();

$role = $user['role'];
$tab = $_GET['tab'] ?? ($role === 'mahasiswa' ? 'nilai' : ($role === 'dosen' ? 'penilaian' : 'users'));
$action_msg = $_GET['msg'] ?? '';

// ========================
// HANDLER AKSI-AKSI
// ========================

// 1. MAHASISWA: UNGGAH TUGAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_tugas') {
    if ($role === 'mahasiswa') {
        $assignment_id = $_POST['assignment_id'] ?? '';
        $catatan = trim($_POST['catatan'] ?? '');
        $file_name = 'sample_dokumen.pdf';
        $file_path = 'uploads/sample_arsitektur.pdf';

        if (isset($_FILES['file_tugas']) && $_FILES['file_tugas']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['file_tugas']['tmp_name'];
            $orig_name = basename($_FILES['file_tugas']['name']);
            $dest_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $orig_name);
            $dest_path = UPLOAD_DIR . '/' . $dest_name;
            if (move_uploaded_file($tmp_name, $dest_path)) {
                $file_name = $orig_name;
                $file_path = 'uploads/' . $dest_name;
            }
        }

        // Cek apakah sudah pernah upload untuk assignment ini
        $found = false;
        foreach ($data['submissions'] as &$sub) {
            if ($sub['assignment_id'] === $assignment_id && $sub['student_id'] === $user['id']) {
                $sub['catatan'] = $catatan;
                $sub['file_name'] = $file_name;
                $sub['file_path'] = $file_path;
                $sub['submitted_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        unset($sub);

        if (!$found) {
            $new_sub = [
                'id' => 'SUB' . (count($data['submissions']) + 1),
                'assignment_id' => $assignment_id,
                'student_id' => $user['id'],
                'student_nama' => $user['nama'],
                'student_nim' => $user['nim_nidn'],
                'catatan' => $catatan,
                'file_name' => $file_name,
                'file_path' => $file_path,
                'submitted_at' => date('Y-m-d H:i:s'),
                'nilai' => null,
                'feedback' => null
            ];
            $data['submissions'][] = $new_sub;
        }

        save_data($data);
        header('Location: portal.php?tab=tugas&msg=upload_success');
        exit;
    }
}

// 2. DOSEN: BERI NILAI SUBMISSION & NILAI KHS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'grade_submission') {
    if ($role === 'dosen') {
        $sub_id = $_POST['submission_id'] ?? '';
        $nilai = floatval($_POST['nilai'] ?? 0);
        $feedback = trim($_POST['feedback'] ?? '');

        foreach ($data['submissions'] as &$sub) {
            if ($sub['id'] === $sub_id) {
                $sub['nilai'] = $nilai;
                $sub['feedback'] = $feedback;
                break;
            }
        }
        unset($sub);

        save_data($data);
        header('Location: portal.php?tab=penilaian&msg=grade_success');
        exit;
    }
}

// DOSEN: UPDATE NILAI KHS AKHIR MAHASISWA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_khs') {
    if ($role === 'dosen') {
        $grade_id = $_POST['grade_id'] ?? '';
        $tugas = floatval($_POST['tugas'] ?? 0);
        $uts = floatval($_POST['uts'] ?? 0);
        $uas = floatval($_POST['uas'] ?? 0);
        $akhir = round(($tugas * 0.3) + ($uts * 0.35) + ($uas * 0.35), 1);
        $huruf = ($akhir >= 85) ? 'A' : (($akhir >= 80) ? 'A-' : (($akhir >= 75) ? 'B+' : (($akhir >= 70) ? 'B' : 'C')));

        foreach ($data['grades'] as &$g) {
            if ($g['id'] === $grade_id) {
                $g['tugas'] = $tugas;
                $g['uts'] = $uts;
                $g['uas'] = $uas;
                $g['akhir'] = $akhir;
                $g['huruf'] = $huruf;
                break;
            }
        }
        unset($g);

        save_data($data);
        header('Location: portal.php?tab=khs_input&msg=khs_updated');
        exit;
    }
}

// 3. SEMUA: FORUM DISKUSI (POST TOPIK & REPLY)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_discussion') {
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $course_id = $_POST['course_id'] ?? 'MK01';

    if (!empty($judul) && !empty($isi)) {
        $new_topic = [
            'id' => 'DSC' . (count($data['discussions']) + 1),
            'course_id' => $course_id,
            'user_id' => $user['id'],
            'user_nama' => $user['nama'],
            'user_role' => $user['role'],
            'judul' => $judul,
            'isi' => $isi,
            'waktu' => date('Y-m-d H:i'),
            'replies' => []
        ];
        array_unshift($data['discussions'], $new_topic);
        save_data($data);
        header('Location: portal.php?tab=forum&msg=topic_created');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply_discussion') {
    $topic_id = $_POST['topic_id'] ?? '';
    $reply_isi = trim($_POST['isi'] ?? '');

    if (!empty($reply_isi)) {
        foreach ($data['discussions'] as &$top) {
            if ($top['id'] === $topic_id) {
                $top['replies'][] = [
                    'id' => 'REP' . (rand(100, 999)),
                    'user_id' => $user['id'],
                    'user_nama' => $user['nama'],
                    'user_role' => $user['role'],
                    'isi' => $reply_isi,
                    'waktu' => date('Y-m-d H:i')
                ];
                break;
            }
        }
        unset($top);
        save_data($data);
        header('Location: portal.php?tab=forum&msg=reply_added');
        exit;
    }
}

// 4. ADMIN: MANAJEMEN AKUN (TAMBAH / EDIT / HAPUS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_add_user') {
    if ($role === 'admin') {
        $new_user = [
            'id' => 'usr_' . time(),
            'username' => trim($_POST['username']),
            'password' => trim($_POST['password']),
            'nama' => trim($_POST['nama']),
            'email' => trim($_POST['email']),
            'role' => $_POST['role_user'],
            'nim_nidn' => trim($_POST['nim_nidn']),
            'jurusan' => trim($_POST['jurusan']),
            'foto' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80'
        ];
        $data['users'][] = $new_user;
        save_data($data);
        header('Location: portal.php?tab=users&msg=user_added');
        exit;
    }
}

if (isset($_GET['delete_user']) && $role === 'admin') {
    $del_id = $_GET['delete_user'];
    $data['users'] = array_values(array_filter($data['users'], function($u) use ($del_id) {
        return $u['id'] !== $del_id;
    }));
    save_data($data);
    header('Location: portal.php?tab=users&msg=user_deleted');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Akademik - Universitas Angolia</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- TOP MINI BAR -->
    <div class="top-util-bar">
        <div class="top-util-container">
            <span><i class="fa-regular fa-calendar-check"></i> Tahun Akademik 2026/2027 Ganjil</span>
            <span><i class="fa-solid fa-server"></i> Server: Angolia Cloud Node-ID</span>
            <a href="index.php"><i class="fa-solid fa-globe"></i> Website Utama</a>
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
                    <span>SISTEM INFORMASI AKADEMIK TERPADU</span>
                </div>
            </a>

            <div class="nav-actions">
                <div style="text-align: right; margin-right: 8px;">
                    <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a;"><?= htmlspecialchars($user['nama']) ?></div>
                    <span class="role-badge role-<?= $user['role'] ?>"><?= strtoupper($user['role']) ?> • <?= htmlspecialchars($user['nim_nidn']) ?></span>
                </div>
                <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                </a>
            </div>
        </div>
    </header>

    <!-- ALERT STATUS MESSAGE -->
    <?php if ($action_msg): ?>
        <div style="max-width: 1280px; margin: 20px auto 0; padding: 0 24px;">
            <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: 10px; display: flex; align-items: center; gap: 10px; font-weight: 600;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
                <?php
                    $alerts = [
                        'upload_success' => 'Tugas berhasil diunggah / diperbarui ke sistem dosen.',
                        'grade_success' => 'Nilai dan feedback submission berhasil disimpan.',
                        'khs_updated' => 'Nilai KHS semester mahasiswa berhasil diperbarui.',
                        'topic_created' => 'Topik diskusi baru berhasil diterbitkan.',
                        'reply_added' => 'Tanggapan diskusi berhasil dikirim.',
                        'user_added' => 'Akun pengguna baru berhasil ditambahkan.',
                        'user_deleted' => 'Akun pengguna berhasil dihapus dari sistem.'
                    ];
                    echo htmlspecialchars($alerts[$action_msg] ?? 'Aksi berhasil dieksekusi.');
                ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="dashboard-container">
        <!-- SIDEBAR PROFIL & NAVIGASI -->
        <aside>
            <div class="sidebar-profile-card">
                <img src="<?= htmlspecialchars($user['foto'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80') ?>" alt="Foto Profil" class="avatar-lg">
                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 700; color: #0f172a;">
                    <?= htmlspecialchars($user['nama']) ?>
                </h3>
                <p style="font-size: 0.84rem; color: #64748b; margin-top: 2px;">
                    <?= htmlspecialchars($user['email']) ?>
                </p>
                <div style="margin-top: 10px;">
                    <span class="role-badge role-<?= $user['role'] ?>">Peran: <?= strtoupper($user['role']) ?></span>
                </div>
                <div style="font-size: 0.8rem; color: #475569; margin-top: 8px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                    <strong>Program / Unit:</strong><br><?= htmlspecialchars($user['jurusan'] ?? 'Teknik Informatika') ?>
                </div>

                <!-- MENU NAVIGASI PERAN -->
                <ul class="sidebar-nav">
                    <?php if ($role === 'mahasiswa'): ?>
                        <li>
                            <a href="portal.php?tab=nilai" class="<?= $tab === 'nilai' ? 'active' : '' ?>">
                                <i class="fa-solid fa-chart-pie"></i> Nilai KHS & IPK
                            </a>
                        </li>
                        <li>
                            <a href="portal.php?tab=tugas" class="<?= $tab === 'tugas' ? 'active' : '' ?>">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Unggah Tugas Kuliah
                            </a>
                        </li>
                    <?php elseif ($role === 'dosen'): ?>
                        <li>
                            <a href="portal.php?tab=penilaian" class="<?= $tab === 'penilaian' ? 'active' : '' ?>">
                                <i class="fa-solid fa-file-signature"></i> Penilaian Tugas Mahasiswa
                            </a>
                        </li>
                        <li>
                            <a href="portal.php?tab=khs_input" class="<?= $tab === 'khs_input' ? 'active' : '' ?>">
                                <i class="fa-solid fa-table-list"></i> Kelola Nilai KHS / Mata Kuliah
                            </a>
                        </li>
                    <?php elseif ($role === 'admin'): ?>
                        <li>
                            <a href="portal.php?tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">
                                <i class="fa-solid fa-users-gear"></i> Manajemen Akun Sivitas
                            </a>
                        </li>
                        <li>
                            <a href="portal.php?tab=courses" class="<?= $tab === 'courses' ? 'active' : '' ?>">
                                <i class="fa-solid fa-book-open"></i> Manajemen Mata Kuliah
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- SHARED MENU -->
                    <li>
                        <a href="portal.php?tab=forum" class="<?= $tab === 'forum' ? 'active' : '' ?>">
                            <i class="fa-solid fa-comments"></i> Forum Diskusi Kampus
                        </a>
                    </li>
                    <li>
                        <a href="portal.php?tab=matakuliah" class="<?= $tab === 'matakuliah' ? 'active' : '' ?>">
                            <i class="fa-solid fa-calendar-days"></i> Jadwal Perkuliahan
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- KONTEN AREA UTAMA -->
        <main class="content-area">

            <!-- ============================================== -->
            <!-- TAB MAHASISWA: NILAI KHS & IPK                 -->
            <!-- ============================================== -->
            <?php if ($role === 'mahasiswa' && $tab === 'nilai'): ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-award" style="color:var(--primary);"></i> Kartu Hasil Studi (KHS) & Transkrip</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Daftar perolehan nilai mata kuliah semester aktif</p>
                        </div>
                        <div style="background:#e0f2fe; padding:8px 18px; border-radius:10px; text-align:right;">
                            <span style="font-size:0.75rem; color:#0369a1; font-weight:700;">IPK KUMULATIF</span>
                            <div style="font-size:1.6rem; font-weight:800; color:#0369a1; line-height:1;">3.89</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Nilai Tugas (30%)</th>
                                    <th>UTS (35%)</th>
                                    <th>UAS (35%)</th>
                                    <th>Nilai Akhir</th>
                                    <th>Huruf</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $mhs_grades = array_filter($data['grades'], function($g) use ($user) {
                                    return $g['student_id'] === $user['id'];
                                });
                                foreach ($mhs_grades as $gr):
                                    $c_info = null;
                                    foreach ($data['courses'] as $c) {
                                        if ($c['id'] === $gr['course_id']) { $c_info = $c; break; }
                                    }
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($c_info['kode'] ?? '-') ?></strong></td>
                                    <td>
                                        <div style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($c_info['nama'] ?? 'Mata Kuliah') ?></div>
                                        <span style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($c_info['dosen_nama'] ?? '') ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($c_info['sks'] ?? 3) ?> SKS</td>
                                    <td><span style="font-weight:600; color:#2563eb;"><?= $gr['tugas'] ?></span></td>
                                    <td><span style="font-weight:600; color:#2563eb;"><?= $gr['uts'] ?></span></td>
                                    <td><span style="font-weight:600; color:#2563eb;"><?= $gr['uas'] ?></span></td>
                                    <td><strong style="font-size:1.05rem;"><?= $gr['akhir'] ?></strong></td>
                                    <td>
                                        <span class="role-badge" style="background:#dcfce7; color:#15803d; font-size:0.9rem; padding:4px 12px;">
                                            <?= $gr['huruf'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- TAB MAHASISWA: UNGGAH TUGAS KULIAH             -->
            <!-- ============================================== -->
            <?php elseif ($role === 'mahasiswa' && $tab === 'tugas'): ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-cloud-arrow-up" style="color:var(--primary);"></i> Unggah Tugas & Proyek Kuliah</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Kumpulkan tugas tepat waktu sebelum batas deadline dosen</p>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <?php foreach ($data['assignments'] as $asg): 
                            $c_info = null;
                            foreach ($data['courses'] as $c) {
                                if ($c['id'] === $asg['course_id']) { $c_info = $c; break; }
                            }
                            $my_sub = null;
                            foreach ($data['submissions'] as $sub) {
                                if ($sub['assignment_id'] === $asg['id'] && $sub['student_id'] === $user['id']) {
                                    $my_sub = $sub;
                                    break;
                                }
                            }
                        ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; background: #ffffff; box-shadow: var(--shadow-sm);">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; gap:10px;">
                                <div>
                                    <span class="role-badge role-mhs" style="margin-bottom:6px;"><?= htmlspecialchars($c_info['nama'] ?? 'Mata Kuliah') ?></span>
                                    <h3 style="font-size:1.15rem; font-weight:700; color:#0f172a;"><?= htmlspecialchars($asg['judul']) ?></h3>
                                    <p style="color:#475569; font-size:0.9rem; margin-top:6px;"><?= htmlspecialchars($asg['deskripsi']) ?></p>
                                </div>
                                <div style="text-align:right;">
                                    <span style="font-size:0.8rem; color:#e11d48; font-weight:700; display:block;">
                                        <i class="fa-regular fa-clock"></i> Deadline: <?= htmlspecialchars($asg['deadline']) ?>
                                    </span>
                                    <?php if ($my_sub): ?>
                                        <span class="role-badge" style="background:#dcfce7; color:#15803d; margin-top:5px;">
                                            <i class="fa-solid fa-check-circle"></i> Sudah Mengunggah
                                        </span>
                                    <?php else: ?>
                                        <span class="role-badge" style="background:#fee2e2; color:#b91c1c; margin-top:5px;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Belum Mengumpulkan
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- HASIL REVIEW DOSEN JIKA SUDAH DINILAI -->
                            <?php if ($my_sub && $my_sub['nilai'] !== null): ?>
                                <div style="background:#f0fdf4; border-left:4px solid #22c55e; padding:14px 16px; border-radius:6px; margin:16px 0;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <strong style="color:#15803d;"><i class="fa-solid fa-star"></i> Nilai Tugas Dosen:</strong>
                                        <span style="font-size:1.4rem; font-weight:800; color:#15803d;"><?= $my_sub['nilai'] ?> / 100</span>
                                    </div>
                                    <p style="font-size:0.88rem; color:#166534; margin-top:4px;">
                                        <strong>Feedback Dosen:</strong> "<?= htmlspecialchars($my_sub['feedback'] ?? 'Kerja bagus!') ?>"
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- FORM UPLOAD / EDIT BERKAS -->
                            <form method="POST" action="portal.php" enctype="multipart/form-data" style="margin-top: 16px; background:#f8fafc; padding:16px; border-radius:10px;">
                                <input type="hidden" name="action" value="upload_tugas">
                                <input type="hidden" name="assignment_id" value="<?= htmlspecialchars($asg['id']) ?>">

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label"><i class="fa-solid fa-file"></i> Pilih Berkas Tugas (PDF/ZIP/DOCX)</label>
                                        <input type="file" name="file_tugas" class="form-control" style="padding:7px 10px;">
                                        <?php if ($my_sub): ?>
                                            <span style="font-size:0.75rem; color:#0369a1; margin-top:4px; display:block;">
                                                Berkas terunggah saat ini: <strong><?= htmlspecialchars($my_sub['file_name']) ?></strong> (<?= htmlspecialchars($my_sub['submitted_at']) ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label"><i class="fa-solid fa-comment-dots"></i> Catatan Mahasiswa</label>
                                        <input type="text" name="catatan" class="form-control" placeholder="Tuliskan catatan tambahan..." value="<?= htmlspecialchars($my_sub['catatan'] ?? '') ?>">
                                    </div>
                                </div>

                                <div style="margin-top: 12px; display:flex; justify-content:flex-end;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> <?= $my_sub ? 'Perbarui Submission Tugas' : 'Unggah Tugas Sekarang' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- TAB DOSEN: PENILAIAN TUGAS MAHASISWA           -->
            <!-- ============================================== -->
            <?php elseif ($role === 'dosen' && $tab === 'penilaian'): ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-file-signature" style="color:var(--primary);"></i> Penilaian Tugas & Submission Mahasiswa</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Evaluasi hasil karya mahasiswa dan berikan skor nilai serta masukan konstruktif</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Tugas & Matkul</th>
                                    <th>Waktu Unggah</th>
                                    <th>Berkas Tugas</th>
                                    <th>Catatan</th>
                                    <th>Nilai Saat Ini</th>
                                    <th>Aksi Penilaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['submissions'] as $sub): 
                                    $asg_info = null;
                                    foreach ($data['assignments'] as $a) {
                                        if ($a['id'] === $sub['assignment_id']) { $asg_info = $a; break; }
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <strong style="color:#0f172a;"><?= htmlspecialchars($sub['student_nama']) ?></strong>
                                        <div style="font-size:0.8rem; color:#64748b;">NIM: <?= htmlspecialchars($sub['student_nim']) ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;"><?= htmlspecialchars($asg_info['judul'] ?? 'Tugas') ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size:0.82rem; color:#64748b;"><?= htmlspecialchars($sub['submitted_at']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= htmlspecialchars($sub['file_path']) ?>" download class="btn btn-outline btn-sm" style="font-size:0.78rem;">
                                            <i class="fa-solid fa-file-arrow-down"></i> <?= htmlspecialchars($sub['file_name']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <small style="color:#475569;"><?= htmlspecialchars($sub['catatan'] ?: '-') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($sub['nilai'] !== null): ?>
                                            <span class="role-badge" style="background:#dcfce7; color:#15803d; font-size:0.9rem; padding:4px 10px;">
                                                <?= $sub['nilai'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="role-badge" style="background:#fee2e2; color:#b91c1c;">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- FORM INPUT NILAI LANGSUNG -->
                                        <form method="POST" action="portal.php" style="display:flex; flex-direction:column; gap:6px;">
                                            <input type="hidden" name="action" value="grade_submission">
                                            <input type="hidden" name="submission_id" value="<?= htmlspecialchars($sub['id']) ?>">
                                            <div style="display:flex; gap:6px;">
                                                <input type="number" step="0.1" min="0" max="100" name="nilai" class="form-control" placeholder="0-100" value="<?= htmlspecialchars($sub['nilai'] ?? '') ?>" style="width:75px; padding:4px 8px; font-size:0.85rem;" required>
                                                <input type="text" name="feedback" class="form-control" placeholder="Feedback dosen..." value="<?= htmlspecialchars($sub['feedback'] ?? '') ?>" style="padding:4px 8px; font-size:0.85rem;" required>
                                            </div>
                                            <button type="submit" class="btn btn-secondary btn-sm" style="padding:4px 8px; font-size:0.75rem;">
                                                <i class="fa-solid fa-check"></i> Simpan Nilai
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- TAB DOSEN: KELOLA KHS MAHASISWA                -->
            <!-- ============================================== -->
            <?php elseif ($role === 'dosen' && $tab === 'khs_input'): ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-table-list" style="color:var(--primary);"></i> Rekapitulasi & Pembobotan Nilai KHS Semester</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Hitung nilai komposit (30% Tugas + 35% UTS + 35% UAS) untuk transkrip mahasiswa</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Mata Kuliah</th>
                                    <th>Nilai Tugas (30%)</th>
                                    <th>UTS (35%)</th>
                                    <th>UAS (35%)</th>
                                    <th>Total Akhir</th>
                                    <th>Grade</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['grades'] as $g): 
                                    $student_info = null;
                                    foreach ($data['users'] as $u) {
                                        if ($u['id'] === $g['student_id']) { $student_info = $u; break; }
                                    }
                                    $c_info = null;
                                    foreach ($data['courses'] as $c) {
                                        if ($c['id'] === $g['course_id']) { $c_info = $c; break; }
                                    }
                                ?>
                                <tr>
                                    <form method="POST" action="portal.php">
                                        <input type="hidden" name="action" value="update_khs">
                                        <input type="hidden" name="grade_id" value="<?= htmlspecialchars($g['id']) ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($student_info['nama'] ?? 'Mahasiswa') ?></strong>
                                            <div style="font-size:0.8rem; color:#64748b;">NIM: <?= htmlspecialchars($student_info['nim_nidn'] ?? '-') ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($c_info['nama'] ?? 'Matkul') ?></td>
                                        <td>
                                            <input type="number" step="0.5" min="0" max="100" name="tugas" value="<?= $g['tugas'] ?>" class="form-control" style="width:75px; padding:4px 6px;">
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" min="0" max="100" name="uts" value="<?= $g['uts'] ?>" class="form-control" style="width:75px; padding:4px 6px;">
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" min="0" max="100" name="uas" value="<?= $g['uas'] ?>" class="form-control" style="width:75px; padding:4px 6px;">
                                        </td>
                                        <td><strong><?= $g['akhir'] ?></strong></td>
                                        <td>
                                            <span class="role-badge" style="background:#e0f2fe; color:#0369a1;"><?= $g['huruf'] ?></span>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm" style="padding:6px 12px; font-size:0.8rem;">
                                                <i class="fa-solid fa-floppy-disk"></i> Hitung
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- TAB ADMIN: MANAJEMEN AKUN SIVITAS              -->
            <!-- ============================================== -->
            <?php elseif ($role === 'admin' && $tab === 'users'): ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-users-gear" style="color:var(--primary);"></i> Manajemen Akun Sivitas Akademika</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Kelola seluruh akun sintetis untuk mahasiswa, dosen, dan administrator</p>
                        </div>
                    </div>

                    <!-- FORM TAMBAH USER BARU -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:25px;">
                        <h4 style="font-size:1.05rem; font-weight:700; color:#0f172a; margin-bottom:14px;">
                            <i class="fa-solid fa-user-plus" style="color:var(--primary);"></i> Tambah Pengguna Baru (Sintetis)
                        </h4>
                        <form method="POST" action="portal.php">
                            <input type="hidden" name="action" value="admin_add_user">
                            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Andi Pratama" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="Username login" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Kata Sandi</label>
                                    <input type="text" name="password" class="form-control" placeholder="Password" value="123456" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Peran (Role)</label>
                                    <select name="role_user" class="form-control" required>
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="dosen">Dosen</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">NIM / NIDN</label>
                                    <input type="text" name="nim_nidn" class="form-control" placeholder="2401019004" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="user@angolia.ac.id" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Jurusan / Unit</label>
                                    <input type="text" name="jurusan" class="form-control" placeholder="Teknik Informatika" required>
                                </div>
                            </div>
                            <div style="margin-top:16px; text-align:right;">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i> Simpan Akun Baru
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- DAFTAR PENGGUNA SAAT INI -->
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Username & Password</th>
                                    <th>Role</th>
                                    <th>NIM / NIDN</th>
                                    <th>Jurusan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['users'] as $u): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <img src="<?= htmlspecialchars($u['foto']) ?>" style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                                            <div>
                                                <strong><?= htmlspecialchars($u['nama']) ?></strong>
                                                <div style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($u['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?= htmlspecialchars($u['username']) ?></code> / <code><?= htmlspecialchars($u['password']) ?></code>
                                    </td>
                                    <td>
                                        <span class="role-badge role-<?= $u['role'] ?>"><?= strtoupper($u['role']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($u['nim_nidn']) ?></td>
                                    <td><?= htmlspecialchars($u['jurusan']) ?></td>
                                    <td>
                                        <?php if ($u['id'] !== $user['id']): ?>
                                            <a href="portal.php?delete_user=<?= urlencode($u['id']) ?>" class="btn btn-danger btn-sm" style="padding:4px 10px; font-size:0.75rem;" onclick="return confirm('Hapus akun ini?')">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </a>
                                        <?php else: ?>
                                            <span style="font-size:0.78rem; color:#94a3b8;">(Akun Anda)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- TAB FORUM DISKUSI INTERAKTIF                   -->
            <!-- ============================================== -->
            <?php elseif ($tab === 'forum'): ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-comments" style="color:var(--primary);"></i> Forum Kolaborasi & Diskusi Akademik</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Ruang tanya jawab antara mahasiswa dan dosen pengampu mata kuliah</p>
                        </div>
                    </div>

                    <!-- FORM BUAT TOPIK BARU -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:25px;">
                        <h4 style="font-size:1.05rem; font-weight:700; color:#0f172a; margin-bottom:12px;">
                            <i class="fa-solid fa-pen-to-square" style="color:var(--primary);"></i> Buat Topik Diskusi Baru
                        </h4>
                        <form method="POST" action="portal.php">
                            <input type="hidden" name="action" value="add_discussion">
                            <div class="form-group">
                                <label class="form-label">Terkait Mata Kuliah</label>
                                <select name="course_id" class="form-control" style="max-width:350px;">
                                    <?php foreach ($data['courses'] as $c): ?>
                                        <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['kode']) ?> - <?= htmlspecialchars($c['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Judul Diskusi</label>
                                <input type="text" name="judul" class="form-control" placeholder="Contoh: Pertanyaan seputar implementasi Kafka" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Isi Pertanyaan / Topik</label>
                                <textarea name="isi" class="form-control" rows="3" placeholder="Tuliskan detail pertanyaan atau materi yang ingin didiskusikan..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-paper-plane"></i> Publikasikan Topik
                            </button>
                        </form>
                    </div>

                    <!-- DAFTAR TOPIK DISKUSI -->
                    <div>
                        <?php foreach ($data['discussions'] as $disc): 
                            $c_info = null;
                            foreach ($data['courses'] as $c) {
                                if ($c['id'] === $disc['course_id']) { $c_info = $c; break; }
                            }
                        ?>
                        <div class="discussion-item">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div>
                                    <span class="role-badge role-<?= $disc['user_role'] ?>" style="margin-bottom:6px;"><?= strtoupper($disc['user_role']) ?></span>
                                    <span style="font-size:0.8rem; color:#64748b; margin-left:8px;">
                                        <i class="fa-solid fa-book"></i> <?= htmlspecialchars($c_info['nama'] ?? 'Umum') ?>
                                    </span>
                                    <h3 style="font-size:1.15rem; font-weight:700; color:#0f172a; margin-top:4px;"><?= htmlspecialchars($disc['judul']) ?></h3>
                                </div>
                                <span style="font-size:0.78rem; color:#64748b;"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($disc['waktu']) ?></span>
                            </div>
                            
                            <p style="color:#334155; font-size:0.92rem; margin:12px 0; line-height:1.6;">
                                <?= nl2br(htmlspecialchars($disc['isi'])) ?>
                            </p>
                            
                            <div style="font-size:0.82rem; color:#64748b; font-weight:600;">
                                Dibuat oleh: <span style="color:#0f172a;"><?= htmlspecialchars($disc['user_nama']) ?></span>
                            </div>

                            <!-- DAFTAR REPLIES -->
                            <div style="margin-top:16px;">
                                <h5 style="font-size:0.88rem; font-weight:700; color:#475569; margin-bottom:8px;">
                                    <i class="fa-solid fa-reply-all"></i> Tanggapan (<?= count($disc['replies'] ?? []) ?>)
                                </h5>
                                <?php if (!empty($disc['replies'])): ?>
                                    <?php foreach ($disc['replies'] as $rep): ?>
                                    <div class="discussion-reply">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                            <div>
                                                <strong><?= htmlspecialchars($rep['user_nama']) ?></strong>
                                                <span class="role-badge role-<?= $rep['user_role'] ?>" style="font-size:0.68rem; margin-left:6px;"><?= strtoupper($rep['user_role']) ?></span>
                                            </div>
                                            <span style="font-size:0.75rem; color:#94a3b8;"><?= htmlspecialchars($rep['waktu']) ?></span>
                                        </div>
                                        <p style="font-size:0.88rem; color:#334155;"><?= nl2br(htmlspecialchars($rep['isi'])) ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- FORM BALAS REPLY -->
                                <form method="POST" action="portal.php" style="margin-top:12px; display:flex; gap:8px;">
                                    <input type="hidden" name="action" value="reply_discussion">
                                    <input type="hidden" name="topic_id" value="<?= htmlspecialchars($disc['id']) ?>">
                                    <input type="text" name="isi" class="form-control" placeholder="Tulis tanggapan / jawaban Anda..." required style="font-size:0.88rem;">
                                    <button type="submit" class="btn btn-secondary btn-sm" style="white-space:nowrap;">
                                        <i class="fa-solid fa-reply"></i> Balas
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- TAB JADWAL / MATA KULIAH                       -->
            <!-- ============================================== -->
            <?php else: ?>
                <div class="card">
                    <div class="card-header-styled">
                        <div>
                            <h2 class="card-title"><i class="fa-solid fa-book-bookmark" style="color:var(--primary);"></i> Daftar Mata Kuliah & Jadwal Aktif</h2>
                            <p style="color:#64748b; font-size:0.88rem;">Kurikulum pembelajaran semester berjalan Universitas Angolia</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>Beban SKS</th>
                                    <th>Dosen Pengampu</th>
                                    <th>Jadwal Perkuliahan & Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['courses'] as $c): ?>
                                <tr>
                                    <td><span class="role-badge role-mhs" style="font-size:0.85rem;"><?= htmlspecialchars($c['kode']) ?></span></td>
                                    <td><strong style="font-size:1rem; color:#0f172a;"><?= htmlspecialchars($c['nama']) ?></strong></td>
                                    <td><strong><?= htmlspecialchars($c['sks']) ?> SKS</strong></td>
                                    <td>
                                        <i class="fa-solid fa-user-tie" style="color:var(--secondary); margin-right:5px;"></i>
                                        <?= htmlspecialchars($c['dosen_nama']) ?>
                                    </td>
                                    <td>
                                        <i class="fa-regular fa-clock" style="color:#64748b; margin-right:5px;"></i>
                                        <?= htmlspecialchars($c['jadwal']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>
