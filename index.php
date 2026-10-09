<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin-dashboard.php');
    } else {
        header('Location: employee-dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password   = trim($_POST['password']);

    if (!empty($identifier) && !empty($password)) {
        // Cari pengguna berdasarkan email atau NIP
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :id OR nip = :id LIMIT 1");
        $stmt->execute(['id' => $identifier]);
        $user = $stmt->fetch();

        // Cek password polos ATAU password hash
        if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nip']          = $user['nip'];
            $_SESSION['nama_lengkap']  = $user['nama_lengkap'];
            $_SESSION['role']         = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin-dashboard.php');
            } else {
                header('Location: employee-dashboard.php');
            }
            exit;
        } else {
            $error = 'NIP/Email atau Password tidak cocok!';
        }
    } else {
        $error = 'Silakan isi seluruh kolom!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRMS Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-indigo-400">HRMS Portal</h1>
            <p class="text-slate-400 text-sm mt-1">Sistem Informasi Manajemen Sumber Daya Manusia</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded-lg text-sm mb-6 text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIP atau Alamat Email</label>
                <input type="text" name="identifier" required placeholder="Masukkan NIP atau Email..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kata Sandi</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold p-3 rounded-xl transition duration-200 shadow-lg shadow-indigo-600/30">
                Masuk ke System
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-700/60 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> HRMS Application. All rights reserved.
        </div>
    </div>

</body>
</html>