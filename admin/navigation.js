// navigation.js - Gestiona la navegación entre secciones

document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a los botones
    const articlesButton = document.getElementById('articlesButton');
    const categoriesButton = document.getElementById('categoriesButton');
    const commentsButton = document.getElementById('commentsButton');
    const usersButton = document.getElementById('usersButton');

    // Función para ocultar todas las secciones
    function hideAllSections() {
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(section => {
            section.style.display = 'none';
        });
    }

    // Función para mostrar una sección específica
    function showSection(sectionId) {
        hideAllSections();
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'block';
        }
    }

    // Verificar y asignar event listeners a los botones
    if (articlesButton) {
        articlesButton.addEventListener('click', function() {
            showSection('articles');
            console.log('Mostrando articles');
        });
    }

    if (categoriesButton) {
        categoriesButton.addEventListener('click', function() {
            showSection('categories');
            console.log('Mostrando categories');
        });
    }

    if (commentsButton) {
        commentsButton.addEventListener('click', function() {
            showSection('comments');
            console.log('Mostrando comments');
        });
    }

    if (usersButton) {
        usersButton.addEventListener('click', function() {
            showSection('users');
            console.log('Mostrando users');
        });
    }

    // Mostrar la sección de artículos por defecto
    showSection('articles');
    console.log('Carga inicial: mostrando articles');
}); 