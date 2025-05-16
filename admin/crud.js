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
    overlay.style.display = 'block';
            modal.style.display = 'block';
    document.body.classList.add('modal-open');
        }
    }

    function closeAllModals() {
        const modals = document.querySelectorAll('.modal');
        if (overlay) overlay.style.display = 'none';
        modals.forEach(modal => {
            if (modal) modal.style.display = 'none';
        });
        document.body.classList.remove('modal-open');
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
overlay.addEventListener('click', () => {
    modalArticle.style.display = 'none';
    modalCategory.style.display = 'none';
    modalResource.style.display = 'none';
            
            // Verificar si modalEditArticle existe antes de intentar acceder a sus propiedades
            const editModal = document.getElementById('modal-edit-article');
            if (editModal) editModal.style.display = 'none';
            
            // Verificar si modalDeleteConfirmation existe antes de intentar acceder a sus propiedades
            const deleteModal = document.getElementById('modal-delete-confirmation');
            if (deleteModal) deleteModal.style.display = 'none';
            
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
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
        modalEditArticle.style.display = 'none';
                if (overlay) overlay.style.display = 'none';
        document.body.classList.remove('modal-open');
            }
    });
    }
    
    if (cancelEditButton) {
        cancelEditButton.addEventListener('click', function() {
            if (modalEditArticle) {
        modalEditArticle.style.display = 'none';
                if (overlay) overlay.style.display = 'none';
        document.body.classList.remove('modal-open');
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
});

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
            modalEditArticle.style.display = 'block';
            overlay.style.display = 'block';
            document.body.classList.add('modal-open');
            }
        })
        .catch(error => {
            console.error('Error fetching post data:', error);
            showNotification('Error al cargar los datos del post. Por favor, intente de nuevo.', 'error');
        });
}

// ... existing code ...