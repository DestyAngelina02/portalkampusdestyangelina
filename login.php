<?php
require_once __DIR__ . '/db.php';
$data = load_data();
$error = '';

// Handle Quick Login dari Index
if (isset($_GET['quick'])) {
    $role = $_GET['quick'];
    foreach ($data['users'] as $u) {
        if ($u['role'] === $role) {
            $_SESSION['user'] = $u;
            header('Location: portal.php');
            exit;
        }
    }
}

// Handle Form Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $matched = null;
    foreach ($data['users'] as $u) {
        if ($u['username'] === $username && $u['password'] === $password) {
            $matched = $u;
            break;
        }
    }

    if ($matched) {
        $_SESSION['user'] = $matched;
        header('Location: portal.php');
        exit;
    } else {
        $error = 'Username atau kata sandi tidak valid. Silakan gunakan akun sintetis demo.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Portal - Universitas Angolia</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: radial-gradient(circle at 50% 20%, #0c2554 0%, #06132d 60%, #030814 100%); min-height: 100vh; display: flex; flex-direction: column;">

    <!-- TOP MINI HEADER -->
    <div style="padding: 20px 30px; display: flex; justify-content: space-between; align-items: center;">
        <a href="index.php" class="brand-wrapper" style="color: #fff;">
            <div class="brand-logo-badge">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="brand-text">
                <h1 style="color: #fff;">UNIVERSITAS ANGOLIA</h1>
                <span style="color: var(--secondary);">PORTAL RESMI AKADEMIK</span>
            </div>
        </a>
        <a href="index.php" class="btn btn-outline btn-sm" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25); color: #fff;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="max-width: 480px; width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); padding: 36px;">
            <div style="text-align: center; margin-bottom: 26px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 26px; margin-bottom: 12px; box-shadow: 0 4px 15px var(--primary-glow);">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2 style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 800; color: #0f172a;">Akses Portal Kampus</h2>
                <p style="color: #64748b; font-size: 0.88rem; margin-top: 4px;">Single Sign-On (SSO) Mahasiswa, Dosen, & Administrator</p>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 14px; border-radius: 6px; font-size: 0.88rem; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username / NIM / NIDN</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: mahasiswa / dosen / admin" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-key"></i> Kata Sandi</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
                </button>
            </form>

            <!-- AKUN DEMO SINTETIS -->
            <div style="margin-top: 30px; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
                <p style="font-size: 0.82rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 10px; text-align: center;">
                    ⚡ Klik Langsung Akun Demo Latihan:
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                    <a href="login.php?quick=mahasiswa" class="btn btn-sm" style="background:#e0f2fe; color:#0369a1; font-size:0.75rem; border:none; text-align:center;">
                        Mahasiswa
                    </a>
                    <a href="login.php?quick=dosen" class="btn btn-sm" style="background:#fef3c7; color:#b45309; font-size:0.75rem; border:none; text-align:center;">
                        Dosen
                    </a>
                    <a href="login.php?quick=admin" class="btn btn-sm" style="background:#fee2e2; color:#b91c1c; font-size:0.75rem; border:none; text-align:center;">
                        Admin
                    </a>
                </div>
                <div style="background:#f8fafc; border-radius:8px; padding:10px; margin-top:12px; font-size:0.75rem; color:#64748b; line-height:1.5;">
                    <div>• <strong>Mahasiswa:</strong> <code>mahasiswa</code> / <code>mhs123</code></div>
                    <div>• <strong>Dosen:</strong> <code>dosen</code> / <code>dosen123</code></div>
                    <div>• <strong>Admin:</strong> <code>admin</code> / <code>admin123</code></div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
