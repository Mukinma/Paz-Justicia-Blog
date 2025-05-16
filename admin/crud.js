// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Función para verificar la existencia de un elemento
    function getElement(id) {
        const element = document.getElementById(id);
        return element;
    }

    // Funciones para gestionar las vistas
    function hideAllSections() {
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(section => {
            section.style.display = 'none';
        });
    }

    function showSection(sectionId) {
        hideAllSections();
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'block';
        }
    }

    // Inicializar los botones de navegación
    const articlesButton = getElement('articlesButton');
    const categoriesButton = getElement('categoriesButton');
    const commentsButton = getElement('commentsButton');
    const usersButton = getElement('usersButton');

    // Agregar event listeners a los botones de navegación
    if (articlesButton) {
        articlesButton.addEventListener('click', function() {
            showSection('articles');
        });
    }

    if (categoriesButton) {
        categoriesButton.addEventListener('click', function() {
            showSection('categories');
        });
    }

    if (commentsButton) {
        commentsButton.addEventListener('click', function() {
            showSection('comments');
        });
    }

    if (usersButton) {
        usersButton.addEventListener('click', function() {
            showSection('users');
        });
    }

    // Mostrar la sección de artículos por defecto
    showSection('articles');

    // Variables para modales
    const overlay = document.getElementById('overlay');
    const modalArticle = document.getElementById('modal-article');
    const modalCategory = document.getElementById('modal-category');
    const modalResource = document.getElementById('modal-resource');
    const modalEditArticle = document.getElementById('modal-edit-article');
    const modalDeleteConfirmation = document.getElementById('modal-delete-confirmation');
    const modalEditCategory = document.getElementById('modal-edit-category');
    const modalDeleteCategory = document.getElementById('modal-delete-category');

    // Botones para abrir modales
    const addArticleButton = document.getElementById('article-add-button');
    const addCategoryButton = document.getElementById('category-add-button');
    
    // Botones para editar/eliminar categorías
    const editCategoryButtons = document.querySelectorAll('.edit-category-button');
    const deleteCategoryButtons = document.querySelectorAll('.delete-category-button');
    
    // Botones para cerrar modales
    const closeButtons = document.querySelectorAll('.close-button');
    const cancelButtons = document.querySelectorAll('.cancel-article');
    
    // Botones para formularios
    const submitButton = document.getElementById('submit-button');
    const submitCategoryButton = document.getElementById('submit-category-button');
    const submitEditCategoryButton = document.getElementById('submit-edit-category-button');
    const confirmDeleteCategoryButton = document.getElementById('confirm-delete-category-button');
    
    // Formularios
    const form = document.getElementById('myForm1');
    const categoryForm = document.getElementById('categoryForm');
    const editCategoryForm = document.getElementById('editCategoryForm');

    console.log("DOM cargado, elementos encontrados:", {
        overlay: !!overlay,
        modalEditCategory: !!modalEditCategory,
        modalDeleteCategory: !!modalDeleteCategory,
        editCategoryButtons: editCategoryButtons.length,
        deleteCategoryButtons: deleteCategoryButtons.length
    });

    // Funciones para gestionar modales
    function openModal(modal) {
        if (overlay && modal) {
            console.log('Abriendo modal:', modal.id);
            overlay.classList.add('active');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAllModals() {
        console.log('Cerrando todos los modales');
        const modals = document.querySelectorAll('.modal');
        
        if (overlay) {
            overlay.classList.remove('active');
        }
        
        modals.forEach(modal => {
            if (modal) modal.classList.remove('active');
        });
        
        document.body.style.overflow = 'auto';
    }

    // Event listeners para botones de modales
    if (addArticleButton) {
        addArticleButton.addEventListener('click', function() {
            openModal(modalArticle);
        });
    }

    if (addCategoryButton) {
        addCategoryButton.addEventListener('click', function() {
            openModal(modalCategory);
        });
    }

    // Event listeners para cerrar modales
    closeButtons.forEach(button => {
        button.addEventListener('click', closeAllModals);
    });

    cancelButtons.forEach(button => {
        button.addEventListener('click', closeAllModals);
    });

    // Event listener para el overlay
    if (overlay) {
        overlay.addEventListener('click', closeAllModals);
    }

    // Event listener para enviar formulario de artículo
    if (submitButton && form) {
        submitButton.addEventListener('click', function() {
            form.submit();
        });
    }

    // Event listener para el formulario de categorías
    if (submitCategoryButton && categoryForm) {
        submitCategoryButton.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Validar el formulario
            const nombre = categoryForm.querySelector('#nombre').value.trim();
            const descripcion = categoryForm.querySelector('#descripcion').value.trim();
            
            if (!nombre) {
                showNotification('El nombre de la categoría es requerido', 'error');
                return;
            }
            
            try {
                const formData = new FormData(categoryForm);
                const response = await fetch('insertar_categoria.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al crear la categoría');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAllModals();
                    // Limpiar el formulario
                    categoryForm.reset();
                    // Recargar la página para mostrar la nueva categoría
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Error al crear la categoría', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification(error.message || 'Error al crear la categoría', 'error');
            }
        });
    }

    // Inicializar botones de editar categorías
    if (editCategoryButtons.length > 0) {
        console.log('Configurando botones de edición de categorías:', editCategoryButtons.length);
        editCategoryButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                console.log('Botón editar categoría clickeado:', this.dataset.id);
                const categoryId = this.dataset.id;
                
                // Hacer una petición para obtener los datos de la categoría
                fetch(`obtener_categoria.php?id=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Llenar el formulario con los datos
                            document.getElementById('edit-category-id').value = data.categoria.id_categoria;
                            document.getElementById('edit-category-nombre').value = data.categoria.nombre;
                            document.getElementById('edit-category-descripcion').value = data.categoria.descripcion || '';
                            
                            // Mostrar el modal
                            openModal(modalEditCategory);
                        } else {
                            showNotification(data.message || 'Error al cargar la categoría', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error al cargar la categoría', 'error');
                    });
            });
        });
    }

    // Inicializar botones de eliminar categorías
    if (deleteCategoryButtons.length > 0) {
        console.log('Configurando botones de eliminación de categorías:', deleteCategoryButtons.length);
        deleteCategoryButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                console.log('Botón eliminar categoría clickeado:', this.dataset.id);
                const categoryId = this.dataset.id;
                const categoryName = this.dataset.nombre;
                
                // Llenar el modal con los datos
                document.getElementById('category-delete-name').textContent = categoryName;
                confirmDeleteCategoryButton.setAttribute('data-id', categoryId);
                
                // Mostrar el modal
                openModal(modalDeleteCategory);
            });
        });
    }

    // Event listener para el formulario de edición de categorías
    if (submitEditCategoryButton && editCategoryForm) {
        submitEditCategoryButton.addEventListener('click', async function(e) {
            e.preventDefault();
            console.log('Clic en guardar cambios de categoría');
            
            try {
                // Verificar que el formulario tenga los datos requeridos
                const id = editCategoryForm.querySelector('#edit-category-id').value;
                const nombre = editCategoryForm.querySelector('#edit-category-nombre').value;
                
                if (!id || !nombre) {
                    console.error('Faltan datos requeridos:', { id, nombre });
                    showNotification('Faltan datos requeridos', 'error');
                    return;
                }
                
                console.log('Datos a enviar:', { 
                    id, 
                    nombre, 
                    descripcion: editCategoryForm.querySelector('#edit-category-descripcion').value 
                });
                
                // Crear FormData (esto conserva los nombres de los campos del formulario)
                const formData = new FormData(editCategoryForm);
                
                // Enviar la solicitud con modo de depuración
                const response = await fetch('editar_categoria.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        // No agregar Content-Type para FormData, el navegador lo establece automáticamente
                    }
                });
                
                console.log('Estado de la respuesta:', response.status, response.statusText);
                
                // Si la respuesta no es exitosa, lanzar un error
                if (!response.ok) {
                    // Intentar leer el texto de la respuesta
                    const errorText = await response.text();
                    console.error('Respuesta del servidor (no JSON):', errorText);
                    throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                }
                
                // Verificar que la respuesta sea JSON válido
                let responseText = '';
                try {
                    responseText = await response.text();
                    console.log('Respuesta del servidor (texto):', responseText);
                    
                    const data = JSON.parse(responseText);
                    console.log('Respuesta del servidor (JSON):', data);
                    
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeAllModals();
                        window.location.reload();
                    } else {
                        showNotification(data.message || 'Error al editar la categoría', 'error');
                    }
                } catch (jsonError) {
                    console.error('Error al analizar JSON:', jsonError);
                    console.error('Texto recibido del servidor:', responseText);
                    showNotification('Error al procesar la respuesta del servidor', 'error');
                }
            } catch (error) {
                console.error('Error en la solicitud AJAX:', error);
                showNotification('Error al editar la categoría: ' + error.message, 'error');
            }
        });
    }

    // Event listener para confirmar eliminación de categoría
    if (confirmDeleteCategoryButton) {
        confirmDeleteCategoryButton.addEventListener('click', async function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-id');
            console.log('Confirmando eliminación de categoría:', categoryId);
            
            try {
                const response = await fetch('eliminar_categoria.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: categoryId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAllModals();
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Error al eliminar la categoría', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al eliminar la categoría', 'error');
            }
        });
    }

    // Función para mostrar notificaciones
    window.showNotification = function(message, type = 'success') {
        const notificationModal = document.getElementById('notificationModal');
        
        if (notificationModal) {
            notificationModal.textContent = message;
            notificationModal.className = 'notification-modal';
            notificationModal.classList.add(type);
            notificationModal.style.display = 'block';
            
            setTimeout(() => {
                notificationModal.style.display = 'none';
            }, 3000);
        } else {
            alert(message);
        }
    };

    // Inicializar funcionalidad de editar artículos
    const editButtons = document.querySelectorAll('.edit-button');
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.id;
                fetch(`editar_post.php?id=${postId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Rellenar el formulario con los datos
                        document.getElementById('edit-id').value = data.post.id_post;
                        document.getElementById('edit-titulo').value = data.post.titulo;
                        document.getElementById('edit-descripcion').value = data.post.resumen;
                        document.getElementById('edit-contenido').value = data.post.contenido;
                        document.getElementById('edit-categoria').value = data.post.id_categoria;
                        
                        // Mostrar la imagen actual si existe
                        if (data.post.imagen_destacada) {
                            document.getElementById('current-image').src = data.post.imagen_destacada;
                            document.getElementById('current-image-container').style.display = 'block';
                        } else {
                            document.getElementById('current-image-container').style.display = 'none';
                        }
                        
                        // Abrir el modal
                        openModal(modalEditArticle);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error al cargar los datos del post', 'error');
                    });
            });
        });
    }

    // Event listeners para confirmación de eliminación de artículos
    const deleteButtons = document.querySelectorAll('.delete-button');
    if (deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.id;
                const postTitle = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                
                document.getElementById('post-delete-title').textContent = postTitle;
                document.getElementById('confirm-delete-button').setAttribute('data-id', postId);
                
                openModal(modalDeleteConfirmation);
            });
        });
    }

    // Event listener para confirmar eliminación de artículo
    const confirmDeleteButton = document.getElementById('confirm-delete-button');
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            window.location.href = `eliminar_post.php?id=${postId}`;
        });
    }
});