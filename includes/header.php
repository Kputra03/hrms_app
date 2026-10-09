<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mencegah error jika variabel belum terdefinisi
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = $page_title ?? 'HRMS Enterprise';
$role = $_SESSION['role'] ?? 'karyawan';
$nama_user = $_SESSION['nama_lengkap'] ?? 'User';
$nip_user = $_SESSION['nip'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - HRMS Enterprise</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Material Symbols & Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex antialiased selection:bg-indigo-500 selection:text-white">

    <!-- SIDEBAR NAVIGASI KIRI -->
    <aside id="sidebar" class="w-64 bg-slate-900/95 border-r border-slate-800 flex flex-col fixed inset-y-0 left-0 z-40 transition-transform duration-300 md:translate-x-0 -translate-x-full backdrop-blur-xl">
        <!-- Logo Brand -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800/80 justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 font-extrabold text-xl">
                    H
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-wide text-white leading-none">HRMS <span class="text-indigo-400 font-normal">Pro</span></h1>
                    <span class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold">Enterprise Edition</span>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Menu Navigasi -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-1.5">
            <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</div>

            <?php if ($role === 'admin'): ?>
                <a href="admin-dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'admin-dashboard.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
                </a>
                <a href="data-pegawai.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= in_array($current_page, ['data-pegawai.php', 'form-pegawai.php']) ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">group</span> Data Pegawai
                </a>
                <a href="kehadiran.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'kehadiran.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">fact_check</span> Log Kehadiran
                </a>
                <a href="pengajuan-cuti.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'pengajuan-cuti.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">event_note</span> Persetujuan Cuti
                </a>
                <a href="laporan.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'laporan.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">analytics</span> Laporan Audit
                </a>
            <?php else: ?>
                <a href="employee-dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'employee-dashboard.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">grid_view</span> ESS Portal
                </a>
                <a href="kehadiran.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'kehadiran.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">timer</span> Presensi Saya
                </a>
                <a href="pengajuan-cuti.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition <?= $current_page === 'pengajuan-cuti.php' ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' ?>">
                    <span class="material-symbols-outlined text-xl">time_to_leave</span> Permohonan Cuti
                </a>
            <?php endif; ?>
        </div>

        <!-- Footer Sidebar (User Badge) -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-900/50">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-800/40 border border-slate-700/50">
                <div class="w-9 h-9 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-sm border border-indigo-500/30">
                    <?= strtoupper(substr($nama_user, 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-[10px] text-slate-400 font-mono truncate"><?= htmlspecialchars($nip_user) ?></p>
                </div>
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="text-slate-400 hover:text-rose-400 transition" title="Keluar">
                    <span class="material-symbols-outlined text-lg">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- WRAPPER KONTEN UTAMA -->
    <div class="flex-1 md:ml-64 flex flex-col min-w-0">

        <!-- HEADER ATAS KUSTOM -->
        <header class="h-16 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-30 px-4 md:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
                <div class="hidden sm:flex items-center gap-2 text-slate-400 text-xs">
                    <span class="material-symbols-outlined text-sm text-indigo-400">schedule</span>
                    <span id="realtime-clock" class="font-mono text-slate-300">00:00:00 WIB</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $role === 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' ?>">
                    <?= $role ?>
                </span>

                <div class="h-4 w-[1px] bg-slate-800"></div>

                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center gap-1.5 text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 px-3 py-1.5 rounded-lg transition">
                    <span class="material-symbols-outlined text-sm">power_settings_new</span>
                    <span class="hidden sm:inline">Logout</span>
                </a>
            </div>
        </header>

        <!-- AREA KONTEN -->
        <main class="p-4 md:p-8 flex-1">