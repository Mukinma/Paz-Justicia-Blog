const contentBody = document.querySelector('.content-body');
const menuLinks = document.querySelectorAll('.left-side-options a');

menuLinks.forEach(link => {
  link.addEventListener('click', event => {
    event.preventDefault();
    const sectionId = link.getAttribute('data-section'); // Ej. data-section="home"
    
    // Aquí puedes cambiar el contenido dinámicamente
    // Ejemplo rápido: contentBody.innerHTML = `<h2>${sectionId}</h2>`;
    // O mostrar/ocultar secciones específicas

    
  });
});