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

    // Inicializar elementos para modales
    const overlay = getElement('overlay');
    const modalArticle = getElement('modal-article');
    const modalCategory = getElement('modal-category');
    const modalResource = getElement('modal-resource');
    const modalEditArticle = getElement('modal-edit-article');
    const modalDeleteConfirmation = getElement('modal-delete-confirmation');

    // Botones para abrir modales
    const addArticleButton = getElement('article-add-button');
    const addCategoryButton = getElement('category-add-button');
    const addResourceButton = getElement('resource-add-button');
    
    // Botones para cerrar modales
    const closeModalButtons = document.querySelectorAll('.close-button');
    const cancelButtons = document.querySelectorAll('.cancel-article');
    
    // Botón para enviar formulario
    const submitButton = getElement('submit-button');
    const form = getElement('myForm1');

    // Funciones para gestionar modales
    function openModal(modal) {
        if (overlay && modal) {
            console.log('Abriendo modal:', modal.id);
            overlay.classList.add('active');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            document.body.classList.add('modal-open');
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
        
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
    }

    // Event listeners para botones de modales
    if (addArticleButton) {
        addArticleButton.addEventListener('click', function() {
            console.log('Clic en botón agregar artículo');
            openModal(modalArticle);
        });
    }

    if (addCategoryButton) {
        addCategoryButton.addEventListener('click', function() {
            console.log('Clic en botón agregar categoría');
            openModal(modalCategory);
        });
    }

    if (addResourceButton) {
        addResourceButton.addEventListener('click', function() {
            openModal(modalResource);
        });
    }

    // Event listeners para cerrar modales
    closeModalButtons.forEach(button => {
        button.addEventListener('click', closeAllModals);
    });

    cancelButtons.forEach(button => {
        button.addEventListener('click', closeAllModals);
    });

    // Event listener para el overlay
    if (overlay) {
        overlay.addEventListener('click', closeAllModals);
    }

    // Event listener para enviar formulario
    if (submitButton && form) {
        submitButton.addEventListener('click', function() {
            form.submit();
        });
    }

    // Inicializar funcionalidad de editar artículos
    const closeEditModalButton = getElement('close-edit-modal');
    const cancelEditButton = getElement('cancel-edit');
    const editForm = getElement('editForm');
    const submitEditButton = getElement('submit-edit-button');

    // Event listeners para botones de edición
    const editButtons = document.querySelectorAll('.edit-button[data-id]');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            fetchPostData(postId);
        });
    });
    
    if (closeEditModalButton) {
        closeEditModalButton.addEventListener('click', function() {
            if (modalEditArticle) {
                modalEditArticle.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';
            }
        });
    }
    
    if (cancelEditButton) {
        cancelEditButton.addEventListener('click', function() {
            if (modalEditArticle) {
                modalEditArticle.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';
            }
        });
    }
    
    if (submitEditButton && editForm) {
        submitEditButton.addEventListener('click', function() {
            editForm.submit();
        });
    }

    // Inicializar funcionalidad para editar categorías
    initCategoryEditing();
    
    // Inicializar funcionalidad para cambiar roles
    initRoleChanging();

    // Inicializar manejadores de eventos para modales de eliminación
    initDeleteConfirmation();
});

// Función para manejar confirmación de eliminación
function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('.delete-button[data-id]');
    const modalDeleteConfirmation = document.getElementById('modal-delete-confirmation');
    const overlay = document.getElementById('overlay');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    const closeDeleteModalButton = document.getElementById('close-delete-modal');
    const confirmDeleteButton = document.getElementById('confirm-delete-button');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            const postTitle = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            
            document.getElementById('post-delete-title').textContent = postTitle;
            
            // Guardar el ID para usarlo en la confirmación
            confirmDeleteButton.setAttribute('data-id', postId);
            
            // Mostrar el modal
            if (modalDeleteConfirmation && overlay) {
                overlay.classList.add('active');
                modalDeleteConfirmation.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', function() {
            closeDeleteModal();
        });
    }
    
    if (closeDeleteModalButton) {
        closeDeleteModalButton.addEventListener('click', function() {
            closeDeleteModal();
        });
    }
    
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            // Aquí iría la lógica para eliminar el post
            window.location.href = `eliminar_post.php?id=${postId}`;
        });
    }
    
    function closeDeleteModal() {
        if (modalDeleteConfirmation && overlay) {
            overlay.classList.remove('active');
            modalDeleteConfirmation.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }
}

// Función para obtener datos del post
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
            const modalEditArticle = document.getElementById('modal-edit-article');
            const overlay = document.getElementById('overlay');
            if (modalEditArticle && overlay) {
                overlay.classList.add('active');
                modalEditArticle.classList.add('active');
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
            }
        })
        .catch(error => {
            console.error('Error fetching post data:', error);
            showNotification('Error al cargar los datos del post. Por favor, intente de nuevo.', 'error');
        });
}

// Función para mostrar notificaciones
function showNotification(message, type) {
    const notification = document.getElementById('notificationModal');
    
    if (notification) {
        notification.textContent = message;
        notification.className = 'notification-modal';
        notification.classList.add(type);
        notification.style.display = 'block';
        
        setTimeout(function() {
            notification.style.display = 'none';
        }, 3000);
    }
}

function initRoleChanging() {
    console.log('Inicializando cambio de rol...');
    
    const changeRoleButtons = document.querySelectorAll('.change-role-button');
    console.log('Botones encontrados:', changeRoleButtons.length);
    
    const modal = document.getElementById('modal-change-role');
    const overlay = document.getElementById('overlay');
    const closeButton = document.getElementById('close-change-role-modal');
    const cancelButton = document.getElementById('cancel-change-role');
    const confirmButton = document.getElementById('confirm-change-role-button');
    const userIdInput = document.getElementById('change-role-user-id');
    const roleSelect = document.getElementById('new-role');

    if (!modal || !overlay || !closeButton || !cancelButton || !confirmButton || !userIdInput || !roleSelect) {
        console.error('Elementos del modal no encontrados:', {
            modal: !!modal,
            overlay: !!overlay,
            closeButton: !!closeButton,
            cancelButton: !!cancelButton,
            confirmButton: !!confirmButton,
            userIdInput: !!userIdInput,
            roleSelect: !!roleSelect
        });
        return;
    }

    // Remover event listeners existentes
    const removeEventListeners = () => {
        changeRoleButtons.forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });
        closeButton.replaceWith(closeButton.cloneNode(true));
        cancelButton.replaceWith(cancelButton.cloneNode(true));
        overlay.replaceWith(overlay.cloneNode(true));
        confirmButton.replaceWith(confirmButton.cloneNode(true));
    };

    removeEventListeners();

    // Obtener los elementos nuevamente después de clonarlos
    const newChangeRoleButtons = document.querySelectorAll('.change-role-button');
    const newCloseButton = document.getElementById('close-change-role-modal');
    const newCancelButton = document.getElementById('cancel-change-role');
    const newOverlay = document.getElementById('overlay');
    const newConfirmButton = document.getElementById('confirm-change-role-button');

    function openModal(userId, currentRole) {
        console.log('Abriendo modal para usuario:', userId, 'rol actual:', currentRole);
        userIdInput.value = userId;
        roleSelect.value = currentRole.toLowerCase();
        modal.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        console.log('Cerrando modal...');
        modal.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        userIdInput.value = '';
        roleSelect.value = '';
    }

    newChangeRoleButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const userId = button.dataset.userId;
            const currentRole = button.dataset.currentRole;
            openModal(userId, currentRole);
        });
    });

    newCloseButton.addEventListener('click', closeModal);
    newCancelButton.addEventListener('click', closeModal);
    newOverlay.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    newConfirmButton.addEventListener('click', async () => {
        console.log('Confirmando cambio de rol...');
        const formData = new FormData();
        formData.append('id_usuario', userIdInput.value);
        formData.append('rol', roleSelect.value);

        try {
            console.log('Enviando solicitud...');
            const response = await fetch('cambiar_rol.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                showNotification('Rol actualizado exitosamente', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Error al cambiar el rol', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al procesar la solicitud', 'error');
        }

        closeModal();
    });
}

function initCategoryEditing() {
    console.log('Inicializando edición de categorías...');
    const editButtons = document.querySelectorAll('.edit-category-button');
    const deleteButtons = document.querySelectorAll('.delete-category-button');
    const modalEditCategory = document.getElementById('modal-edit-category');
    const modalDeleteCategory = document.getElementById('modal-delete-category');
    const overlay = document.getElementById('overlay');
    
    // Configuración para botones de edición de categorías
    editButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const categoryId = button.dataset.id;
            console.log('Editando categoría:', categoryId);
            
            try {
                const response = await fetch(`obtener_categoria.php?id=${categoryId}`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('edit-category-id').value = categoryId;
                    document.getElementById('edit-category-nombre').value = data.categoria.nombre;
                    document.getElementById('edit-category-descripcion').value = data.categoria.descripcion || '';
                    
                    if (modalEditCategory && overlay) {
                        overlay.classList.add('active');
                        modalEditCategory.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                } else {
                    showNotification('Error al cargar la categoría', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al cargar la categoría', 'error');
            }
        });
    });
    
    // Configuración para botones de eliminación de categorías
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const categoryId = button.dataset.id;
            const categoryName = button.dataset.nombre;
            
            document.getElementById('category-delete-name').textContent = categoryName;
            document.getElementById('confirm-delete-category-button').setAttribute('data-id', categoryId);
            
            if (modalDeleteCategory && overlay) {
                overlay.classList.add('active');
                modalDeleteCategory.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Configurar cierre de modales de categorías
    const closeEditCategoryModalBtn = document.getElementById('close-edit-category-modal');
    const cancelEditCategoryBtn = document.getElementById('cancel-edit-category');
    const closeDeleteCategoryModalBtn = document.getElementById('close-delete-category-modal');
    const cancelDeleteCategoryBtn = document.getElementById('cancel-delete-category');
    
    if (closeEditCategoryModalBtn) {
        closeEditCategoryModalBtn.addEventListener('click', () => {
            closeModal(modalEditCategory);
        });
    }
    
    if (cancelEditCategoryBtn) {
        cancelEditCategoryBtn.addEventListener('click', () => {
            closeModal(modalEditCategory);
        });
    }
    
    if (closeDeleteCategoryModalBtn) {
        closeDeleteCategoryModalBtn.addEventListener('click', () => {
            closeModal(modalDeleteCategory);
        });
    }
    
    if (cancelDeleteCategoryBtn) {
        cancelDeleteCategoryBtn.addEventListener('click', () => {
            closeModal(modalDeleteCategory);
        });
    }
    
    function closeModal(modal) {
        if (modal && overlay) {
            modal.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }
}

// Asegurarse de que todas las funciones se inicialicen cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado, inicializando funciones...');
    // Las inicializaciones principales ya están en el primer evento DOMContentLoaded
});