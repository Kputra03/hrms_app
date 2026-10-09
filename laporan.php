<?php
$page_title = "Laporan HRMS";
require_once 'includes/auth_check.php';
check_role('admin');
require_once 'config/database.php';

$total_karyawan = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'karyawan'")->fetchColumn();

$bulan_ini = date('Y-m');
$stmt_cuti = $pdo->prepare("SELECT COUNT(*) FROM cuti WHERE status = 'Disetujui' AND DATE_FORMAT(created_at, '%Y-%m') = :bln");
$stmt_cuti->execute(['bln' => $bulan_ini]);
$cuti_bulan_ini = $stmt_cuti->fetchColumn();

$today = date('Y-m-d');
$stmt_hadir = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE tanggal = :tgl");
$stmt_hadir->execute(['tgl' => $today]);
$hadir_hari_ini = $stmt_hadir->fetchColumn();

require_once 'includes/header.php';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Laporan & Rekapitulasi Sistem</h1>
            <p class="text-slate-400 text-sm">Ringkasan statistik kepegawaian</p>
        </div>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-lg text-sm font-semibold transition">Cetak Laporan</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
            <p class="text-slate-400 text-xs uppercase font-semibold">Total Karyawan Aktif</p>
            <p class="text-3xl font-bold text-indigo-400 mt-2"><?= $total_karyawan ?> Orang</p>
        </div>
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
            <p class="text-slate-400 text-xs uppercase font-semibold">Kehadiran Hari Ini</p>
            <p class="text-3xl font-bold text-green-400 mt-2"><?= $hadir_hari_ini ?> Orang</p>
        </div>
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
            <p class="text-slate-400 text-xs uppercase font-semibold">Cuti Disetujui (Bulan Ini)</p>
            <p class="text-3xl font-bold text-amber-400 mt-2"><?= $cuti_bulan_ini ?> Pengajuan</p>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>