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
    modalEditArticle.style.display = 'none'; // Add this line to hide the edit modal
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

// Edit post functionality
const modalEditArticle = document.getElementById('modal-edit-article');
const closeEditModalButton = document.getElementById('close-edit-modal');
const cancelEditButton = document.getElementById('cancel-edit');
const editForm = document.getElementById('editForm');
const submitEditButton = document.getElementById('submit-edit-button');

// Add event listeners to all edit buttons
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-button[data-id]');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            fetchPostData(postId);
        });
    });
    
    // Close modal when clicking the close button
    closeEditModalButton.addEventListener('click', () => {
        modalEditArticle.style.display = 'none';
        overlay.style.display = 'none';
    });
    
    // Close modal when clicking the cancel button
    cancelEditButton.addEventListener('click', () => {
        modalEditArticle.style.display = 'none';
        overlay.style.display = 'none';
    });
    
    // Submit edit form
    submitEditButton.addEventListener('click', () => {
        editForm.submit();
    });
});

// Function to fetch post data
function fetchPostData(postId) {
    fetch(`editar_post.php?id=${postId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            populateEditForm(data);
            modalEditArticle.style.display = 'block';
            overlay.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching post data:', error);
            alert('Error loading post data. Please try again.');
        });
}

// Function to populate the edit form with post data
function populateEditForm(post) {
    document.getElementById('edit-id').value = post.id_post;
    document.getElementById('edit-titulo').value = post.titulo;
    document.getElementById('edit-descripcion').value = post.resumen;
    document.getElementById('edit-contenido').value = post.contenido;
    document.getElementById('edit-categoria').value = post.id_categoria;
    document.getElementById('edit-estado').value = post.estado;
    
    // Handle image display if it exists
    const imageContainer = document.getElementById('current-image-container');
    if (post.imagen) {
        imageContainer.innerHTML = `
            <p>Current image:</p>
            <img src="${post.imagen.ruta}" alt="${post.imagen.alt_text}" style="max-width:200px;">
            <input type="hidden" name="current_image_id" value="${post.id_imagen_destacada}">
        `;
        imageContainer.style.display = 'block';
    } else {
        imageContainer.style.display = 'none';
    }
}

// Post deletion functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get all delete buttons with data-id attribute
    const deleteButtons = document.querySelectorAll('.delete-button[data-id]');
    const modalDeleteConfirmation = document.getElementById('modal-delete-confirmation');
    const closeDeleteModalButton = document.getElementById('close-delete-modal');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    const confirmDeleteButton = document.getElementById('confirm-delete-button');
    const postDeleteTitle = document.getElementById('post-delete-title');
    
    let deletePostId = null;
    let deleteButtonElement = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get post ID and title
            deletePostId = this.getAttribute('data-id');
            deleteButtonElement = this;
            const postTitle = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            
            // Show modal with post title
            postDeleteTitle.textContent = postTitle;
            modalDeleteConfirmation.style.display = 'block';
            overlay.style.display = 'block';
        });
    });
    
    // Close modal when clicking the close button
    closeDeleteModalButton.addEventListener('click', () => {
        modalDeleteConfirmation.style.display = 'none';
        overlay.style.display = 'none';
    });
    
    // Close modal when clicking the cancel button
    cancelDeleteButton.addEventListener('click', () => {
        modalDeleteConfirmation.style.display = 'none';
        overlay.style.display = 'none';
    });
    
    // Handle delete confirmation
    confirmDeleteButton.addEventListener('click', () => {
        if (deletePostId) {
            deletePost(deletePostId, deleteButtonElement);
            modalDeleteConfirmation.style.display = 'none';
            overlay.style.display = 'none';
        }
    });
});

// Function to delete post
function deletePost(postId, buttonElement) {
    // Create form data for the request
    const formData = new FormData();
    formData.append('id', postId);
    
    // Send DELETE request
    fetch('eliminar_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from the table on success with animation
            const row = buttonElement.closest('tr');
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            
            setTimeout(() => {
                row.remove();
                // Show success message
                alert('Post deleted successfully');
            }, 300);
        } else {
            // Show error message
            alert('Error deleting post: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting post. Please try again.');
    });
}