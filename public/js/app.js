/* ==========================================================================
   Student Grading Management System — Frontend Interactions
   Pure JavaScript. No backend calls anywhere in this file.
   ========================================================================== */

(function () {
    "use strict";

    /* ---------------------------------------------------------------------
     * Icons
     * ------------------------------------------------------------------- */
    function renderIcons() {
        if (window.lucide) window.lucide.createIcons();
    }

    /* ---------------------------------------------------------------------
     * Dark mode
     * ------------------------------------------------------------------- */
    function initDarkMode() {
        const root = document.documentElement;
        const stored = localStorage.getItem("sg_theme");
        if (stored === "dark") root.classList.add("dark");

        document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
            btn.addEventListener("click", () => {
                root.classList.toggle("dark");
                localStorage.setItem(
                    "sg_theme",
                    root.classList.contains("dark") ? "dark" : "light",
                );
            });
        });
    }

    /* ---------------------------------------------------------------------
     * Sidebar (desktop collapse + mobile off-canvas)
     * ------------------------------------------------------------------- */
    function initSidebar() {
        const sidebar = document.getElementById("sg-sidebar");
        const backdrop = document.getElementById("sg-sidebar-backdrop");
        const openBtn = document.querySelectorAll("[data-sidebar-open]");
        const closeBtn = document.querySelectorAll("[data-sidebar-close]");

        openBtn.forEach((b) =>
            b.addEventListener("click", () => {
                sidebar?.classList.remove("-translate-x-full");
                backdrop?.classList.remove("hidden");
            }),
        );
        closeBtn.forEach((b) =>
            b.addEventListener("click", closeMobileSidebar),
        );
        backdrop?.addEventListener("click", closeMobileSidebar);

        function closeMobileSidebar() {
            sidebar?.classList.add("-translate-x-full");
            backdrop?.classList.add("hidden");
        }

        // Desktop collapse (icon-only) toggle
        const collapseBtn = document.querySelector("[data-sidebar-collapse]");
        const shell = document.getElementById("sg-app-shell");
        const collapsed = localStorage.getItem("sg_sidebar_collapsed") === "1";
        if (collapsed) shell?.classList.add("sidebar-collapsed");
        collapseBtn?.addEventListener("click", () => {
            shell?.classList.toggle("sidebar-collapsed");
            localStorage.setItem(
                "sg_sidebar_collapsed",
                shell?.classList.contains("sidebar-collapsed") ? "1" : "0",
            );
        });
    }

    /* ---------------------------------------------------------------------
     * Dropdown menus (user menu, filters, action menus)
     * ------------------------------------------------------------------- */
    function initDropdowns() {
        document.addEventListener("click", (e) => {
            const trigger = e.target.closest("[data-dropdown-toggle]");
            document
                .querySelectorAll("[data-dropdown-menu]")
                .forEach((menu) => {
                    const id = menu.getAttribute("data-dropdown-menu");
                    if (
                        trigger &&
                        trigger.getAttribute("data-dropdown-toggle") === id
                    ) {
                        menu.classList.toggle("hidden");
                    } else if (!menu.contains(e.target)) {
                        menu.classList.add("hidden");
                    }
                });
        });
    }

    /* ---------------------------------------------------------------------
     * Tabs
     * ------------------------------------------------------------------- */
    function initTabs() {
        document.querySelectorAll("[data-tabs]").forEach((group) => {
            const buttons = group.querySelectorAll("[data-tab-target]");
            buttons.forEach((btn) => {
                btn.addEventListener("click", () => {
                    const targetSel = btn.getAttribute("data-tab-target");
                    group
                        .querySelectorAll("[data-tab-panel]")
                        .forEach((p) => p.classList.add("hidden"));
                    group.querySelector(targetSel)?.classList.remove("hidden");
                    buttons.forEach((b) => b.classList.remove("sg-tab-active"));
                    btn.classList.add("sg-tab-active");
                });
            });
        });
    }

    /* ---------------------------------------------------------------------
     * Modals
     * ------------------------------------------------------------------- */
    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
        renderIcons();
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    }
    function initModals() {
        document.addEventListener("click", (e) => {
            const openTrigger = e.target.closest("[data-modal-open]");
            if (openTrigger)
                openModal(openTrigger.getAttribute("data-modal-open"));
            const closeTrigger = e.target.closest("[data-modal-close]");
            if (closeTrigger)
                closeModal(closeTrigger.getAttribute("data-modal-close"));
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                document
                    .querySelectorAll(".sg-modal-backdrop:not(.hidden)")
                    .forEach((m) => m.classList.add("hidden"));
                document.body.classList.remove("overflow-hidden");
            }
        });
    }

    /* ---------------------------------------------------------------------
     * Confirmation dialog (promise-based, uses the #confirm-dialog modal)
     * ------------------------------------------------------------------- */
    window.confirmAction = function (
        message,
        {
            title = "Please confirm",
            confirmText = "Confirm",
            danger = true,
        } = {},
    ) {
        return new Promise((resolve) => {
            const modal = document.getElementById("confirm-dialog");
            if (!modal) {
                resolve(window.confirm(message));
                return;
            }
            modal.querySelector("[data-confirm-title]").textContent = title;
            modal.querySelector("[data-confirm-message]").textContent = message;
            const confirmBtn = modal.querySelector("[data-confirm-yes]");
            confirmBtn.textContent = confirmText;
            confirmBtn.className =
                "sg-btn " + (danger ? "sg-btn-danger" : "sg-btn-primary");

            const cleanup = (result) => {
                modal.classList.add("hidden");
                document.body.classList.remove("overflow-hidden");
                confirmBtn.removeEventListener("click", onYes);
                cancelBtn.removeEventListener("click", onNo);
                resolve(result);
            };
            const onYes = () => cleanup(true);
            const onNo = () => cleanup(false);
            const cancelBtn = modal.querySelector("[data-confirm-no]");
            confirmBtn.addEventListener("click", onYes);
            cancelBtn.addEventListener("click", onNo);

            modal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden");
        });
    };

    /* ---------------------------------------------------------------------
     * Toast notifications
     * ------------------------------------------------------------------- */
    function toast(message, type = "success", duration = 3200) {
        let container = document.getElementById("toast-container");
        if (!container) {
            container = document.createElement("div");
            container.id = "toast-container";
            document.body.appendChild(container);
        }
        const icons = {
            success: "circle-check",
            error: "circle-x",
            info: "info",
        };
        const el = document.createElement("div");
        el.className = `sg-toast ${type}`;
        el.innerHTML = `
      <i data-lucide="${icons[type] || "info"}" class="w-5 h-5 mt-0.5 flex-shrink-0" style="color:${type === "success" ? "var(--sg-success)" : type === "error" ? "var(--sg-danger)" : "var(--sg-navy-500)"}"></i>
      <div class="flex-1 text-sm" style="color:var(--sg-text)">${message}</div>
      <button class="text-slate-400 hover:text-slate-600" data-toast-close><i data-lucide="x" class="w-4 h-4"></i></button>
    `;
        container.appendChild(el);
        renderIcons();
        const remove = () => {
            el.classList.add("leaving");
            setTimeout(() => el.remove(), 200);
        };
        el.querySelector("[data-toast-close]").addEventListener(
            "click",
            remove,
        );
        setTimeout(remove, duration);
    }

    /* ---------------------------------------------------------------------
     * Grade calculation — Prelim 30% / Midterm 30% / Final 40%
     * ------------------------------------------------------------------- */
    function computeFinalGrade(prelim, midterm, final) {
        const p = parseFloat(prelim),
            m = parseFloat(midterm),
            f = parseFloat(final);
        if ([p, m, f].some((n) => isNaN(n))) return null;
        const grade = Math.round((p * 0.3 + m * 0.3 + f * 0.4) * 100) / 100;
        const passed = grade >= 75;
        let equivalent;
        if (grade >= 97) equivalent = "1.00";
        else if (grade >= 94) equivalent = "1.25";
        else if (grade >= 91) equivalent = "1.50";
        else if (grade >= 88) equivalent = "1.75";
        else if (grade >= 85) equivalent = "2.00";
        else if (grade >= 82) equivalent = "2.25";
        else if (grade >= 79) equivalent = "2.50";
        else if (grade >= 76) equivalent = "2.75";
        else if (grade >= 75) equivalent = "3.00";
        else equivalent = "5.00";
        return {
            grade,
            passed,
            equivalent,
            remarks: passed ? "Passed" : "Failed",
        };
    }

    function initGradeTables() {
        document.querySelectorAll("[data-grade-row]").forEach((row) => {
            const inputs = row.querySelectorAll(".grade-input");
            const finalCell = row.querySelector("[data-final-grade]");
            const remarksCell = row.querySelector("[data-remarks]");

            function recalc() {
                const [prelim, midterm, final] =
                    row.querySelectorAll(".grade-input");
                const result = computeFinalGrade(
                    prelim.value,
                    midterm.value,
                    final.value,
                );
                if (!result) {
                    if (finalCell) finalCell.textContent = "—";
                    if (remarksCell)
                        remarksCell.innerHTML = `<span class="sg-badge sg-badge-info">Pending</span>`;
                    return;
                }
                if (finalCell) finalCell.textContent = result.grade.toFixed(2);
                if (remarksCell) {
                    remarksCell.innerHTML = result.passed
                        ? `<span class="sg-badge sg-badge-success">Passed</span>`
                        : `<span class="sg-badge sg-badge-danger">Failed</span>`;
                }
                row.setAttribute("data-computed-grade", result.grade);
                row.setAttribute("data-computed-remarks", result.remarks);
            }

            inputs.forEach((input) => input.addEventListener("input", recalc));
            recalc();
        });
    }

    /* ---------------------------------------------------------------------
     * Table search / filter / sort (generic, data-attribute driven)
     * ------------------------------------------------------------------- */
    function initTableTools() {
        document.querySelectorAll("[data-table-search]").forEach((input) => {
            const tableId = input.getAttribute("data-table-search");
            input.addEventListener("input", () => {
                const term = input.value.trim().toLowerCase();
                document
                    .querySelectorAll(`#${tableId} tbody tr`)
                    .forEach((row) => {
                        row.style.display = row.textContent
                            .toLowerCase()
                            .includes(term)
                            ? ""
                            : "none";
                    });
                updateEmptyState(tableId);
            });
        });

        document.querySelectorAll("[data-table-filter]").forEach((select) => {
            select.addEventListener("change", () => {
                const tableId = select.getAttribute("data-table-filter");
                const col = select.getAttribute("data-filter-column");
                const val = select.value;
                console.log(
                    `Filtering table #${tableId} by column "${col}" with value "${val}"`,
                );
                document
                    .querySelectorAll(`#${tableId} tbody tr`)
                    .forEach((row) => {
                        const cell = row.querySelector(`[data-col="${col}"]`);
                        row.style.display =
                            !val || !cell || cell.textContent.trim() === val
                                ? ""
                                : "none";
                    });
                updateEmptyState(tableId);
            });
        });

        document.querySelectorAll("[data-sort-column]").forEach((th) => {
            th.addEventListener("click", () => {
                const table = th.closest("table");
                const idx = Array.from(th.parentNode.children).indexOf(th);
                const tbody = table.querySelector("tbody");
                const rows = Array.from(tbody.querySelectorAll("tr"));
                const asc = th.getAttribute("data-sort-dir") !== "asc";
                rows.sort((a, b) => {
                    const av =
                        a.children[idx]?.textContent.trim().toLowerCase() || "";
                    const bv =
                        b.children[idx]?.textContent.trim().toLowerCase() || "";
                    return asc
                        ? av.localeCompare(bv, undefined, { numeric: true })
                        : bv.localeCompare(av, undefined, { numeric: true });
                });
                table
                    .querySelectorAll("[data-sort-column]")
                    .forEach((h) => h.removeAttribute("data-sort-dir"));
                th.setAttribute("data-sort-dir", asc ? "asc" : "desc");
                rows.forEach((r) => tbody.appendChild(r));
            });
        });
    }

    function updateEmptyState(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const rows = table.querySelectorAll("tbody tr");
        const visible = Array.from(rows).filter(
            (r) => r.style.display !== "none",
        );
        const emptyState = document.querySelector(
            `[data-empty-state="${tableId}"]`,
        );
        if (emptyState)
            emptyState.classList.toggle("hidden", visible.length > 0);
    }

    /* ---------------------------------------------------------------------
     * Password show/hide
     * ------------------------------------------------------------------- */
    function initPasswordToggle() {
        document.querySelectorAll("[data-password-toggle]").forEach((btn) => {
            btn.addEventListener("click", () => {
                const input = document.getElementById(
                    btn.getAttribute("data-password-toggle"),
                );
                if (!input) return;
                const isPassword = input.type === "password";
                input.type = isPassword ? "text" : "password";
                btn.innerHTML = isPassword
                    ? '<i data-lucide="eye-off" class="w-4 h-4"></i>'
                    : '<i data-lucide="eye" class="w-4 h-4"></i>';
                renderIcons();
            });
        });
    }

    /* ---------------------------------------------------------------------
     * Simple client-side form validation helper
     * ------------------------------------------------------------------- */
    function validateRequired(form) {
        let valid = true;
        form.querySelectorAll("[required]").forEach((field) => {
            const errorEl = form.querySelector(
                `[data-error-for="${field.name || field.id}"]`,
            );
            const isEmpty = !field.value || !field.value.trim();
            field.classList.toggle("border-red-400", isEmpty);
            if (errorEl) errorEl.classList.toggle("hidden", !isEmpty);
            if (isEmpty) valid = false;
        });
        return valid;
    }

    function initLogout() {
        document.querySelectorAll("[data-logout]").forEach((btn) => {
            btn.addEventListener("click", async () => {
                const ok = await confirmAction(
                    "You will be signed out of the current session.",
                    {
                        title: "Log out?",
                        confirmText: "Log Out",
                    },
                );
                if (ok) {
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = "/logout";
                    const csrf = document.createElement("input");
                    csrf.type = "hidden";
                    csrf.name = "_token";
                    csrf.value =
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "";
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    }

    /* ---------------------------------------------------------------------
     * Generic action toast wiring — Add/Edit/Delete/Save/Submit/Print
     * ------------------------------------------------------------------- */
    function initDemoActions() {
        document.querySelectorAll("[data-toast]").forEach((el) => {
            el.addEventListener("click", () => {
                const message =
                    el.getAttribute("data-toast") || "Action completed.";
                const type = el.getAttribute("data-toast-type") || "success";
                toast(message, type);
            });
        });

        document.querySelectorAll("[data-delete-row]").forEach((btn) => {
            btn.addEventListener("click", async () => {
                const row = btn.closest("tr");
                const label =
                    btn.getAttribute("data-delete-row") || "this record";
                const ok = await confirmAction(
                    `Are you sure you want to delete ${label}? This cannot be undone.`,
                    {
                        title: "Delete record",
                        confirmText: "Delete",
                    },
                );
                if (ok && row) {
                    row.style.transition = "opacity .2s ease";
                    row.style.opacity = "0";
                    setTimeout(() => {
                        row.remove();
                        toast(`${label} was removed from this view.`, "info");
                    }, 200);
                }
            });
        });

        document.querySelectorAll("[data-print]").forEach((btn) => {
            btn.addEventListener("click", () => window.print());
        });
    }

    /* ---------------------------------------------------------------------
     * Boot
     * ------------------------------------------------------------------- */
    document.addEventListener("DOMContentLoaded", () => {
        renderIcons();
        initDarkMode();
        initSidebar();
        initDropdowns();
        initTabs();
        initModals();
        initGradeTables();
        initTableTools();
        initPasswordToggle();
        initLogout();
        initDemoActions();
        document.querySelectorAll("[data-loading-wrap]").forEach((wrap) => {
            const skeleton = wrap.querySelector("[data-skeleton]");
            const content = wrap.querySelector("[data-content]");
            if (skeleton && content) {
                content.classList.add("hidden");
                setTimeout(() => {
                    skeleton.classList.add("hidden");
                    content.classList.remove("hidden");
                    renderIcons();
                }, 450);
            }
        });
    });

    // Expose helpers used inline in Blade views
    window.SG = {
        toast,
        confirmAction,
        openModal,
        closeModal,
        computeFinalGrade,
        renderIcons,
    };

    // Auto-dismiss flash banners after a few seconds, and wire the close
    // button so users can dismiss them immediately.
    function initFlash() {
        document.querySelectorAll("[data-flash]").forEach(function (el) {
            const close = function () {
                el.style.transition = "opacity .2s ease";
                el.style.opacity = "0";
                setTimeout(function () {
                    el.remove();
                }, 200);
            };
            el.querySelector("[data-flash-close]")?.addEventListener(
                "click",
                close,
            );
            if (
                el.getAttribute("data-flash-type") === "success" ||
                el.getAttribute("data-flash-type") === "info"
            ) {
                setTimeout(close, 4000);
            }
        });
    }
    initFlash();
})();
