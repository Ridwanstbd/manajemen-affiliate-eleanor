document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const toggle = document.getElementById("menuToggle");

    // Pastikan semua elemen ada sebelum menambahkan event listener
    if (toggle && sidebar && overlay) {
        function openSidebar() {
            sidebar.classList.add("open");
            overlay.style.display = "block";

            // Beri sedikit jeda (10ms) agar transisi CSS 'opacity' punya waktu untuk mendeteksi perubahan 'display'
            setTimeout(() => {
                overlay.classList.add("active");
            }, 10);

            document.body.style.overflow = "hidden";
        }

        function closeSidebar() {
            sidebar.classList.remove("open");
            overlay.classList.remove("active");
            document.body.style.overflow = "";

            // Tunggu transisi selesai (350ms) baru sembunyikan sepenuhnya
            setTimeout(() => {
                if (!overlay.classList.contains("active")) {
                    overlay.style.display = "none";
                }
            }, 350);
        }

        // Event listener untuk tombol hamburger
        toggle.addEventListener("click", function (e) {
            e.preventDefault(); // Mencegah aksi default tombol
            sidebar.classList.contains("open") ? closeSidebar() : openSidebar();
        });

        // Tutup sidebar jika overlay gelap diklik
        overlay.addEventListener("click", closeSidebar);

        // Nav active state & Auto-close di mobile
        document.querySelectorAll(".nav-item").forEach((item) => {
            item.addEventListener("click", function (e) {
                document
                    .querySelectorAll(".nav-item")
                    .forEach((i) => i.classList.remove("active"));
                this.classList.add("active");

                // Tutup otomatis di layar mobile setelah menu diklik
                if (window.innerWidth <= 860) {
                    closeSidebar();
                }
            });
        });
    }

    // Animasi Search Input
    const searchInput = document.querySelector(".search-input");
    if (searchInput) {
        searchInput.addEventListener("focus", () => {
            searchInput.parentElement.style.transform = "scale(1.02)";
        });
        searchInput.addEventListener("blur", () => {
            searchInput.parentElement.style.transform = "";
        });
    }

    // Reset state saat layar dibesarkan ke mode desktop
    window.addEventListener("resize", () => {
        if (window.innerWidth > 860 && sidebar) {
            sidebar.classList.remove("open");
            if (overlay) {
                overlay.classList.remove("active");
                overlay.style.display = "none";
            }
            document.body.style.overflow = "";
        }
    });
});
document.addEventListener("click", function (e) {
    const isDropdownTrigger = e.target.closest("[data-dropdown-trigger]");

    if (isDropdownTrigger) {
        const dropdown = isDropdownTrigger.closest("[data-dropdown]");
        dropdown.classList.toggle("open");
    }

    document.querySelectorAll("[data-dropdown].open").forEach((dropdown) => {
        if (dropdown !== e.target.closest("[data-dropdown]")) {
            dropdown.classList.remove("open");
        }
    });
});
