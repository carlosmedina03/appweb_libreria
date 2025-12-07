// RESPONSABLE: Rol 2 (Front)rd
// Validaciones generales, manejo de modales, toggles de menú.
console.log("Sistema cargado");

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // MENÚ HAMBURGUESA
    // ==========================================
    const menuBtn = document.getElementById('mobile-menu-btn');
    const navbarMenu = document.getElementById('navbar-menu');

    if (menuBtn && navbarMenu) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Evita que el clic se propague al documento
            navbarMenu.classList.toggle('active');
            console.log("Abriendo/Cerrando menú...")
        });
    }

    // ==========================================
    // SUBMENÚS
    // ==========================================
    const dropdownBtns = document.querySelectorAll('.dropbtn');

    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            
            if (window.innerWidth <= 768) {
                e.preventDefault();   
                e.stopPropagation();  
                
                const dropdownContent = this.nextElementSibling;
                
                const yaEstabaAbierto = dropdownContent.classList.contains('show');

                document.querySelectorAll('.dropdown-content').forEach(content => {
                    content.classList.remove('show');
                });

                if (!yaEstabaAbierto) {
                    dropdownContent.classList.add('show');
                }
            }
        });
    });

    // ==========================================
    // CERRAR AL HACER CLIC AFUERA
    // ==========================================
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            
            if (navbarMenu && !navbarMenu.contains(e.target) && e.target !== menuBtn) {
                
                navbarMenu.classList.remove('active');
                
                document.querySelectorAll('.dropdown-content').forEach(c => c.classList.remove('show'));
            }
        }
    });
});