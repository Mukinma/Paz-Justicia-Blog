/**
 * Peace in Progress - Script para el manejo del menú de perfil
 * Maneja el comportamiento del menú desplegable del perfil en todas las páginas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Control del menú de perfil para dispositivos táctiles
    const profileBtn = document.querySelector('.profile-btn');
    const dropdownContent = document.querySelector('.dropdown-content');
    
    if (profileBtn && dropdownContent) {
        // Al hacer clic en el botón de perfil
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownContent.classList.toggle('active');
        });
        
        // Cerrar el menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.profile-dropdown')) {
                dropdownContent.classList.remove('active');
            }
        });
        
        // Manejo para dispositivos táctiles
        document.addEventListener('touchstart', function(e) {
            if (!e.target.closest('.profile-dropdown') && !e.target.closest('.dropdown-content')) {
                dropdownContent.classList.remove('active');
            }
        }, { passive: true });
    }
    
    // Mejorar la experiencia en hover para dispositivos no táctiles
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (profileDropdown && window.matchMedia('(hover: hover)').matches) {
        let hoverTimeout;
        
        profileDropdown.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
            dropdownContent.classList.add('active');
        });
        
        profileDropdown.addEventListener('mouseleave', function() {
            hoverTimeout = setTimeout(function() {
                dropdownContent.classList.remove('active');
            }, 300);
        });
    }
}); 