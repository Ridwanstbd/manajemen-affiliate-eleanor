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
// ==========================================
// LOGIKA DROPDOWN DINAMIS (TELEPORT TO BODY FIX)
// ==========================================
document.addEventListener("click", function (e) {
    const trigger = e.target.closest("[data-dropdown-trigger]");

    // 1. Jika area kosong diklik, tutup semua menu yang melayang di body
    if (!trigger && !e.target.closest("[data-dropdown-menu]")) {
        document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
            m.style.display = "none";
        });
        return;
    }

    // 2. Jika tombol trigger diklik
    if (trigger) {
        e.preventDefault();
        e.stopPropagation();

        const container = trigger.closest("[data-dropdown]");
        let menu = container.querySelector("[data-dropdown-menu]");

        // TELEPORTASI: Pindahkan menu ke <body> agar terbebas dari efek blur & batas tabel
        if (menu) {
            document.body.appendChild(menu);

            // Beri ID unik agar tombol tahu mana menu miliknya
            const uniqueId = "menu-" + Math.random().toString(36).substr(2, 9);
            menu.id = uniqueId;
            container.setAttribute("data-target-menu", uniqueId);
        } else {
            // Jika sudah pernah dipindah sebelumnya, cari berdasarkan ID-nya
            const targetId = container.getAttribute("data-target-menu");
            menu = document.getElementById(targetId);
        }

        if (menu) {
            const isHidden =
                menu.style.display === "none" || menu.style.display === "";

            // Tutup semua menu lain yang terbuka sebelum menampilkan yang ini
            document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
                m.style.display = "none";
            });

            // Tampilkan menu yang diklik
            if (isHidden) {
                menu.style.display = "block";
                menu.style.position = "fixed";
                menu.style.zIndex = "999999"; // Pastikan berada paling atas

                // Kalkulasi posisi kordinat asli dari tombol
                const rect = trigger.getBoundingClientRect();
                menu.style.top = rect.bottom + 4 + "px";

                // Rata kanan dengan tombol
                const menuWidth = menu.offsetWidth || 180;
                menu.style.left = rect.right - menuWidth + "px";

                // Pencegahan jika menu terpotong di bagian bawah layar browser
                if (rect.bottom + menu.offsetHeight > window.innerHeight) {
                    menu.style.top = rect.top - menu.offsetHeight - 4 + "px";
                }
            }
        }
    }
});

// 3. Wajib: Tutup menu saat halaman atau DataTables di-scroll
document.addEventListener(
    "scroll",
    function (e) {
        if (!e.target.closest("[data-dropdown-menu]")) {
            document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
                m.style.display = "none";
            });
        }

        // Ekstra: Bersihkan "sampah" menu jika baris DataTables berganti halaman
        document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
            if (
                m.id &&
                !document.querySelector(`[data-target-menu="${m.id}"]`)
            ) {
                m.remove();
            }
        });
    },
    true,
);
function toggleOffcanvas(id) {
    const offcanvas = document.getElementById(id);
    const backdrop = document.getElementById(id + "-backdrop");

    if (offcanvas.classList.contains("show")) {
        offcanvas.classList.remove("show");
        backdrop.classList.remove("show");
    } else {
        offcanvas.classList.add("show");
        backdrop.classList.add("show");
    }
}
