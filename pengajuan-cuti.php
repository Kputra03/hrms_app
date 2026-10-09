<?php
$page_title = "Pengajuan Cuti";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$msg     = '';

if ($role === 'admin' && isset($_GET['action']) && isset($_GET['id'])) {
    $cuti_id = intval($_GET['id']);
    $status  = $_GET['action'] === 'approve' ? 'Disetujui' : ($_GET['action'] === 'reject' ? 'Ditolak' : 'Pending');
    $stmt_acc = $pdo->prepare("UPDATE cuti SET status = :status WHERE id = :id");
    $stmt_acc->execute(['status' => $status, 'id' => $cuti_id]);
    header("Location: pengajuan-cuti.php"); exit;
}

if ($role === 'karyawan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_cuti      = $_POST['jenis_cuti'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $alasan          = trim($_POST['alasan']);

    if (!empty($jenis_cuti) && !empty($tanggal_mulai) && !empty($tanggal_selesai) && !empty($alasan)) {
        $stmt_ins = $pdo->prepare("INSERT INTO cuti (user_id, jenis_cuti, tanggal_mulai, tanggal_selesai, alasan) VALUES (?, ?, ?, ?, ?)");
        $stmt_ins->execute([$user_id, $jenis_cuti, $tanggal_mulai, $tanggal_selesai, $alasan]);
        $msg = "Pengajuan cuti berhasil dikirim!";
    }
}

if ($role === 'admin') {
    $stmt_list = $pdo->query("SELECT cuti.*, users.nama_lengkap, users.nip FROM cuti JOIN users ON cuti.user_id = users.id ORDER BY cuti.id DESC");
} else {
    $stmt_list = $pdo->prepare("SELECT * FROM cuti WHERE user_id = :uid ORDER BY id DESC");
    $stmt_list->execute(['uid' => $user_id]);
}
$cuti_list = $stmt_list->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-white">Manajemen Pengajuan Cuti</h1>

    <?php if (!empty($msg)): ?><div class="bg-green-500/20 border border-green-500 text-green-300 p-3 rounded-lg text-sm"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <?php if ($role === 'karyawan'): ?>
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
            <h2 class="text-lg font-semibold mb-4 text-slate-200">Form Pengajuan Cuti Baru</h2>
            <form method="POST" action="" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Jenis Cuti</label>
                        <select name="jenis_cuti" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
                            <option value="Cuti Tahunan">Cuti Tahunan</option>
                            <option value="Cuti Sakit">Cuti Sakit</option>
                            <option value="Cuti Alasan Penting">Cuti Alasan Penting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Alasan</label>
                    <textarea name="alasan" rows="3" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white" placeholder="Alasan pengajuan cuti..."></textarea>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-6 py-2.5 rounded-lg font-semibold transition">Kirim Pengajuan</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-700/50 text-slate-200 uppercase text-xs">
                <tr>
                    <?php if ($role === 'admin'): ?><th class="p-4">Pegawai</th><?php endif; ?>
                    <th class="p-4">Jenis Cuti</th>
                    <th class="p-4">Tanggal Mulai</th>
                    <th class="p-4">Tanggal Selesai</th>
                    <th class="p-4">Alasan</th>
                    <th class="p-4">Status</th>
                    <?php if ($role === 'admin'): ?><th class="p-4 text-center">Aksi Admin</th><?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                <?php if (count($cuti_list) > 0): ?>
                    <?php foreach ($cuti_list as $c): ?>
                        <tr class="hover:bg-slate-700/30">
                            <?php if ($role === 'admin'): ?><td class="p-4 font-semibold text-white"><?= htmlspecialchars($c['nama_lengkap']) ?></td><?php endif; ?>
                            <td class="p-4 font-medium text-white"><?= htmlspecialchars($c['jenis_cuti']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($c['tanggal_mulai']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($c['tanggal_selesai']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($c['alasan']) ?></td>
                            <td class="p-4">
                                <?php
                                    $badge = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
                                    if ($c['status'] === 'Disetujui') $badge = 'bg-green-500/20 text-green-300 border-green-500/30';
                                    if ($c['status'] === 'Ditolak') $badge = 'bg-red-500/20 text-red-300 border-red-500/30';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold border <?= $badge ?>"><?= $c['status'] ?></span>
                            </td>
                            <?php if ($role === 'admin'): ?>
                                <td class="p-4 text-center space-x-2">
                                    <?php if ($c['status'] === 'Pending'): ?>
                                        <a href="pengajuan-cuti.php?action=approve&id=<?= $c['id'] ?>" class="text-green-400 hover:underline">Setujui</a>
                                        <a href="pengajuan-cuti.php?action=reject&id=<?= $c['id'] ?>" class="text-red-400 hover:underline">Tolak</a>
                                    <?php else: ?>
                                        <span class="text-slate-500">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $role === 'admin' ? 7 : 5 ?>" class="p-6 text-center text-slate-500">Belum ada pengajuan cuti.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>