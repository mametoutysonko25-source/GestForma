document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("gf-sidebar");
  const collapseBtn = document.getElementById("sidebar-collapse-btn");

  if (sidebar && collapseBtn) {
    collapseBtn.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      collapseBtn.textContent = sidebar.classList.contains("collapsed")
        ? "»"
        : "«";
    });
  }
});
