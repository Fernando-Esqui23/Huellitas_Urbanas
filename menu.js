document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("menuToggle");
    const menu = document.getElementById("menu");
    const menuItems = document.querySelectorAll(".menu-item");

    menuToggle.addEventListener("click", function () {
        if (menu.style.right === "0px") {
            menu.style.right = "-200px";
        } else {
            menu.style.right = "0px";
        }
    });

    menuItems.forEach(item => {
        item.addEventListener("click", function (e) {
            const submenuId = this.getAttribute("data-toggle");
            const submenu = document.getElementById(submenuId);

            if (submenu) {
                e.preventDefault(); // Prevenir navegación
                submenu.style.display = submenu.style.display === "block" ? "none" : "block";
            }
        });
    });
       // Redirige al index.html(cierre de sesion)
    if (logoutButton) {
        logoutButton.addEventListener("click", function () {
            window.location.href = 'index.html';  
        });
    }
    
});
