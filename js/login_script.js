const container = document.querySelector('.container');
const signupButton = document.querySelector('.signup-section header');
const loginButton = document.querySelector('.login-section header');
const formInputs = document.querySelectorAll('form input');
const socialButtons = document.querySelectorAll('.social-buttons button');

// Función para animación de entrada de elementos
const animateItems = (items, delay = 100) => {
    items.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            
            // Trigger reflow
            void item.offsetWidth;
            
            item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, delay * index);
    });
};

// Animación inicial
window.addEventListener('DOMContentLoaded', () => {
    // Animación de entrada para elementos del formulario
    animateItems(formInputs, 150);
    animateItems(socialButtons, 200);
    
    // Eliminar clase de animación inicial después de que termine
    setTimeout(() => {
        container.classList.add('initialized');
    }, 1500);
});

// Efecto ripple para botones
const buttons = document.querySelectorAll('.btn, .social-buttons button');
buttons.forEach(button => {
    button.addEventListener('click', function(e) {
        const x = e.clientX - e.target.getBoundingClientRect().left;
        const y = e.clientY - e.target.getBoundingClientRect().top;
        
        const ripple = document.createElement('span');
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;
        ripple.className = 'ripple';
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Efecto de enfoque en campos de entrada
formInputs.forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
    
    // Inicializar estado para entradas que ya tienen valor
    if (input.value) {
        input.parentElement.classList.add('focused');
    }
});

// Cambio entre login y registro con animación mejorada
loginButton.addEventListener('click', () => {
    container.classList.add('active');
    // Animar elementos cuando se muestra el login
    setTimeout(() => {
        const loginInputs = document.querySelectorAll('.login-section form input');
        const loginButtons = document.querySelectorAll('.login-section .social-buttons button');
        animateItems(loginInputs, 100);
        animateItems(loginButtons, 150);
    }, 400);
});

signupButton.addEventListener('click', () => {
    container.classList.remove('active');
    // Animar elementos cuando se muestra el registro
    setTimeout(() => {
        const signupInputs = document.querySelectorAll('.signup-section form input');
        const signupButtons = document.querySelectorAll('.signup-section .social-buttons button');
        animateItems(signupInputs, 100);
        animateItems(signupButtons, 150);
    }, 400);
});

// Añadir clase de inicialización después de cargar
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 300);
});
