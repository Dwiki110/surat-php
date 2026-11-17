        </div>
    </div>
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="toast" id="toast"><?= h($_SESSION['flash']) ?></div>
        <script>setTimeout(function(){ var t=document.getElementById('toast'); if(t) t.style.display='none'; }, 3200);</script>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
</div>
<script>
(function () {
    var sidebar = document.getElementById('sidebarNav');
    var overlay = document.getElementById('sidebarOverlay');
    var openBtn = document.getElementById('hamburgerBtn');
    var closeBtn = document.getElementById('sidebarCloseBtn');
    function openSidebar() { sidebar.classList.add('open'); overlay.classList.add('open'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }
    if (openBtn) openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
})();
</script>
</body>
</html>

