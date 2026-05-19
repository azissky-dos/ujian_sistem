<?php
// includes/footer.php
?>
        </div>
    </main>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
<button class="menu-toggle" id="menuToggle" style="display:none;">
    <i class="fas fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
function checkMobile() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (window.innerWidth <= 768) {
        if (menuToggle) menuToggle.style.display = 'block';
    } else {
        if (menuToggle) menuToggle.style.display = 'none';
        if (sidebar) sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
    }
}

const menuBtn = document.getElementById('menuToggle');
const overlay = document.getElementById('sidebarOverlay');
const sidebar = document.querySelector('.sidebar');

if (menuBtn) {
    menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (sidebar) sidebar.classList.toggle('show');
        if (overlay) overlay.classList.toggle('show');
    });
}

if (overlay) {
    overlay.addEventListener('click', function() {
        if (sidebar) sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
    });
}

// Tutup sidebar saat klik di luar area sidebar
document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768) {
        if (sidebar && !sidebar.contains(e.target) && menuBtn && !menuBtn.contains(e.target)) {
            sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        }
    }
});

window.addEventListener('load', checkMobile);
window.addEventListener('resize', checkMobile);
</script>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>