document.addEventListener('DOMContentLoaded', function() {
    // Control de menú móvil
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });
    }
    
    // Control de dropdowns en móvil
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
        
        if (dropdownToggle) {
            dropdownToggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                }
            });
        }
    });
    
    // Mejora del menú desplegable de perfil
    const profileDropdown = document.querySelector('.profile-dropdown');
    const profileBtn = document.querySelector('.profile-btn');
    const dropdownContent = document.querySelector('.profile-dropdown .dropdown-content');
    
    if (profileDropdown && dropdownContent) {
        let timeoutId;
        let isMenuOpen = false;
        
        // Abrir/cerrar con clic (ayuda en dispositivos táctiles)
        profileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (isMenuOpen) {
                dropdownContent.classList.remove('active');
                isMenuOpen = false;
            } else {
                dropdownContent.classList.add('active');
                isMenuOpen = true;
            }
        });
        
        // Cuando el cursor entra en el botón de perfil
        profileDropdown.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            dropdownContent.classList.add('active');
            isMenuOpen = true;
        });
        
        // Cuando el cursor entra en el menú desplegable
        dropdownContent.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            isMenuOpen = true;
        });
        
        // Cuando el cursor sale del botón de perfil
        profileDropdown.addEventListener('mouseleave', function(e) {
            // Si está moviendo hacia el menú desplegable, no ocultar
            if (e.relatedTarget === dropdownContent || dropdownContent.contains(e.relatedTarget)) {
                return;
            }
            
            // Retraso para ocultar el menú
            timeoutId = setTimeout(() => {
                // Verificar si el cursor está sobre el menú desplegable
                if (!dropdownContent.matches(':hover')) {
                    dropdownContent.classList.remove('active');
                    isMenuOpen = false;
                }
            }, 800); // Retraso de 800ms (más tiempo)
        });
        
        // Cuando el cursor sale del menú desplegable
        dropdownContent.addEventListener('mouseleave', function(e) {
            // Si está moviendo hacia el botón de perfil, no ocultar
            if (e.relatedTarget === profileDropdown || profileDropdown.contains(e.relatedTarget)) {
                return;
            }
            
            // Retraso para ocultar el menú
            timeoutId = setTimeout(() => {
                dropdownContent.classList.remove('active');
                isMenuOpen = false;
            }, 500); // Retraso de 500ms (más tiempo)
        });
        
        // Cerrar el menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (isMenuOpen && !profileDropdown.contains(e.target) && !dropdownContent.contains(e.target)) {
                dropdownContent.classList.remove('active');
                isMenuOpen = false;
            }
        });
    }
    
    // Cambiar estilo de header al hacer scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header.main-header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Log para verificar carga
    console.log('✅ nav-fix.js cargado correctamente');
}); 