// Botones agregar
const addArticleButton = document.getElementById('article-add-button'); 
const addCategoryButton = document.getElementById('category-add-button');
const addResourceButton = document.getElementById('resource-add-button');

const modalArticle = document.getElementById('modal-article');
const modalCategory = document.getElementById('modal-category');
const modalResource = document.getElementById('modal-resource');

const closeModalButton = document.getElementById('close-modal');

const overlay = document.getElementById('overlay');

const submitButton = document.getElementById('submit-button'); // Botón externo
const form = document.getElementById('myForm1'); // Formulario

// Botones de navegación
const articlesButton = document.getElementById('articlesButton'); // Botón de artículos
const categoriesButton = document.getElementById('categoriesButton'); // Botón de categorías
const commentsButton = document.getElementById('commentsButton'); // Botón de comentarios
const usersButton = document.getElementById('usersButton'); // Botón de usuarios
const statisticsButton = document.getElementById('statisticsButton'); // Botón de estadísticas
const resourcesButton = document.getElementById('resourcesButton'); // Botón de recursos

// Secciones tablas
const articles = document.getElementById('articles'); // Artículos
const categories = document.getElementById('categories'); // Categorías
const comments = document.getElementById('comments'); // Comentarios
const users = document.getElementById('users'); // Usuarios
const statistics = document.getElementById('statistics'); // Estadísticas
const resources = document.getElementById('resources'); // Recursos

// Show modal for adding article
addArticleButton.addEventListener('click', () => {
    modalArticle.style.display = 'block';
    overlay.style.display = 'block';
});
// Show modal for adding category
addCategoryButton.addEventListener('click', () => {
    modalCategory.style.display = 'block';
    overlay.style.display = 'block';
});
// Show modal for adding resource
addResourceButton.addEventListener('click', () => {
    modalResource.style.display = 'block';
    overlay.style.display = 'block';
});
// Close modal when clicking the close button
closeModalButton.addEventListener('click', () => {
    modalArticle.style.display = 'none';
    modalCategory.style.display = 'none';
    modalResource.style.display = 'none';
    overlay.style.display = 'none';
});
// Close modal when clicking outside of it
overlay.addEventListener('click', () => {
    modalArticle.style.display = 'none';
    modalCategory.style.display = 'none';
    modalResource.style.display = 'none';
    overlay.style.display = 'none';
});

// Show articles section
articlesButton.addEventListener('click', () => {
    articles.style.display = 'block';
    categories.style.display = 'none';
    comments.style.display = 'none';
    users.style.display = 'none';
    statistics.style.display = 'none';
    resources.style.display = 'none';
});

// Show categories section
categoriesButton.addEventListener('click', () => {
    articles.style.display = 'none';
    categories.style.display = 'block';
    comments.style.display = 'none';
    users.style.display = 'none';
    statistics.style.display = 'none';
    resources.style.display = 'none';
});

// Show comments section
commentsButton.addEventListener('click', () => {
    articles.style.display = 'none';
    categories.style.display = 'none';
    comments.style.display = 'block';
    users.style.display = 'none';
    statistics.style.display = 'none';
    resources.style.display = 'none';
});

// Show users section
usersButton.addEventListener('click', () => {
    articles.style.display = 'none';
    categories.style.display = 'none';
    comments.style.display = 'none';
    users.style.display = 'block';
    statistics.style.display = 'none';
    resources.style.display = 'none';
});

// Show statistics section
statisticsButton.addEventListener('click', () => {
    articles.style.display = 'none';
    categories.style.display = 'none';
    comments.style.display = 'none';
    users.style.display = 'none';
    statistics.style.display = 'block';
    resources.style.display = 'none';
});

// Show resources section
resourcesButton.addEventListener('click', () => {
    articles.style.display = 'none';
    categories.style.display = 'none';
    comments.style.display = 'none';
    users.style.display = 'none';
    statistics.style.display = 'none';
    resources.style.display = 'block';
});

// Submit form when clicking the external button
submitButton.addEventListener('click', () => {
    form.submit(); // Envía el formulario
});