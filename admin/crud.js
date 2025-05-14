// Botones agregar
const addArticleButton = document.getElementById('article-add-button'); 
const addCategoryButton = document.getElementById('category-add-button');
const addResourceButton = document.getElementById('resource-add-button');

const modalArticle = document.getElementById('modal-article');
const modalCategory = document.getElementById('modal-category');
const modalResource = document.getElementById('modal-resource');
const modalDeleteConfirmation = document.getElementById('modal-delete-confirmation');
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
    document.body.classList.add('modal-open');
});
// Show modal for adding category
addCategoryButton.addEventListener('click', () => {
    modalCategory.style.display = 'block';
    overlay.style.display = 'block';
    document.body.classList.add('modal-open');
});
// Show modal for adding resource
addResourceButton.addEventListener('click', () => {
    modalResource.style.display = 'block';
    overlay.style.display = 'block';
    document.body.classList.add('modal-open');
});
// Close modal when clicking the close button
closeModalButton.addEventListener('click', () => {
    modalArticle.style.display = 'none';
    modalCategory.style.display = 'none';
    modalResource.style.display = 'none';
    overlay.style.display = 'none';
    document.body.classList.remove('modal-open');
});
// Close modal when clicking outside of it
overlay.addEventListener('click', () => {
    modalArticle.style.display = 'none';
    modalCategory.style.display = 'none';
    modalResource.style.display = 'none';
    modalEditArticle.style.display = 'none';
    modalDeleteConfirmation.style.display = 'none';
    overlay.style.display = 'none';
    document.body.classList.remove('modal-open');
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
        document.body.classList.remove('modal-open');
    });
    
    // Close modal when clicking the cancel button
    cancelEditButton.addEventListener('click', () => {
        modalEditArticle.style.display = 'none';
        overlay.style.display = 'none';
        document.body.classList.remove('modal-open');
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
            document.body.classList.add('modal-open');
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
            document.body.classList.add('modal-open');
        });
    });
    
    // Close modal when clicking the close button
    closeDeleteModalButton.addEventListener('click', () => {
        modalDeleteConfirmation.style.display = 'none';
        overlay.style.display = 'none';
        document.body.classList.remove('modal-open');
    });
    
    // Close modal when clicking the cancel button
    cancelDeleteButton.addEventListener('click', () => {
        modalDeleteConfirmation.style.display = 'none';
        overlay.style.display = 'none';
        document.body.classList.remove('modal-open');
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
                // Show success notification
                showNotification('Post eliminado correctamente', 'success');
            }, 300);
        } else {
            // Show error notification
            showNotification('Error al eliminar el post: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al eliminar el post. Por favor, intente nuevamente.', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.getElementById('notificationModal');
    notification.textContent = message;
    notification.className = `notification-modal ${type}`;
    notification.style.display = 'block';

    // Remover la clase modal-open del body después de que la notificación desaparezca
    setTimeout(() => {
        notification.style.display = 'none';
        document.body.classList.remove('modal-open');
    }, 3000);
}

function archivePost(postId, buttonElement) {
    const row = buttonElement.closest('tr');
    const title = row.querySelector('td:nth-child(2)').textContent;
    const category = row.querySelector('td:nth-child(3)').textContent;
    const author = row.querySelector('td:nth-child(5)').textContent;

    fetch('archivar_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover la fila con animación
            row.style.transition = 'opacity 0.5s ease';
            row.style.opacity = '0';
            
            setTimeout(() => {
                row.remove();
                
                // Verificar si la tabla está vacía
                const tbody = document.querySelector('#published-articles tbody');
                if (tbody.children.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="no-results">
                                <div class="no-results-message">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="no-results-icon">
                                        <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
                                    </svg>
                                    <p>No hay artículos publicados</p>
                                    <span>Los artículos que agregues aparecerán aquí</span>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                // Agregar la fila a la tabla de archivados
                const archivedTbody = document.querySelector('#archived-articles tbody');
                // Limpiar el mensaje de "No hay artículos archivados" si existe
                if (archivedTbody.querySelector('.no-results')) {
                    archivedTbody.innerHTML = '';
                }
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="checkbox"></td>
                    <td>${title}</td>
                    <td>${category}</td>
                    <td>Archivado</td>
                    <td>${author}</td>
                    <td>
                        <button class="view-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentcolor" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                        </button>
                        <button class="edit-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg>
                        </button>
                        <button class="delete-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentcolor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                        </button>
                        <button class="unarchive-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M32 32l448 0c17.7 0 32 14.3 32 32l0 32c0 17.7-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96L0 64C0 46.3 14.3 32 32 32zm0 128l448 0 0 256c0 35.3-28.7 64-64 64L96 480c-35.3 0-64-28.7-64-64l0-256zm128 80c0 8.8 7.2 16 16 16l160 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-160 0c-8.8 0-16 7.2-16 16z"/></svg>
                        </button>
                    </td>
                `;
                archivedTbody.appendChild(newRow);
                addButtonListeners(newRow);
                
                showNotification('Artículo archivado correctamente');
            }, 500);
        } else {
            showNotification('Error al archivar el artículo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al archivar el artículo');
    });
}

function unarchivePost(postId, buttonElement) {
    const row = buttonElement.closest('tr');
    const title = row.querySelector('td:nth-child(2)').textContent;
    const category = row.querySelector('td:nth-child(3)').textContent;
    const author = row.querySelector('td:nth-child(5)').textContent;

    fetch('desarchivar_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover la fila con animación
            row.style.transition = 'opacity 0.5s ease';
            row.style.opacity = '0';
            
            setTimeout(() => {
                row.remove();
                
                // Verificar si la tabla está vacía
                const tbody = document.querySelector('#archived-articles tbody');
                if (tbody.children.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="no-results">
                                <div class="no-results-message">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="no-results-icon">
                                        <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
                                    </svg>
                                    <p>No hay artículos archivados</p>
                                    <span>Los artículos que archives aparecerán aquí</span>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                // Agregar la fila a la tabla de publicados
                const publishedTbody = document.querySelector('#published-articles tbody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="checkbox"></td>
                    <td>${title}</td>
                    <td>${category}</td>
                    <td>Publicado</td>
                    <td>${author}</td>
                    <td>
                        <button class="view-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentcolor" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                        </button>
                        <button class="edit-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg>
                        </button>
                        <button class="delete-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentcolor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                        </button>
                        <button class="archive-button" data-id="${postId}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M32 32l448 0c17.7 0 32 14.3 32 32l0 32c0 17.7-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96L0 64C0 46.3 14.3 32 32 32zm0 128l448 0 0 256c0 35.3-28.7 64-64 64L96 480c-35.3 0-64-28.7-64-64l0-256zm128 80c0 8.8 7.2 16 16 16l160 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-160 0c-8.8 0-16 7.2-16 16z"/></svg>
                        </button>
                    </td>
                `;
                publishedTbody.appendChild(newRow);
                addButtonListeners(newRow);
                
                showNotification('Artículo desarchivado correctamente');
            }, 500);
        } else {
            showNotification('Error al desarchivar el artículo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al desarchivar el artículo');
    });
}

// Función para agregar event listeners a los botones
function addButtonListeners(row) {
    // Agregar event listener al botón de editar
    const editButton = row.querySelector('.edit-button');
    if (editButton) {
        editButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            fetchPostData(postId);
        });
    }
    
    // Agregar event listener al botón de eliminar
    const deleteButton = row.querySelector('.delete-button');
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            const postTitle = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            
            document.getElementById('post-delete-title').textContent = postTitle;
            modalDeleteConfirmation.style.display = 'block';
            overlay.style.display = 'block';
            document.body.classList.add('modal-open');
            
            // Guardar el ID del post y el botón para usarlos en la confirmación
            window.deletePostId = postId;
            window.deleteButtonElement = this;
        });
    }
    
    // Agregar event listener al botón de archivar
    const archiveButton = row.querySelector('.archive-button');
    if (archiveButton) {
        archiveButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            archivePost(postId, this);
        });
    }
    
    // Agregar event listener al botón de desarchivar
    const unarchiveButton = row.querySelector('.unarchive-button');
    if (unarchiveButton) {
        unarchiveButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            unarchivePost(postId, this);
        });
    }
}

// Funcionalidad para mostrar artículos publicados/archivados
document.addEventListener('DOMContentLoaded', function() {
    const showPublishedButton = document.getElementById('showPublished');
    const showArchivedButton = document.getElementById('showArchived');
    const publishedArticles = document.getElementById('published-articles');
    const archivedArticles = document.getElementById('archived-articles');

    // Establecer el botón de publicados como activo por defecto
    showPublishedButton.classList.add('active');

    showPublishedButton.addEventListener('click', function() {
        publishedArticles.style.display = 'block';
        archivedArticles.style.display = 'none';
        showPublishedButton.classList.add('active');
        showArchivedButton.classList.remove('active');
    });

    showArchivedButton.addEventListener('click', function() {
        publishedArticles.style.display = 'none';
        archivedArticles.style.display = 'block';
        showArchivedButton.classList.add('active');
        showPublishedButton.classList.remove('active');
    });
});

// Agregar event listeners iniciales cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners para botones de archivar
    const archiveButtons = document.querySelectorAll('.archive-button[data-id]');
    archiveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            archivePost(postId, this);
        });
    });

    // Event listeners para botones de desarchivar
    const unarchiveButtons = document.querySelectorAll('.unarchive-button[data-id]');
    unarchiveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            unarchivePost(postId, this);
        });
    });

    // Event listeners para botones de editar
    const editButtons = document.querySelectorAll('.edit-button[data-id]');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            fetchPostData(postId);
        });
    });

    // Event listeners para botones de eliminar
    const deleteButtons = document.querySelectorAll('.delete-button[data-id]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            const postTitle = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            
            document.getElementById('post-delete-title').textContent = postTitle;
            modalDeleteConfirmation.style.display = 'block';
            overlay.style.display = 'block';
            document.body.classList.add('modal-open');
            
            // Guardar el ID del post y el botón para usarlos en la confirmación
            window.deletePostId = postId;
            window.deleteButtonElement = this;
        });
    });
});

// Función para manejar la vista previa de imágenes
function handleImagePreview(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            preview.classList.add('active');
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
        preview.classList.remove('active');
    }
}

// Agregar event listeners para los campos de imagen
document.addEventListener('DOMContentLoaded', function() {
    const imagenIlustrativa = document.getElementById('imagen_ilustrativa');
    const imagenBackground = document.getElementById('imagen_background');
    
    if (imagenIlustrativa) {
        imagenIlustrativa.addEventListener('change', function() {
            handleImagePreview(this, 'preview_ilustrativa');
        });
    }
    
    if (imagenBackground) {
        imagenBackground.addEventListener('change', function() {
            handleImagePreview(this, 'preview_background');
        });
    }
});

// Función para manejar la eliminación de posts
function handleDeletePost(postId) {
    const modal = document.getElementById('modal-delete-confirmation');
    const overlay = document.getElementById('overlay');
    const postTitle = document.getElementById('post-delete-title');
    const confirmButton = document.getElementById('confirm-delete-button');
    const cancelButton = document.getElementById('cancel-delete');
    const closeButton = document.getElementById('close-delete-modal');

    // Obtener el título del post
    const postRow = document.querySelector(`tr[data-id="${postId}"]`);
    if (postRow) {
        const titleCell = postRow.querySelector('td:nth-child(2)');
        postTitle.textContent = titleCell.textContent;
    }

    // Mostrar el modal
    modal.style.display = 'flex';
    overlay.style.display = 'block';

    // Función para cerrar el modal
    const closeModal = () => {
        modal.style.display = 'none';
        overlay.style.display = 'none';
    };

    // Event listeners para cerrar el modal
    cancelButton.onclick = closeModal;
    closeButton.onclick = closeModal;
    overlay.onclick = closeModal;

    // Manejar la confirmación de eliminación
    confirmButton.onclick = async () => {
        try {
            const formData = new FormData();
            formData.append('id', postId);

            const response = await fetch('eliminar_post.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Mostrar notificación de éxito
                showNotification('Post eliminado correctamente', 'success');
                // Eliminar la fila de la tabla
                if (postRow) {
                    postRow.remove();
                }
                // Cerrar el modal
                closeModal();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            showNotification('Error al eliminar el post: ' + error.message, 'error');
        }
    };
}

// Agregar event listeners a los botones de eliminar
document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const postId = button.getAttribute('data-id');
            if (postId) {
                handleDeletePost(postId);
            }
        });
    });
});