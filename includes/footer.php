<?php
// includes/footer.php
?>
        </div>
    </main>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
<!-- Tombol Hamburger Menu (untuk mobile) -->
<button class="menu-toggle" id="menuToggle" style="display:none;">
    <i class="fas fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
function checkMobile() {
    if (window.innerWidth <= 768) {
        document.getElementById('menuToggle').style.display = 'block';
    } else {
        document.getElementById('menuToggle').style.display = 'none';
        document.querySelector('.sidebar')?.classList.remove('show');
        document.getElementById('sidebarOverlay')?.classList.remove('show');
    }
}

const menuBtn = document.getElementById('menuToggle');
const overlay = document.getElementById('sidebarOverlay');

if (menuBtn) {
    menuBtn.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
        overlay.classList.toggle('show');
    });
}

if (overlay) {
    overlay.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.remove('show');
        this.classList.remove('show');
    });
}

window.addEventListener('load', checkMobile);
window.addEventListener('resize', checkMobile);
</script>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>