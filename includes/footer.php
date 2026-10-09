</main> <!-- End Main -->

        <!-- FOOTER BAWAH -->
        <footer class="border-t border-slate-800/80 py-4 px-8 text-center text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p>&copy; <?= date('Y') ?> HRMS Enterprise Platform. All rights reserved.</p>
            <p class="font-mono text-[11px] text-slate-600">v2.4.0 • PHP Native & MySQL</p>
        </footer>
    </div> <!-- End Content Wrapper -->

    <!-- SCRIPT UTILITY -->
    <script>
        // Toggle Sidebar Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Realtime Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) clockEl.innerText = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>