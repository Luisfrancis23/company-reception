document.addEventListener("DOMContentLoaded", function () {

    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
        document.addEventListener("click", function (e) {
            if (window.innerWidth < 992 && sidebar.classList.contains("show")) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove("show");
                }
            }
        });
    }

    const alerts = document.querySelectorAll(".auto-alert");
    alerts.forEach(function (alertBox) {
        setTimeout(function () {
            alertBox.style.transition = "opacity 0.5s";
            alertBox.style.opacity = "0";
            setTimeout(function () {
                alertBox.style.display = "none";
            }, 500);
        }, 4000);
    });

    const deleteButtons = document.querySelectorAll(".btn-delete-confirm");
    deleteButtons.forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            if (!confirm("Are you sure you want to delete this visitor record? This cannot be undone.")) {
                e.preventDefault();
            }
        });
    });

    const phoneInput = document.getElementById("phone");
    if (phoneInput) {
        phoneInput.addEventListener("input", function () {
            this.value = this.value.replace(/[^0-9+\-\s]/g, "");
        });
    }
});
