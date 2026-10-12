<?php
session_start();

// Database file path
define('DATA_DIR', __DIR__ . '/data');
define('DATA_FILE', DATA_DIR . '/portal_data.json');
define('UPLOAD_DIR', __DIR__ . '/uploads');

if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Initial Data Seed
function get_initial_data() {
    return [
        'users' => [
            [
                'id' => 'adm_01',
                'username' => 'admin',
                'password' => 'admin123',
                'nama' => 'Dr. H. Anggoro Pratama, M.Kom',
                'email' => 'admin@angolia.ac.id',
                'role' => 'admin',
                'nim_nidn' => 'ADM-001',
                'jurusan' => 'Biro Sistem Informasi',
                'foto' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'
            ],
            [
                'id' => 'dos_01',
                'username' => 'dosen',
                'password' => 'dosen123',
                'nama' => 'Prof. Dr. Ir. Rendy Anggara, S.Kom., M.T.',
                'email' => 'rendy.anggara@angolia.ac.id',
                'role' => 'dosen',
                'nim_nidn' => '0412088501',
                'jurusan' => 'Teknik Informatika',
                'foto' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80'
            ],
            [
                'id' => 'dos_02',
                'username' => 'dosen2',
                'password' => 'dosen123',
                'nama' => 'Dr. Maria Jessica, M.Sc.',
                'email' => 'maria.jessica@angolia.ac.id',
                'role' => 'dosen',
                'nim_nidn' => '0418048702',
                'jurusan' => 'Sistem Informasi',
                'foto' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80'
            ],
            [
                'id' => 'mhs_01',
                'username' => 'mahasiswa',
                'password' => 'mhs123',
                'nama' => 'Bintang Arya Nugraha',
                'email' => 'bintang.arya@student.angolia.ac.id',
                'role' => 'mahasiswa',
                'nim_nidn' => '2401019001',
                'jurusan' => 'Teknik Informatika',
                'semester' => 4,
                'foto' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80'
            ],
            [
                'id' => 'mhs_02',
                'username' => 'clarissa',
                'password' => 'mhs123',
                'nama' => 'Clarissa Putri Maharani',
                'email' => 'clarissa.putri@student.angolia.ac.id',
                'role' => 'mahasiswa',
                'nim_nidn' => '2401019002',
                'jurusan' => 'Teknik Informatika',
                'semester' => 4,
                'foto' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80'
            ],
            [
                'id' => 'mhs_03',
                'username' => 'david',
                'password' => 'mhs123',
                'nama' => 'David Christian Wijaya',
                'email' => 'david.c@student.angolia.ac.id',
                'role' => 'mahasiswa',
                'nim_nidn' => '2401019003',
                'jurusan' => 'Sistem Informasi',
                'semester' => 4,
                'foto' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80'
            ]
        ],
        'courses' => [
            [
                'id' => 'MK01',
                'kode' => 'IF204',
                'nama' => 'Rekayasa Perangkat Lunak Modern',
                'sks' => 3,
                'dosen_id' => 'dos_01',
                'dosen_nama' => 'Prof. Dr. Ir. Rendy Anggara, S.Kom., M.T.',
                'jadwal' => 'Senin, 08:00 - 10:30 WIB | Lab Komputer 3'
            ],
            [
                'id' => 'MK02',
                'kode' => 'IF302',
                'nama' => 'Kecerdasan Buatan & Deep Learning',
                'sks' => 3,
                'dosen_id' => 'dos_01',
                'dosen_nama' => 'Prof. Dr. Ir. Rendy Anggara, S.Kom., M.T.',
                'jadwal' => 'Rabu, 13:00 - 15:30 WIB | R. Teater 201'
            ],
            [
                'id' => 'MK03',
                'kode' => 'SI208',
                'nama' => 'Manajemen Basis Data Terdistribusi',
                'sks' => 4,
                'dosen_id' => 'dos_02',
                'dosen_nama' => 'Dr. Maria Jessica, M.Sc.',
                'jadwal' => 'Kamis, 10:00 - 13:00 WIB | Lab Cyber Security'
            ]
        ],
        'assignments' => [
            [
                'id' => 'TS01',
                'course_id' => 'MK01',
                'judul' => 'Tugas 1: Desain Arsitektur Microservices & Clean Architecture',
                'deskripsi' => 'Rancanglah diagram arsitektur enterprise dan dokumentasi class diagram untuk sistem perbankan digital. Unggah berkas dokumen (PDF/ZIP).',
                'deadline' => '2026-10-25 23:59',
                'dosen_id' => 'dos_01'
            ],
            [
                'id' => 'TS02',
                'course_id' => 'MK02',
                'judul' => 'Tugas 2: Implementasi CNN untuk Klasifikasi Citra Medis',
                'deskripsi' => 'Latih model machine learning menggunakan PyTorch/TensorFlow dengan dataset minimal 2000 sampel. Laporkan akurasi, confusion matrix, dan link code repo.',
                'deadline' => '2026-11-05 23:59',
                'dosen_id' => 'dos_01'
            ],
            [
                'id' => 'TS03',
                'course_id' => 'MK03',
                'judul' => 'Tugas 1: Sharding & Query Optimization MongoDB',
                'deskripsi' => 'Simulasikan cluster replikasi 3 node dan lakukan benchmark throughput query read/write.',
                'deadline' => '2026-10-30 23:59',
                'dosen_id' => 'dos_02'
            ]
        ],
        'submissions' => [
            [
                'id' => 'SUB01',
                'assignment_id' => 'TS01',
                'student_id' => 'mhs_01',
                'student_nama' => 'Bintang Arya Nugraha',
                'student_nim' => '2401019001',
                'catatan' => 'Berikut berkas arsitektur perbankan microservices lengkap dengan benchmark latency.',
                'file_name' => 'arsitektur_bintang_rpl.pdf',
                'file_path' => 'uploads/sample_arsitektur.pdf',
                'submitted_at' => '2026-10-10 14:20:11',
                'nilai' => 92,
                'feedback' => 'Analisis komponen API Gateway dan Kafka event bus sangat detail dan terstruktur. Pertahankan!'
            ],
            [
                'id' => 'SUB02',
                'assignment_id' => 'TS01',
                'student_id' => 'mhs_02',
                'student_nama' => 'Clarissa Putri Maharani',
                'student_nim' => '2401019002',
                'catatan' => 'Tugas RPL - Modul Microservices dan Container Orchestration.',
                'file_name' => 'clarissa_tugas1.pdf',
                'file_path' => 'uploads/sample_arsitektur.pdf',
                'submitted_at' => '2026-10-11 19:40:00',
                'nilai' => 88,
                'feedback' => 'Penjelasan diagram sequence sangat baik, perhatikan handling idempotency token.'
            ]
        ],
        'grades' => [
            [
                'id' => 'GR01',
                'student_id' => 'mhs_01',
                'course_id' => 'MK01',
                'tugas' => 90,
                'uts' => 88,
                'uas' => 92,
                'akhir' => 90.2,
                'huruf' => 'A'
            ],
            [
                'id' => 'GR02',
                'student_id' => 'mhs_01',
                'course_id' => 'MK02',
                'tugas' => 94,
                'uts' => 86,
                'uas' => 90,
                'akhir' => 90.0,
                'huruf' => 'A'
            ],
            [
                'id' => 'GR03',
                'student_id' => 'mhs_01',
                'course_id' => 'MK03',
                'tugas' => 85,
                'uts' => 82,
                'uas' => 88,
                'akhir' => 85.5,
                'huruf' => 'A-'
            ],
            [
                'id' => 'GR04',
                'student_id' => 'mhs_02',
                'course_id' => 'MK01',
                'tugas' => 88,
                'uts' => 85,
                'uas' => 89,
                'akhir' => 87.5,
                'huruf' => 'A-'
            ],
            [
                'id' => 'GR05',
                'student_id' => 'mhs_03',
                'course_id' => 'MK03',
                'tugas' => 80,
                'uts' => 78,
                'uas' => 84,
                'akhir' => 81.2,
                'huruf' => 'B+'
            ]
        ],
        'discussions' => [
            [
                'id' => 'DSC01',
                'course_id' => 'MK01',
                'user_id' => 'mhs_01',
                'user_nama' => 'Bintang Arya Nugraha',
                'user_role' => 'mahasiswa',
                'judul' => 'Tanya implementasi gRPC vs REST API untuk High Throughput service',
                'isi' => 'Selamat pagi Prof Rendy dan rekan-rekan. Pada rancangan arsitektur modul payment, apakah penggunaan gRPC dengan HTTP/2 protobuf lebih direkomendasikan dibanding REST JSON biasa untuk traffic jutaan req/detik?',
                'waktu' => '2026-10-11 09:15',
                'replies' => [
                    [
                        'id' => 'REP01',
                        'user_id' => 'dos_01',
                        'user_nama' => 'Prof. Dr. Ir. Rendy Anggara, S.Kom., M.T.',
                        'user_role' => 'dosen',
                        'isi' => 'Pertanyaan bagus Bintang! Untuk inter-service communication (internal microservices) sangat dianjurkan gRPC karena binary serialization jauh lebih efisien & hemat bandwidth CPU. Untuk client eksternal (mobile app/web), tetap sediakan REST Gateway.',
                        'waktu' => '2026-10-11 10:05'
                    ],
                    [
                        'id' => 'REP02',
                        'user_id' => 'mhs_02',
                        'user_nama' => 'Clarissa Putri Maharani',
                        'user_role' => 'mahasiswa',
                        'isi' => 'Terima kasih penjelasannya Prof! Saya juga sedang mengintegrasikan envoy proxy untuk transcoding gRPC ke web.',
                        'waktu' => '2026-10-11 11:30'
                    ]
                ]
            ],
            [
                'id' => 'DSC02',
                'course_id' => 'MK02',
                'user_id' => 'dos_01',
                'user_nama' => 'Prof. Dr. Ir. Rendy Anggara, S.Kom., M.T.',
                'user_role' => 'dosen',
                'judul' => 'Tips Optimasi Learning Rate & Batch Normalization pada CNN',
                'isi' => 'Halo semuanya, jika ada yang mengalami vanishing gradient saat melatih model citra MRI, silakan coba aktifkan Cosine Annealing learning rate schedule dan periksa transfer learning model dasar (ResNet50 vs EfficientNet).',
                'waktu' => '2026-10-09 16:20',
                'replies' => [
                    [
                        'id' => 'REP03',
                        'user_id' => 'mhs_01',
                        'user_nama' => 'Bintang Arya Nugraha',
                        'user_role' => 'mahasiswa',
                        'isi' => 'Siap prof, setelah mencoba Cosine Annealing akurasi validasi naik dari 84% ke 93.8%. Terima kasih sarannya!',
                        'waktu' => '2026-10-10 08:45'
                    ]
                ]
            ]
        ],
        'announcements' => [
            [
                'id' => 'ANN01',
                'judul' => 'Dies Natalis ke-45 Universitas Angolia & Peringatan #1 Kampus Swasta Riset Unggulan',
                'tanggal' => '12 Oktober 2026',
                'kategori' => 'Prestasi & Kebanggaan',
                'konten' => 'Universitas Angolia berhasil menempati peringkat #1 Universitas Swasta di Indonesia versi THE World University Rankings 2027. Terima kasih kepada seluruh civitas akademika atas inovasi dan dedikasi tiada henti.'
            ],
            [
                'id' => 'ANN02',
                'judul' => 'Pendaftaran Beasiswa Unggulan Prestasi & AI Future Leader 2026/2027',
                'tanggal' => '10 Oktober 2026',
                'kategori' => 'Beasiswa',
                'konten' => 'Dibuka pendaftaran beasiswa penuh 100% biaya kuliah untuk mahasiswa berprestasi dan peneliti muda. Batas pengajuan berkas tanggal 30 November 2026 melalui portal akademik.'
            ]
        ]
    ];
}

// Load Data
function load_data() {
    if (!file_exists(DATA_FILE)) {
        $initial = get_initial_data();
        save_data($initial);
        // Create dummy pdf for testing downloads
        if (!file_exists(UPLOAD_DIR . '/sample_arsitektur.pdf')) {
            file_put_contents(UPLOAD_DIR . '/sample_arsitektur.pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000010 00000 n\n0000000053 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n182\n%%EOF");
        }
        return $initial;
    }
    $raw = file_get_contents(DATA_FILE);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $data = get_initial_data();
        save_data($data);
    }
    return $data;
}

// Save Data
function save_data($data) {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Helper Authentication
function current_user() {
    return $_SESSION['user'] ?? null;
}

function check_auth($allowed_roles = []) {
    $user = current_user();
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    if (!empty($allowed_roles) && !in_array($user['role'], $allowed_roles)) {
        header('Location: index.php?msg=unauthorized');
        exit;
    }
    return $user;
}
