<?php
$page_title = "Kehadiran";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$today   = date('Y-m-d');
$msg = ''; $error = '';

if ($role === 'karyawan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $now_time = date('H:i:s');

    $stmt_check = $pdo->prepare("SELECT * FROM kehadiran WHERE user_id = :uid AND tanggal = :tgl");
    $stmt_check->execute(['uid' => $user_id, 'tgl' => $today]);
    $presensi_today = $stmt_check->fetch();

    if ($action === 'masuk') {
        if (!$presensi_today) {
            $stmt_in = $pdo->prepare("INSERT INTO kehadiran (user_id, tanggal, jam_masuk, status) VALUES (?, ?, ?, 'Hadir')");
            $stmt_in->execute([$user_id, $today, $now_time]);
            $msg = "Berhasil Absen Masuk pukul $now_time";
        } else {
            $error = "Anda sudah melakukan absen masuk hari ini!";
        }
    } elseif ($action === 'keluar') {
        if ($presensi_today && empty($presensi_today['jam_keluar'])) {
            $stmt_out = $pdo->prepare("UPDATE kehadiran SET jam_keluar = :jam WHERE id = :id");
            $stmt_out->execute(['jam' => $now_time, 'id' => $presensi_today['id']]);
            $msg = "Berhasil Absen Keluar pukul $now_time";
        } else {
            $error = "Gagal melakukan absen keluar!";
        }
    }
}

$presensi_hari_ini = null;
if ($role === 'karyawan') {
    $stmt_curr = $pdo->prepare("SELECT * FROM kehadiran WHERE user_id = :uid AND tanggal = :tgl");
    $stmt_curr->execute(['uid' => $user_id, 'tgl' => $today]);
    $presensi_hari_ini = $stmt_curr->fetch();
}

$filter_tgl = $_GET['tanggal'] ?? $today;
if ($role === 'admin') {
    $stmt_list = $pdo->prepare("SELECT kehadiran.*, users.nama_lengkap, users.nip FROM kehadiran JOIN users ON kehadiran.user_id = users.id WHERE kehadiran.tanggal = :tgl ORDER BY kehadiran.id DESC");
    $stmt_list->execute(['tgl' => $filter_tgl]);
} else {
    $stmt_list = $pdo->prepare("SELECT * FROM kehadiran WHERE user_id = :uid ORDER BY tanggal DESC LIMIT 30");
    $stmt_list->execute(['uid' => $user_id]);
}
$kehadiran_list = $stmt_list->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-white">Presensi & Kehadiran</h1>

    <?php if (!empty($msg)): ?><div class="bg-green-500/20 border border-green-500 text-green-300 p-3 rounded-lg text-sm"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded-lg text-sm"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($role === 'karyawan'): ?>
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold text-slate-200">Presensi Hari Ini (<?= date('d F Y') ?>)</h2>
                <div class="mt-2 text-sm space-x-4">
                    <span>Jam Masuk: <b class="text-green-400"><?= $presensi_hari_ini['jam_masuk'] ?? '--:--:--' ?></b></span>
                    <span>Jam Keluar: <b class="text-amber-400"><?= $presensi_hari_ini['jam_keluar'] ?? '--:--:--' ?></b></span>
                </div>
            </div>
            <form method="POST" action="" class="flex gap-4">
                <button type="submit" name="action" value="masuk" <?= ($presensi_hari_ini && !empty($presensi_hari_ini['jam_masuk'])) ? 'disabled' : '' ?> class="bg-green-600 hover:bg-green-500 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3 rounded-xl font-bold transition">Absen Masuk</button>
                <button type="submit" name="action" value="keluar" <?= (!$presensi_hari_ini || !empty($presensi_hari_ini['jam_keluar'])) ? 'disabled' : '' ?> class="bg-amber-600 hover:bg-amber-500 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3 rounded-xl font-bold transition">Absen Keluar</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <form method="GET" action="" class="flex items-center gap-4 bg-slate-800 p-4 rounded-xl border border-slate-700">
            <label class="text-sm text-slate-300 font-medium">Filter Tanggal:</label>
            <input type="date" name="tanggal" value="<?= htmlspecialchars($filter_tgl) ?>" class="bg-slate-900 border border-slate-700 rounded-lg p-2 text-white">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-lg font-medium">Tampilkan</button>
        </form>
    <?php endif; ?>

    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-700/50 text-slate-200 uppercase text-xs">
                <tr>
                    <th class="p-4"><?= $role === 'admin' ? 'Pegawai' : 'Tanggal' ?></th>
                    <th class="p-4">Jam Masuk</th>
                    <th class="p-4">Jam Keluar</th>
                    <th class="p-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                <?php if (count($kehadiran_list) > 0): ?>
                    <?php foreach ($kehadiran_list as $k): ?>
                        <tr class="hover:bg-slate-700/30">
                            <td class="p-4 font-semibold text-white"><?= $role === 'admin' ? htmlspecialchars($k['nama_lengkap']) : htmlspecialchars($k['tanggal']) ?></td>
                            <td class="p-4 font-mono text-green-400"><?= htmlspecialchars($k['jam_masuk'] ?? '-') ?></td>
                            <td class="p-4 font-mono text-amber-400"><?= htmlspecialchars($k['jam_keluar'] ?? '-') ?></td>
                            <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30"><?= htmlspecialchars($k['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="p-6 text-center text-slate-500">Tidak ada rekam kehadiran.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>