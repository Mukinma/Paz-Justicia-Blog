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
    const modalDeleteCommentConfirmation = document.getElementById('modal-delete-comment-confirmation');

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
        submitButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir comportamiento por defecto
            console.log('Botón de envío de artículo clickeado');
            console.log('ID del formulario:', form.id);
            console.log('Action:', form.getAttribute('action'));
            console.log('Method:', form.getAttribute('method'));
            console.log('Enctype:', form.getAttribute('enctype'));
            
            // Validar formulario antes de enviarlo
            if (form.checkValidity()) {
                try {
                    console.log('Formulario válido, intentando enviar...');
                    // Verificar que el formulario tiene el atributo enctype correcto
                    if (!form.getAttribute('enctype') || form.getAttribute('enctype') !== 'multipart/form-data') {
                        form.setAttribute('enctype', 'multipart/form-data');
                        console.log('Se estableció el enctype a multipart/form-data');
                    }
                    
                    // Comprobar que el action es correcto
                    if (!form.getAttribute('action') || form.getAttribute('action') === '') {
                        form.setAttribute('action', 'insertar_post.php');
                        console.log('Se estableció el action a insertar_post.php');
                    }
                    
                    // Enviar el formulario
                    console.log('Enviando formulario...');
            form.submit();
                } catch (error) {
                    console.error('Error al enviar el formulario:', error);
                    showNotification('Error al enviar el formulario: ' + error.message, 'error');
                }
            } else {
                console.log('Formulario inválido, por favor complete todos los campos requeridos');
                showNotification('Por favor complete todos los campos requeridos', 'error');
                // Forzar la validación del navegador para mostrar mensajes de error
                form.reportValidity();
            }
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
            console.log('Datos de categoría recibidos:', data.categoria);
                            // Llenar el formulario con los datos
                            document.getElementById('edit-category-id').value = data.categoria.id_categoria;
                            document.getElementById('edit-category-nombre').value = data.categoria.nombre;
                            document.getElementById('edit-category-descripcion').value = data.categoria.descripcion || '';
            
            // Manejar la imagen de la categoría si existe
            const imagenContainer = document.getElementById('category-current-image-container');
            const imagenElement = document.getElementById('category-current-image');
            const imagenActualInput = document.getElementById('edit-category-imagen-actual');
            
            // Manejar la imagen de fondo de la categoría si existe
            const imagenFondoContainer = document.getElementById('category-current-bg-container');
            const imagenFondoElement = document.getElementById('category-current-bg');
            const imagenFondoActualInput = document.getElementById('edit-category-imagen-fondo-actual');
            
            if (data.categoria.imagen) {
                console.log('Imagen encontrada:', data.categoria.imagen);
                // Asignar la imagen actual al campo oculto
                imagenActualInput.value = data.categoria.imagen;
                
                // Mostrar la imagen actual
                let rutaImagen = data.categoria.imagen;
                if (rutaImagen.startsWith('../')) {
                    imagenElement.src = rutaImagen;
                    console.log('Usando ruta relativa existente:', rutaImagen);
                } else {
                    imagenElement.src = '../' + rutaImagen;
                    console.log('Prefijando ruta con "../":', '../' + rutaImagen);
                }
                
                imagenContainer.style.display = 'block';
                
                // Añadir manejador de errores para la imagen
                imagenElement.onerror = function() {
                    console.error('Error al cargar la imagen de categoría:', this.src);
                    this.onerror = null;
                    this.src = '../assets/image-placeholder.png';
                    imagenContainer.querySelector('small').textContent = 'No se pudo cargar la imagen';
                };
                
                // Añadir manejador para verificar que la imagen se cargó correctamente
                imagenElement.onload = function() {
                    console.log('Imagen cargada correctamente:', this.src);
                    imagenContainer.querySelector('small').textContent = 'Esta es la imagen actual de la categoría';
                };
            } else {
                console.log('La categoría no tiene imagen');
                // Si no hay imagen, ocultar el contenedor
                imagenContainer.style.display = 'none';
                // Limpiar el campo oculto
                imagenActualInput.value = '';
            }
            
            // Manejar la imagen de fondo si existe
            if (data.categoria.imagen_fondo) {
                console.log('Imagen de fondo encontrada:', data.categoria.imagen_fondo);
                // Asignar la imagen de fondo actual al campo oculto
                imagenFondoActualInput.value = data.categoria.imagen_fondo;
                
                // Mostrar la imagen de fondo actual
                let rutaImagenFondo = data.categoria.imagen_fondo;
                if (rutaImagenFondo.startsWith('../')) {
                    imagenFondoElement.src = rutaImagenFondo;
                    console.log('Usando ruta relativa existente para fondo:', rutaImagenFondo);
                } else {
                    imagenFondoElement.src = '../' + rutaImagenFondo;
                    console.log('Prefijando ruta de fondo con "../":', '../' + rutaImagenFondo);
                }
                
                imagenFondoContainer.style.display = 'block';
                
                // Añadir manejador de errores para la imagen de fondo
                imagenFondoElement.onerror = function() {
                    console.error('Error al cargar la imagen de fondo:', this.src);
                    this.onerror = null;
                    this.src = '../assets/image-placeholder.png';
                    imagenFondoContainer.querySelector('small').textContent = 'No se pudo cargar la imagen de fondo';
                };
                
                // Añadir manejador para verificar que la imagen de fondo se cargó correctamente
                imagenFondoElement.onload = function() {
                    console.log('Imagen de fondo cargada correctamente:', this.src);
                    imagenFondoContainer.querySelector('small').textContent = 'Esta es la imagen de fondo actual de la categoría';
                };
            } else {
                console.log('La categoría no tiene imagen de fondo');
                // Si no hay imagen de fondo, ocultar el contenedor
                imagenFondoContainer.style.display = 'none';
                // Limpiar el campo oculto
                imagenFondoActualInput.value = '';
            }
            
            // Configurar la previsualización de la nueva imagen
            const imageInput = document.getElementById('edit-category-imagen');
            const imagePreview = document.getElementById('preview_category_image');
            
            // Configurar la previsualización de la nueva imagen de fondo
            const imageFondoInput = document.getElementById('edit-category-imagen-fondo');
            const imageFondoPreview = document.getElementById('preview_category_bg');
            
            // Limpiar previsualización anterior
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            imagePreview.classList.remove('active');
            
            imageFondoPreview.src = '';
            imageFondoPreview.style.display = 'none';
            imageFondoPreview.classList.remove('active');
            
            // Limpiar el input de archivo
            imageInput.value = '';
            imageFondoInput.value = '';
            
            if (imageInput && imagePreview) {
                // Remover oyentes anteriores para evitar duplicados
                imageInput.removeEventListener('change', handleImagePreview);
                
                // Función para manejar la previsualización
                function handleImagePreview() {
                    if (this.files && this.files.length > 0) {
                        console.log('Nueva imagen seleccionada:', this.files[0].name);
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                            imagePreview.classList.add('active');
                            console.log('Previsualización generada');
                        }
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        imagePreview.src = '';
                        imagePreview.style.display = 'none';
                        imagePreview.classList.remove('active');
                        console.log('No hay imagen seleccionada');
                    }
                }
                
                // Añadir el oyente
                imageInput.addEventListener('change', handleImagePreview);
            }
            
            // Manejar previsualización de imagen de fondo
            if (imageFondoInput && imageFondoPreview) {
                // Remover oyentes anteriores para evitar duplicados
                imageFondoInput.removeEventListener('change', handleBgImagePreview);
                
                // Función para manejar la previsualización de imagen de fondo
                function handleBgImagePreview() {
                    if (this.files && this.files.length > 0) {
                        console.log('Nueva imagen de fondo seleccionada:', this.files[0].name);
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imageFondoPreview.src = e.target.result;
                            imageFondoPreview.style.display = 'block';
                            imageFondoPreview.classList.add('active');
                            console.log('Previsualización de fondo generada');
                        }
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        imageFondoPreview.src = '';
                        imageFondoPreview.style.display = 'none';
                        imageFondoPreview.classList.remove('active');
                        console.log('No hay imagen de fondo seleccionada');
                    }
                }
                
                // Añadir el oyente
                imageFondoInput.addEventListener('change', handleBgImagePreview);
            }
                            
                            // Mostrar el modal
                            openModal(modalEditCategory);
        } else {
            console.error('Error al obtener datos de la categoría:', data.message);
                            showNotification(data.message || 'Error al cargar la categoría', 'error');
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
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
                    descripcion: editCategoryForm.querySelector('#edit-category-descripcion').value,
                    imagen_actual: editCategoryForm.querySelector('#edit-category-imagen-actual').value,
                    imagen_fondo_actual: editCategoryForm.querySelector('#edit-category-imagen-fondo-actual').value
                });
                
                // Verificar si se está enviando una nueva imagen
                const imagenInput = editCategoryForm.querySelector('#edit-category-imagen');
                if (imagenInput.files && imagenInput.files.length > 0) {
                    console.log('Nueva imagen a enviar:', imagenInput.files[0].name, 'tamaño:', imagenInput.files[0].size);
                } else {
                    console.log('No hay nueva imagen, se mantendrá la actual');
                }
                
                // Verificar si se está enviando una nueva imagen de fondo
                const imagenFondoInput = editCategoryForm.querySelector('#edit-category-imagen-fondo');
                if (imagenFondoInput.files && imagenFondoInput.files.length > 0) {
                    console.log('Nueva imagen de fondo a enviar:', imagenFondoInput.files[0].name, 'tamaño:', imagenFondoInput.files[0].size);
                    
                    // Verificar si el tamaño del archivo supera el límite permitido (20MB)
                    if (imagenFondoInput.files[0].size > 20 * 1024 * 1024) {
                        console.error('El tamaño del archivo de imagen de fondo excede el límite de 20MB');
                        showNotification('La imagen de fondo es demasiado grande. El tamaño máximo es 20MB', 'error');
                        return;
                    }
                } else {
                    console.log('No hay nueva imagen de fondo, se mantendrá la actual');
                }
                
                // Crear FormData (esto conserva los nombres de los campos del formulario)
                const formData = new FormData(editCategoryForm);
                
                // Depurar los datos que se enviarán
                console.log('Datos FormData a enviar:');
                for (const [key, value] of formData.entries()) {
                    if ((key === 'imagen' || key === 'imagen_fondo') && value instanceof File) {
                        console.log(`${key}: Archivo ${value.name} (${value.size} bytes)`);
                    } else {
                        console.log(`${key}: ${value}`);
                    }
                }
                
                // Enviar la solicitud
                console.log('Enviando solicitud a editar_categoria.php');
                const response = await fetch('editar_categoria.php', {
                    method: 'POST',
                    body: formData
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
                        console.error('Error en la respuesta:', data.message);
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
                
                console.log('Solicitando datos del post ID:', postId);
                
                // Mostrar un indicador de carga
                showNotification('Cargando datos del post...', 'info');
                
                fetch(`editar_post.php?id=${postId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        
                        if (!data || !data.post) {
                            throw new Error('Formato de respuesta inválido');
                        }
                        
                        // Depuración para rutas de imágenes
                        console.log('Imagen destacada:', data.post.imagen_destacada);
                        console.log('Imagen de fondo:', data.post.imagen_background);
                        
                        // Rellenar el formulario con los datos
                        document.getElementById('edit-id').value = data.post.id_post;
                        document.getElementById('edit-titulo').value = data.post.titulo;
                        document.getElementById('edit-descripcion').value = data.post.resumen;
                        document.getElementById('edit-contenido').value = data.post.contenido;
                        document.getElementById('edit-categoria').value = data.post.id_categoria;
                        
                        // Limpiar IDs de imágenes anteriores si existen
                        const existingFields = editForm.querySelectorAll('input[type="hidden"][name^="current_"]');
                        existingFields.forEach(field => field.remove());
                        
                        // Mostrar la imagen destacada actual si existe
                        if (data.post.imagen_destacada) {
                            // Verificar si la ruta ya contiene "../" para evitar rutas duplicadas
                            let rutaImagen = data.post.imagen_destacada;
                            if (rutaImagen.startsWith('../')) {
                                document.getElementById('current-image').src = rutaImagen;
                                console.log('Ruta de imagen destacada con "../":', rutaImagen);
                            } else {
                                document.getElementById('current-image').src = '../' + rutaImagen;
                                console.log('Ruta de imagen destacada con "../" añadido:', '../' + rutaImagen);
                            }
                            
                            document.getElementById('current-image-container').style.display = 'block';
                            
                            // Añadir manejador de errores para la imagen
                            document.getElementById('current-image').onerror = function() {
                                console.error('Error al cargar la imagen destacada:', this.src);
                                this.onerror = null;
                                this.src = '../assets/image-placeholder.png';
                                document.getElementById('current-image-container').querySelector('small').textContent = 'No se pudo cargar la imagen (ruta: ' + rutaImagen + ')';
                            };
                            
                            // Añadir manejador para verificar que la imagen se cargó correctamente
                            document.getElementById('current-image').onload = function() {
                                console.log('Imagen destacada cargada correctamente:', this.src);
                            };
                            
                            // Almacenar el ID de la imagen destacada
                            if (data.post.id_imagen_destacada) {
                                console.log('Guardando ID de imagen destacada:', data.post.id_imagen_destacada);
                                
                                // Buscar si ya existe un campo con este nombre
                                const existingField = editForm.querySelector('input[name="current_image_id"]');
                                if (existingField) {
                                    // Actualizar el valor
                                    existingField.value = data.post.id_imagen_destacada;
                                    console.log('Campo existente de id_imagen_destacada actualizado');
                                } else {
                                    // Crear nuevo campo
                                    const hiddenField = document.createElement('input');
                                    hiddenField.type = 'hidden';
                                    hiddenField.name = 'current_image_id';
                                    hiddenField.value = data.post.id_imagen_destacada;
                                    editForm.appendChild(hiddenField);
                                    console.log('Nuevo campo de id_imagen_destacada añadido');
                                }
                                
                                // También agregar el id como atributo de datos al contenedor
                                document.getElementById('current-image-container').dataset.imageId = data.post.id_imagen_destacada;
                            }
                        } else {
                            document.getElementById('current-image-container').style.display = 'none';
                        }
                        
                        // Mostrar la imagen de fondo actual si existe
                        if (data.post.imagen_background) {
                            // Verificar si la ruta ya contiene "../" para evitar rutas duplicadas
                            let rutaBackground = data.post.imagen_background;
                            if (rutaBackground.startsWith('../')) {
                                document.getElementById('current-background').src = rutaBackground;
                                console.log('Ruta de imagen de fondo con "../":', rutaBackground);
                            } else {
                                document.getElementById('current-background').src = '../' + rutaBackground;
                                console.log('Ruta de imagen de fondo con "../" añadido:', '../' + rutaBackground);
                            }
                            
                            document.getElementById('current-background-container').style.display = 'block';
                            
                            // Añadir manejador de errores para la imagen
                            document.getElementById('current-background').onerror = function() {
                                console.error('Error al cargar la imagen de fondo:', this.src);
                                this.onerror = null;
                                this.src = '../assets/image-placeholder.png';
                                document.getElementById('current-background-container').querySelector('small').textContent = 'No se pudo cargar la imagen (ruta: ' + rutaBackground + ')';
                            };
                            
                            // Añadir manejador para verificar que la imagen se cargó correctamente
                            document.getElementById('current-background').onload = function() {
                                console.log('Imagen de fondo cargada correctamente:', this.src);
                            };
                            
                            // Almacenar el ID de la imagen de fondo
                            if (data.post.id_imagen_background) {
                                console.log('Guardando ID de imagen de fondo:', data.post.id_imagen_background);
                                
                                // Buscar si ya existe un campo con este nombre
                                const existingField = editForm.querySelector('input[name="current_background_id"]');
                                if (existingField) {
                                    // Actualizar el valor
                                    existingField.value = data.post.id_imagen_background;
                                    console.log('Campo existente de id_imagen_background actualizado');
                                } else {
                                    // Crear nuevo campo
                                    const hiddenBackgroundField = document.createElement('input');
                                    hiddenBackgroundField.type = 'hidden';
                                    hiddenBackgroundField.name = 'current_background_id';
                                    hiddenBackgroundField.value = data.post.id_imagen_background;
                                    editForm.appendChild(hiddenBackgroundField);
                                    console.log('Nuevo campo de id_imagen_background añadido');
                                }
                                
                                // También agregar el id como atributo de datos al contenedor
                                document.getElementById('current-background-container').dataset.imageId = data.post.id_imagen_background;
                            }
                        } else {
                            document.getElementById('current-background-container').style.display = 'none';
                        }
                        
                        // Configurar las previsualizaciones de imagen
                        configureImagePreviews();
                        
                        // Abrir el modal
                        openModal(modalEditArticle);
                    })
                    .catch(error => {
                        console.error('Error al cargar datos del post:', error);
                        showNotification('Error al cargar los datos del post: ' + error.message, 'error');
                    });
            });
        });
    }

    // Función para configurar las previsualizaciones de imágenes
    function configureImagePreviews() {
        // Previsualización para imágenes al crear artículo
        const imagenIlustrativa = document.getElementById('imagen_ilustrativa');
        const previewIlustrativa = document.getElementById('preview_ilustrativa');
        
        const imagenBackground = document.getElementById('imagen_background');
        const previewBackground = document.getElementById('preview_background');
        
        // Previsualización para imágenes al editar artículo
        const editImagenIlustrativa = document.getElementById('edit-imagen');
        const previewEditIlustrativa = document.getElementById('preview_edit_ilustrativa');
        
        const editImagenBackground = document.getElementById('edit-imagen-background');
        const previewEditBackground = document.getElementById('preview_edit_background');
        
        // Previsualización para imágenes de categorías
        const newCategoryImage = document.getElementById('categoria_imagen');
        const previewNewCategoryImage = document.getElementById('preview_new_category_image');
        
        const newCategoryBgImage = document.getElementById('categoria_imagen_fondo');
        const previewNewCategoryBgImage = document.getElementById('preview_new_category_bg');
        
        const editCategoryImage = document.getElementById('edit-category-imagen');
        const previewEditCategoryImage = document.getElementById('preview_category_image');
        
        const editCategoryBgImage = document.getElementById('edit-category-imagen-fondo');
        const previewEditCategoryBgImage = document.getElementById('preview_category_bg');
        
        // Configurar cada input de archivo para mostrar una previsualización
        setupImagePreview(imagenIlustrativa, previewIlustrativa);
        setupImagePreview(imagenBackground, previewBackground);
        setupImagePreview(editImagenIlustrativa, previewEditIlustrativa);
        setupImagePreview(editImagenBackground, previewEditBackground);
        setupImagePreview(newCategoryImage, previewNewCategoryImage);
        setupImagePreview(newCategoryBgImage, previewNewCategoryBgImage);
        setupImagePreview(editCategoryImage, previewEditCategoryImage);
        setupImagePreview(editCategoryBgImage, previewEditCategoryBgImage);
    }
    
    // Función auxiliar para configurar la previsualización de imagen
    function setupImagePreview(inputElement, previewElement) {
        if (inputElement && previewElement) {
            inputElement.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewElement.src = e.target.result;
                        previewElement.style.display = 'block';
                        previewElement.classList.add('active');
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewElement.src = '';
                    previewElement.style.display = 'none';
                    previewElement.classList.remove('active');
                }
            });
        }
    }
    
    // Inicializar las previsualizaciones de imagen al cargar la página
    configureImagePreviews();

    // Configurar la acción de guardar los cambios del artículo
    const submitEditButton = document.getElementById('submit-edit-button');
    const editForm = document.getElementById('editForm');
    
    if (submitEditButton && editForm) {
        submitEditButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Enviando formulario de edición...');
            
            // Verificar que el formulario tenga el enctype correcto
            if (!editForm.getAttribute('enctype') || editForm.getAttribute('enctype') !== 'multipart/form-data') {
                editForm.setAttribute('enctype', 'multipart/form-data');
                console.log('Se estableció el enctype a multipart/form-data');
            }
            
            // Verificar que el action sea correcto
            if (!editForm.getAttribute('action') || editForm.getAttribute('action') === '') {
                editForm.setAttribute('action', 'editar_post.php');
                console.log('Se estableció el action a editar_post.php');
            }
            
            // Verificar que el método sea correcto
            if (!editForm.getAttribute('method') || editForm.getAttribute('method') !== 'POST') {
                editForm.setAttribute('method', 'POST');
                console.log('Se estableció el método a POST');
            }
            
            // Asegurar que los campos hidden para IDs de imágenes estén presentes
            const currentImageContainer = document.getElementById('current-image-container');
            const currentBackgroundContainer = document.getElementById('current-background-container');
            
            // Verificar si ya hay campos ocultos para los IDs de imágenes
            let currentImageIdField = editForm.querySelector('input[name="current_image_id"]');
            let currentBackgroundIdField = editForm.querySelector('input[name="current_background_id"]');
            
            // Si el contenedor de imagen destacada está visible, asegurarse de que el ID se envíe
            if (currentImageContainer && currentImageContainer.style.display !== 'none' && !currentImageIdField) {
                const idFieldsDestacada = editForm.querySelectorAll('input[type="hidden"][name="current_image_id"]');
                if (idFieldsDestacada.length === 0) {
                    console.log('Añadiendo campo oculto para ID de imagen destacada');
                    
                    // Buscar entre los elementos existentes
                    const existingFields = Array.from(editForm.elements);
                    let foundImageId = null;
                    
                    for(let field of existingFields) {
                        if (field.name === 'current_image_id') {
                            foundImageId = field.value;
                            break;
                        }
                    }
                    
                    if (!foundImageId) {
                        // Intentar recuperar el ID de data attributes
                        if (currentImageContainer.dataset.imageId) {
                            foundImageId = currentImageContainer.dataset.imageId;
                        }
                    }
                    
                    // Crear el campo si no existe
                    if (foundImageId) {
                        currentImageIdField = document.createElement('input');
                        currentImageIdField.type = 'hidden';
                        currentImageIdField.name = 'current_image_id';
                        currentImageIdField.value = foundImageId;
                        editForm.appendChild(currentImageIdField);
                        console.log('Campo de ID de imagen destacada añadido con valor:', foundImageId);
                    }
                }
            }
            
            // Si el contenedor de imagen de fondo está visible, asegurarse de que el ID se envíe
            if (currentBackgroundContainer && currentBackgroundContainer.style.display !== 'none' && !currentBackgroundIdField) {
                const idFieldsBackground = editForm.querySelectorAll('input[type="hidden"][name="current_background_id"]');
                if (idFieldsBackground.length === 0) {
                    console.log('Añadiendo campo oculto para ID de imagen de fondo');
                    
                    // Buscar entre los elementos existentes
                    const existingFields = Array.from(editForm.elements);
                    let foundBackgroundId = null;
                    
                    for(let field of existingFields) {
                        if (field.name === 'current_background_id') {
                            foundBackgroundId = field.value;
                            break;
                        }
                    }
                    
                    if (!foundBackgroundId) {
                        // Intentar recuperar el ID de data attributes
                        if (currentBackgroundContainer.dataset.imageId) {
                            foundBackgroundId = currentBackgroundContainer.dataset.imageId;
                        }
                    }
                    
                    // Crear el campo si no existe
                    if (foundBackgroundId) {
                        currentBackgroundIdField = document.createElement('input');
                        currentBackgroundIdField.type = 'hidden';
                        currentBackgroundIdField.name = 'current_background_id';
                        currentBackgroundIdField.value = foundBackgroundId;
                        editForm.appendChild(currentBackgroundIdField);
                        console.log('Campo de ID de imagen de fondo añadido con valor:', foundBackgroundId);
                    }
                }
            }

            // Verificar que los campos obligatorios estén presentes
            const requiredFields = ['id', 'titulo', 'descripcion', 'categoria', 'contenido'];
            let camposFaltantes = [];
            
            for(let campo of requiredFields) {
                const elemento = editForm.elements[campo];
                if (!elemento || elemento.value.trim() === '') {
                    camposFaltantes.push(campo);
                }
            }
            
            if (camposFaltantes.length > 0) {
                console.error('Faltan campos obligatorios:', camposFaltantes);
                showNotification('Error: Faltan campos obligatorios: ' + camposFaltantes.join(', '), 'error');
                return;
            }

            // Capturar todos los campos para depuración
            console.log('Campos del formulario antes de enviar:');
            const formData = new FormData(editForm);
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // Validar el formulario
            if (editForm.checkValidity()) {
                console.log('Formulario válido, enviando...');
                
                // Mostrar notificación de envío
                showNotification('Actualizando el artículo...', 'info');
                
                // Usar FormData para enviar el formulario mediante fetch
                fetch(editForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    // Redirigir a adminControl.php
                    window.location.href = 'adminControl.php';
                })
                .catch(error => {
                    console.error('Error al enviar el formulario:', error);
                    showNotification('Error al actualizar el artículo: ' + error.message, 'error');
                });
            } else {
                showNotification('Por favor complete todos los campos requeridos', 'error');
                editForm.reportValidity();
            }
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
        confirmDeleteButton.addEventListener('click', async function() {
            const postId = this.getAttribute('data-id');
            
            try {
                const response = await fetch('eliminar_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${postId}`
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAllModals();
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Error al eliminar el artículo', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al eliminar el artículo', 'error');
            }
        });
    }
    
    // Event listener para confirmar eliminación de comentario
    const confirmDeleteCommentButton = document.getElementById('confirm-delete-comment-button');
    if (confirmDeleteCommentButton) {
        confirmDeleteCommentButton.addEventListener('click', async function() {
            const commentId = this.getAttribute('data-id');
            console.log('Confirmando eliminación de comentario:', commentId);
            
            try {
                const response = await fetch('eliminar_comentario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_comentario=${commentId}`
                });
                
                console.log('Respuesta recibida del servidor:', response.status);
                const responseText = await response.text();
                console.log('Texto de respuesta:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    console.error('Texto recibido:', responseText);
                    throw new Error('La respuesta no es un JSON válido');
                }
                
                console.log('Datos JSON:', data);
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAllModals();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error al eliminar comentario', 'error');
                }
            } catch (error) {
                console.error('Error en la operación:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        });
    }

    // Configuración de botones de cambio de rol
    const changeRoleButtons = document.querySelectorAll('.change-role-button');
    const modalChangeRole = document.getElementById('modal-change-role');
    const closeChangeRoleModal = document.getElementById('close-change-role-modal');
    const cancelChangeRole = document.getElementById('cancel-change-role');
    const confirmChangeRoleButton = document.getElementById('confirm-change-role-button');
    
    if (changeRoleButtons.length > 0) {
        changeRoleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const currentRole = this.dataset.currentRole;
                
                // Rellenar el formulario con los datos actuales
                document.getElementById('change-role-user-id').value = userId;
                
                // Seleccionar el rol actual en el dropdown
                const roleSelect = document.getElementById('new-role');
                for (let i = 0; i < roleSelect.options.length; i++) {
                    if (roleSelect.options[i].value.toLowerCase() === currentRole.toLowerCase()) {
                        roleSelect.selectedIndex = i;
                        break;
                    }
                }
                
                // Abrir el modal
                openModal(modalChangeRole);
            });
        });
    }
    
    // Cerrar modal de cambio de rol
    if (closeChangeRoleModal) {
        closeChangeRoleModal.addEventListener('click', closeAllModals);
    }
    
    if (cancelChangeRole) {
        cancelChangeRole.addEventListener('click', closeAllModals);
    }
    
    // Confirmar cambio de rol
    if (confirmChangeRoleButton) {
        confirmChangeRoleButton.addEventListener('click', async function() {
            const userId = document.getElementById('change-role-user-id').value;
            const newRole = document.getElementById('new-role').value;
            
            try {
                const response = await fetch('cambiar_rol.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_usuario: userId, rol: newRole })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message || 'Rol actualizado correctamente', 'success');
                    closeAllModals();
                    // Recargar la página para mostrar los cambios
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Error al actualizar el rol', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al actualizar el rol', 'error');
            }
        });
    }

    // Configurar botones de visualización de artículos
    const viewButtons = document.querySelectorAll('.view-button');
    if (viewButtons.length > 0) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.id;
                // Redirigir a la página de visualización del post
                window.open(`../views/post.php?id=${postId}`, '_blank');
            });
        });
    }

    // Configurar botones de archivar/desarchivar
    const archiveButtons = document.querySelectorAll('.archive-button');
    const unarchiveButtons = document.querySelectorAll('.unarchive-button');

    // Función común para manejar tanto archivar como desarchivar
    const handleArchiveAction = async (button) => {
        const postId = button.dataset.id;
        const action = button.classList.contains('archive-button') ? 'archive' : 'unarchive';
        
        try {
            const response = await fetch('archivar_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${postId}&action=${action}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                // Recargar la página para actualizar la lista
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al procesar la solicitud', 'error');
        }
    };

    // Agregar event listeners a los botones de archivar
    archiveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleArchiveAction(this);
        });
    });

    // Agregar event listeners a los botones de desarchivar
    unarchiveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleArchiveAction(this);
        });
    });

    // Configurar botones de filtro de artículos
    const showPublishedButton = document.getElementById('showPublished');
    const showArchivedButton = document.getElementById('showArchived');
    const publishedArticles = document.getElementById('published-articles');
    const archivedArticles = document.getElementById('archived-articles');

    if (showPublishedButton && showArchivedButton) {
        showPublishedButton.addEventListener('click', function() {
            showPublishedButton.classList.add('active');
            showArchivedButton.classList.remove('active');
            publishedArticles.style.display = 'block';
            archivedArticles.style.display = 'none';
        });

        showArchivedButton.addEventListener('click', function() {
            showArchivedButton.classList.add('active');
            showPublishedButton.classList.remove('active');
            archivedArticles.style.display = 'block';
            publishedArticles.style.display = 'none';
        });

        // Activar el botón de "Published" por defecto
        showPublishedButton.classList.add('active');
    }

    // Función para filtrar posts por estado
    document.querySelectorAll('.filter-button').forEach(button => {
        button.addEventListener('click', function() {
            const status = this.dataset.status;
            
            // Actualizar estado activo de los botones
            document.querySelectorAll('.filter-button').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filtrar los posts
            document.querySelectorAll('.post-row').forEach(row => {
                const postStatus = row.dataset.status;
                if (status === 'all' || postStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Activar el botón de "Publicados" por defecto
    const publicadoButton = document.querySelector('[data-status="publicado"]');
    if (publicadoButton) {
        publicadoButton.click();
    } else {
        console.log('Botón de estado "publicado" no encontrado');
    }

    // Más adelante en el código, justo antes de la configuración de los botones banUserButtons
    console.log('Verificando elementos en la página:');
    console.log('- Botones banear usuario:', document.querySelectorAll('.ban-user-button').length);
    console.log('- Botones eliminar comentario:', document.querySelectorAll('.delete-comment-button').length);
    
    // Configurar botones para cerrar el modal de confirmación de eliminar comentarios
    const closeDeleteCommentModal = document.getElementById('close-delete-comment-modal');
    const cancelDeleteComment = document.getElementById('cancel-delete-comment');
    
    if (closeDeleteCommentModal) {
        closeDeleteCommentModal.addEventListener('click', closeAllModals);
    }
    
    if (cancelDeleteComment) {
        cancelDeleteComment.addEventListener('click', closeAllModals);
    }
    
    // Forzar una comprobación manual de cada botón en la consola
    document.querySelectorAll('.ban-user-button').forEach((btn, index) => {
        console.log(`Botón ban-user #${index}:`, {
            elemento: btn,
            dataId: btn.dataset.id,
            clickHandler: btn.onclick,
            estaVisible: btn.offsetParent !== null
        });
    });
    
    document.querySelectorAll('.delete-comment-button').forEach((btn, index) => {
        console.log(`Botón delete-comment #${index}:`, {
            elemento: btn,
            dataId: btn.dataset.id,
            clickHandler: btn.onclick,
            estaVisible: btn.offsetParent !== null
        });
    });

    // Configurar botones de banear usuario usando un enfoque alternativo
    document.body.addEventListener('click', function(e) {
        // Verificar si el clic fue en un botón de banear usuario o en alguno de sus elementos hijos
        const banButton = e.target.closest('.ban-user-button');
        if (banButton) {
            e.preventDefault();
            console.log('Botón banear usuario clickeado mediante event delegation');
            
            const userId = banButton.dataset.id;
            console.log('ID de usuario a banear:', userId);
            
            if (!userId) {
                console.error('Error: No se encontró el ID de usuario');
                showNotification('Error: No se encontró el ID de usuario', 'error');
                return;
            }
            
            if (confirm('¿Estás seguro de que deseas banear a este usuario?')) {
                console.log('Confirmación aceptada, enviando solicitud...');
                fetch('banear_usuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_usuario=${userId}`
                })
                .then(response => {
                    console.log('Respuesta recibida del servidor:', response.status);
                    return response.text();
                })
                .then(responseText => {
                    console.log('Texto de respuesta:', responseText);
                    try {
                        return JSON.parse(responseText);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        console.error('Texto recibido:', responseText);
                        throw new Error('La respuesta no es un JSON válido');
                    }
                })
                .then(data => {
                    console.log('Datos JSON:', data);
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message || 'Error al banear usuario', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error en la operación:', error);
                    showNotification('Error: ' + error.message, 'error');
                });
            }
        }
        
        // Verificar si el clic fue en un botón de eliminar comentario
        const deleteButton = e.target.closest('.delete-comment-button');
        if (deleteButton) {
            e.preventDefault();
            console.log('Botón eliminar comentario clickeado mediante event delegation');
            
            const commentId = deleteButton.dataset.id;
            const commentContent = deleteButton.dataset.content;
            console.log('ID de comentario a eliminar:', commentId);
            
            if (!commentId) {
                console.error('Error: No se encontró el ID de comentario');
                showNotification('Error: No se encontró el ID de comentario', 'error');
                return;
            }
            
            // Mostrar el modal de confirmación
            const modalDeleteCommentConfirmation = document.getElementById('modal-delete-comment-confirmation');
            const confirmDeleteCommentButton = document.getElementById('confirm-delete-comment-button');
            
            // Llenar el modal con los datos
            document.getElementById('comment-delete-content').textContent = commentContent;
            confirmDeleteCommentButton.setAttribute('data-id', commentId);
            
            // Mostrar el modal
            openModal(modalDeleteCommentConfirmation);
        }
    });
});