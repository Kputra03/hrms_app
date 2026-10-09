<?php
$page_title = "Form Pegawai";
require_once 'includes/auth_check.php';
check_role('admin');
require_once 'config/database.php';

$id = $_GET['id'] ?? null;
$is_edit = !empty($id);

$nip = ''; $nama_lengkap = ''; $email = ''; $role = 'karyawan';
$jabatan = ''; $departemen = ''; $error = ''; $success = '';

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
    if ($user) {
        $nip = $user['nip']; $nama_lengkap = $user['nama_lengkap'];
        $email = $user['email']; $role = $user['role'];
        $jabatan = $user['jabatan']; $departemen = $user['departemen'];
    } else {
        header('Location: data-pegawai.php'); exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip          = trim($_POST['nip']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email        = trim($_POST['email']);
    $role         = $_POST['role'];
    $jabatan      = trim($_POST['jabatan']);
    $departemen   = trim($_POST['departemen']);
    $password     = $_POST['password'];

    if (empty($nip) || empty($nama_lengkap) || empty($email)) {
        $error = "NIP, Nama Lengkap, dan Email wajib diisi!";
    } else {
        if ($is_edit) {
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET nip=?, nama_lengkap=?, email=?, role=?, jabatan=?, departemen=?, password=? WHERE id=?");
                $stmt->execute([$nip, $nama_lengkap, $email, $role, $jabatan, $departemen, $hashed, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET nip=?, nama_lengkap=?, email=?, role=?, jabatan=?, departemen=? WHERE id=?");
                $stmt->execute([$nip, $nama_lengkap, $email, $role, $jabatan, $departemen, $id]);
            }
            $success = "Data pegawai berhasil diperbarui!";
        } else {
            if (empty($password)) {
                $error = "Kata sandi wajib diisi untuk pengguna baru!";
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                try {
                    $stmt = $pdo->prepare("INSERT INTO users (nip, nama_lengkap, email, password, role, jabatan, departemen) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nip, $nama_lengkap, $email, $hashed, $role, $jabatan, $departemen]);
                    header("Location: data-pegawai.php?msg=added"); exit;
                } catch (PDOException $e) {
                    $error = "Gagal menambah data (NIP atau Email sudah terdaftar)!";
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-2xl mx-auto bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-indigo-400"><?= $is_edit ? 'Edit' : 'Tambah' ?> Data Pegawai</h2>
        <a href="data-pegawai.php" class="text-slate-400 hover:text-white text-sm">&larr; Kembali</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded-lg text-sm mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="bg-green-500/20 border border-green-500 text-green-300 p-3 rounded-lg text-sm mb-4"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">NIP *</label>
                <input type="text" name="nip" value="<?= htmlspecialchars($nip) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Role *</label>
                <select name="role" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
                    <option value="karyawan" <?= $role === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin HR</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm text-slate-400 mb-1">Nama Lengkap *</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($nama_lengkap) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
        </div>

        <div>
            <label class="block text-sm text-slate-400 mb-1">Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Jabatan</label>
                <input type="text" name="jabatan" value="<?= htmlspecialchars($jabatan) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Departemen</label>
                <input type="text" name="departemen" value="<?= htmlspecialchars($departemen) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
            </div>
        </div>

        <div>
            <label class="block text-sm text-slate-400 mb-1">Kata Sandi <?= $is_edit ? '<span class="text-xs text-slate-500">(Kosongkan jika tidak diubah)</span>' : '*' ?></label>
            <input type="password" name="password" <?= $is_edit ? '' : 'required' ?> class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white">
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 font-semibold p-3 rounded-lg transition mt-4">
            <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Pegawai' ?>
        </button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>