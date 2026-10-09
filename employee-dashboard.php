<?php
$page_title = "Employee Dashboard";
require_once 'includes/auth_check.php';
check_role('karyawan');
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$today   = date('Y-m-d');

// Ambil info profil
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt_user->execute(['id' => $user_id]);
$profile = $stmt_user->fetch();

// Status kehadiran hari ini
$stmt_presensi = $pdo->prepare("SELECT * FROM kehadiran WHERE user_id = :uid AND tanggal = :tgl");
$stmt_presensi->execute(['uid' => $user_id, 'tgl' => $today]);
$presensi_today = $stmt_presensi->fetch();

// Total cuti disetujui
$stmt_cuti = $pdo->prepare("SELECT COUNT(*) FROM cuti WHERE user_id = :uid AND status = 'Disetujui'");
$stmt_cuti->execute(['uid' => $user_id]);
$total_cuti_approved = $stmt_cuti->fetchColumn();

// Panggil Header
require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Profil Card -->
    <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white"><?= htmlspecialchars($profile['nama_lengkap']) ?></h2>
            <p class="text-slate-400 text-sm mt-1">NIP: <span class="text-indigo-400 font-mono"><?= htmlspecialchars($profile['nip']) ?></span> | Jabatan: <b><?= htmlspecialchars($profile['jabatan'] ?? 'Staff') ?></b> (<?= htmlspecialchars($profile['departemen'] ?? 'Umum') ?>)</p>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-300 border border-green-500/30 uppercase">
                Status: Aktif
            </span>
        </div>
    </div>

    <!-- Ringkasan Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 space-y-2">
            <p class="text-slate-400 text-sm">Status Presensi Hari Ini</p>
            <div class="text-lg font-bold">
                <?php if ($presensi_today): ?>
                    <span class="text-green-400">Sudah Absen Masuk (<?= htmlspecialchars($presensi_today['jam_masuk']) ?>)</span>
                <?php else: ?>
                    <span class="text-amber-400">Belum Melakukan Absensi</span>
                <?php endif; ?>
            </div>
            <a href="kehadiran.php" class="inline-block text-xs text-indigo-400 hover:underline mt-2">&rarr; Ke Halaman Presensi</a>
        </div>

        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 space-y-2">
            <p class="text-slate-400 text-sm">Total Pengajuan Cuti Disetujui</p>
            <div class="text-3xl font-bold text-indigo-400"><?= $total_cuti_approved ?> Kali</div>
            <a href="pengajuan-cuti.php" class="inline-block text-xs text-indigo-400 hover:underline mt-2">&rarr; Ajukan Cuti Baru</a>
        </div>
    </div>

    <!-- Akses Cepat -->
    <div class="flex gap-4">
        <a href="kehadiran.php" class="bg-indigo-600 hover:bg-indigo-500 px-5 py-3 rounded-xl font-semibold transition">
            Presensi Kehadiran
        </a>
        <a href="pengajuan-cuti.php" class="bg-slate-800 border border-slate-700 hover:bg-slate-700 px-5 py-3 rounded-xl font-semibold transition">
            Pengajuan Cuti
        </a>
    </div>
</div>

<?php
// Panggil Footer
require_once 'includes/footer.php';
?>