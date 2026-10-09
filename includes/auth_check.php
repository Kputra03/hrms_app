<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

/**
 * Fungsi untuk memverifikasi role pengguna
 * @param string $allowed_role 'admin' atau 'karyawan'
 */
function check_role($allowed_role) {
    if ($_SESSION['role'] !== $allowed_role) {
        // Arahkan ke dashboard masing-masing jika role tidak cocok
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin-dashboard.php');
        } else {
            header('Location: employee-dashboard.php');
        }
        exit;
    }
}
?>