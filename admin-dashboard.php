<?php
$page_title = "Admin Dashboard";
require_once 'includes/auth_check.php';
check_role('admin');
require_once 'config/database.php';

// Ambil Statistik Real-time dari Database
$total_karyawan = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'karyawan'")->fetchColumn();
$today = date('Y-m-d');
$total_hadir = $pdo->query("SELECT COUNT(*) FROM kehadiran WHERE tanggal = '$today' AND status = 'Hadir'")->fetchColumn();
$total_cuti_pending = $pdo->query("SELECT COUNT(*) FROM cuti WHERE status = 'Pending'")->fetchColumn();

// Ambil 5 Pengajuan Cuti Terbaru
$stmt_cuti_latest = $pdo->query("SELECT cuti.*, users.nama_lengkap, users.nip FROM cuti JOIN users ON cuti.user_id = users.id ORDER BY cuti.id DESC LIMIT 5");
$cuti_latest = $stmt_cuti_latest->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Ringkasan Eksekutif HR</h1>
            <p class="text-slate-400 text-sm mt-1">Pantau performa kehadiran, status pegawai, dan pengajuan cuti secara real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="form-pegawai.php" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/25 transition">
                <span class="material-symbols-outlined text-lg">person_add</span>
                <span>Tambah Pegawai</span>
            </a>
        </div>
    </div>

    <!-- STATISTIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <!-- Card 1 -->
        <div class="bg-slate-900/60 backdrop-blur-xl p-6 rounded-2xl border border-slate-800/80 flex items-center justify-between shadow-sm hover:border-slate-700 transition">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-3xl font-extrabold text-white mt-2"><?= $total_karyawan ?> <span class="text-sm font-normal text-slate-400">Orang</span></h3>
                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 mt-2 font-medium">
                    <span class="material-symbols-outlined text-xs">trending_up</span> Terdaftar Aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">group</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-slate-900/60 backdrop-blur-xl p-6 rounded-2xl border border-slate-800/80 flex items-center justify-between shadow-sm hover:border-slate-700 transition">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Hadir Hari Ini</p>
                <h3 class="text-3xl font-extrabold text-white mt-2"><?= $total_hadir ?> <span class="text-sm font-normal text-slate-400">Presensi</span></h3>
                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 mt-2 font-medium">
                    <span class="material-symbols-outlined text-xs">check_circle</span> Real-time Log
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-slate-900/60 backdrop-blur-xl p-6 rounded-2xl border border-slate-800/80 flex items-center justify-between shadow-sm hover:border-slate-700 transition">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Cuti Menunggu Persetujuan</p>
                <h3 class="text-3xl font-extrabold text-amber-400 mt-2"><?= $total_cuti_pending ?> <span class="text-sm font-normal text-slate-400">Pengajuan</span></h3>
                <a href="pengajuan-cuti.php" class="inline-flex items-center gap-1 text-[11px] text-indigo-400 hover:underline mt-2 font-medium">
                    Periksa Sekarang &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">pending_actions</span>
            </div>
        </div>
    </div>

    <!-- RECENT LEAVE REQUESTS TABLE -->
    <div class="bg-slate-900/60 backdrop-blur-xl rounded-2xl border border-slate-800/80 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-white">Pengajuan Cuti Terbaru</h2>
                <p class="text-slate-400 text-xs mt-0.5">Daftar permohonan cuti pegawai yang masuk ke sistem.</p>
            </div>
            <a href="pengajuan-cuti.php" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Pegawai</th>
                        <th class="px-6 py-3.5">Jenis Cuti</th>
                        <th class="px-6 py-3.5">Periode</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (count($cuti_latest) > 0): ?>
                        <?php foreach ($cuti_latest as $c): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white"><?= htmlspecialchars($c['nama_lengkap']) ?></div>
                                    <div class="text-xs text-slate-500 font-mono"><?= htmlspecialchars($c['nip']) ?></div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-200"><?= htmlspecialchars($c['jenis_cuti']) ?></td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-400"><?= $c['tanggal_mulai'] ?> s/d <?= $c['tanggal_selesai'] ?></td>
                                <td class="px-6 py-4">
                                    <?php
                                        $badge = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                                        if ($c['status'] === 'Disetujui') $badge = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                        if ($c['status'] === 'Ditolak') $badge = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border <?= $badge ?>">
                                        <?= $c['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="pengajuan-cuti.php" class="text-xs font-medium text-indigo-400 hover:underline">Tinjau</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada pengajuan cuti.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>