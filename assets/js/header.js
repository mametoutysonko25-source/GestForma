// assets/js/header.js - Comportement du menu utilisateur dans le header
document.addEventListener("DOMContentLoaded", function () {
  const btnUserMenu = document.getElementById("btn-user-menu");
  const userMenu = document.getElementById("user-menu");

  if (btnUserMenu && userMenu) {
    btnUserMenu.addEventListener("click", function (e) {
      e.stopPropagation();
      userMenu.classList.toggle("active");
    });

    document.addEventListener("click", function () {
      userMenu.classList.remove("active");
    });
  }
});
