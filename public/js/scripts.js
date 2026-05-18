document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const toggle = document.getElementById("menuToggle");

    if (toggle && sidebar && overlay) {
        function openSidebar() {
            sidebar.classList.add("open");
            overlay.style.display = "block";

            setTimeout(() => {
                overlay.classList.add("active");
            }, 10);

            document.body.style.overflow = "hidden";
        }

        function closeSidebar() {
            sidebar.classList.remove("open");
            overlay.classList.remove("active");
            document.body.style.overflow = "";

            setTimeout(() => {
                if (!overlay.classList.contains("active")) {
                    overlay.style.display = "none";
                }
            }, 350);
        }

        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            sidebar.classList.contains("open") ? closeSidebar() : openSidebar();
        });

        overlay.addEventListener("click", closeSidebar);

        document.querySelectorAll(".nav-item").forEach((item) => {
            item.addEventListener("click", function (e) {
                document
                    .querySelectorAll(".nav-item")
                    .forEach((i) => i.classList.remove("active"));
                this.classList.add("active");

                if (window.innerWidth <= 860) {
                    closeSidebar();
                }
            });
        });
    }

    const searchInput = document.querySelector(".search-input");
    if (searchInput) {
        searchInput.addEventListener("focus", () => {
            searchInput.parentElement.style.transform = "scale(1.02)";
        });
        searchInput.addEventListener("blur", () => {
            searchInput.parentElement.style.transform = "";
        });
    }

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
    const trigger = e.target.closest("[data-dropdown-trigger]");

    if (!trigger && !e.target.closest("[data-dropdown-menu]")) {
        document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
            m.style.display = "none";
        });
        return;
    }

    if (trigger) {
        e.preventDefault();
        e.stopPropagation();

        const container = trigger.closest("[data-dropdown]");
        let menu = container.querySelector("[data-dropdown-menu]");

        if (menu) {
            document.body.appendChild(menu);

            const uniqueId = "menu-" + Math.random().toString(36).substr(2, 9);
            menu.id = uniqueId;
            container.setAttribute("data-target-menu", uniqueId);
        } else {
            const targetId = container.getAttribute("data-target-menu");
            menu = document.getElementById(targetId);
        }

        if (menu) {
            const isHidden =
                menu.style.display === "none" || menu.style.display === "";

            document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
                m.style.display = "none";
            });

            if (isHidden) {
                menu.style.display = "block";
                menu.style.position = "fixed";
                menu.style.zIndex = "999999";

                const rect = trigger.getBoundingClientRect();
                menu.style.top = rect.bottom + 4 + "px";

                const menuWidth = menu.offsetWidth || 180;
                menu.style.left = rect.right - menuWidth + "px";

                if (rect.bottom + menu.offsetHeight > window.innerHeight) {
                    menu.style.top = rect.top - menu.offsetHeight - 4 + "px";
                }
            }
        }
    }
});

document.addEventListener(
    "scroll",
    function (e) {
        if (!e.target.closest("[data-dropdown-menu]")) {
            document.querySelectorAll("[data-dropdown-menu]").forEach((m) => {
                m.style.display = "none";
            });
        }

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

    if (offcanvas && offcanvas.classList.contains("show")) {
        offcanvas.classList.remove("show");
        if (backdrop) backdrop.classList.remove("show");

        document.body.style.removeProperty("overflow");
        document.body.style.overflow = "auto";
    } else if (offcanvas) {
        offcanvas.classList.add("show");
        if (backdrop) backdrop.classList.add("show");

        document.body.style.overflow = "hidden";
    }
}
