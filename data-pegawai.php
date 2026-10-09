<?php
$page_title = "Data Pegawai";
require_once 'includes/auth_check.php';
check_role('admin');
require_once 'config/database.php';

// Proses Hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    if ($id_to_delete !== $_SESSION['user_id']) {
        $stmt_del = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt_del->execute(['id' => $id_to_delete]);
        header("Location: data-pegawai.php?msg=deleted");
        exit;
    }
}

// Search
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (nama_lengkap LIKE :q OR nip LIKE :q OR email LIKE :q) ORDER BY id DESC");
    $stmt->execute(['q' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
}
$pegawai_list = $stmt->fetchAll();

// Panggil Header
require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h1 class="text-2xl font-bold text-white">Kelola Data Pegawai</h1>
        <a href="form-pegawai.php" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium transition">+ Tambah Pegawai</a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="" class="flex gap-4">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari NIP, Nama, atau Email..." class="w-full max-w-md bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:border-indigo-500">
        <button type="submit" class="bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg font-medium">Cari</button>
        <?php if (!empty($search)): ?>
            <a href="data-pegawai.php" class="bg-slate-800 border border-slate-700 hover:bg-slate-700 px-4 py-2 rounded-lg text-slate-400">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Tabel -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-700/50 text-slate-200 uppercase text-xs">
                <tr>
                    <th class="p-4">NIP</th>
                    <th class="p-4">Nama Lengkap</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Jabatan</th>
                    <th class="p-4">Role</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                <?php if (count($pegawai_list) > 0): ?>
                    <?php foreach ($pegawai_list as $pegawai): ?>
                        <tr class="hover:bg-slate-700/30">
                            <td class="p-4 font-mono text-indigo-400"><?= htmlspecialchars($pegawai['nip']) ?></td>
                            <td class="p-4 font-semibold text-white"><?= htmlspecialchars($pegawai['nama_lengkap']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($pegawai['email']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($pegawai['jabatan'] ?? '-') ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $pegawai['role'] === 'admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' ?>">
                                    <?= strtoupper($pegawai['role']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <a href="form-pegawai.php?id=<?= $pegawai['id'] ?>" class="text-amber-400 hover:underline">Edit</a>
                                <?php if ($pegawai['id'] !== $_SESSION['user_id']): ?>
                                    <a href="data-pegawai.php?action=delete&id=<?= $pegawai['id'] ?>" onclick="return confirm('Yakin ingin menghapus pegawai ini?');" class="text-red-400 hover:underline">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-500">Data pegawai tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>