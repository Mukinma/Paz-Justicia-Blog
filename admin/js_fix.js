/**
 * Script para corregir problemas con rutas de imágenes y funcionamiento del panel admin
 * Incluir este archivo en adminControl.php
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Cargando correcciones de JS para el panel de administración');
    
    // Agregar enlaces a las herramientas de diagnóstico
    agregarEnlacesHerramientas();
    
    // Arreglar problemas con rutas de imágenes en los modales
    arreglarRutasImagenes();
    
    // Mejorar manejo de errores en los formularios
    mejorarManejoErrores();
});

/**
 * Agrega enlaces a las herramientas de diagnóstico en el panel
 */
function agregarEnlacesHerramientas() {
    // Buscar un buen lugar para añadir los enlaces
    var container = document.querySelector('.main-content') || document.querySelector('main') || document.body;
    
    if (container) {
        var herramientasDiv = document.createElement('div');
        herramientasDiv.className = 'admin-tools';
        herramientasDiv.style.cssText = 'margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;';
        
        herramientasDiv.innerHTML = `
            <h4>Herramientas de Diagnóstico</h4>
            <ul>
                <li><a href="diagnostico.php" style="color: #007bff;">Diagnóstico del Sistema</a> - Verificar y corregir problemas</li>
                <li><a href="tabla_categorias.php" style="color: #007bff;">Administrar Tabla Categorías</a> - Ver y corregir datos de categorías</li>
                <li><a href="../database/ejecutar_sql.php" style="color: #007bff;">Actualizar Base de Datos</a> - Ejecutar scripts de corrección</li>
            </ul>
        `;
        
        // Añadir al final del contenido
        container.appendChild(herramientasDiv);
        console.log('Enlaces de herramientas añadidos');
    }
}

/**
 * Corrige problemas con rutas de imágenes en los modales
 */
function arreglarRutasImagenes() {
    // Buscar todos los elementos que manejan imágenes y arreglar sus rutas
    
    // Interceptar eventos de carga de imágenes en modales de categorías
    document.addEventListener('click', function(e) {
        // Si se hace clic en botones de edición de categorías
        if (e.target.matches('.edit-category-btn') || e.target.closest('.edit-category-btn')) {
            console.log('Botón de editar categoría detectado');
            
            // Esperar a que se abra el modal
            setTimeout(function() {
                var modal = document.getElementById('modal-edit-category');
                if (modal) {
                    console.log('Modal de edición de categoría detectado');
                    
                    // Arreglar rutas de imágenes
                    var imagenes = modal.querySelectorAll('img');
                    imagenes.forEach(function(img) {
                        if (img.src && !img.src.startsWith('http')) {
                            // Asegurar que las rutas relativas apunten correctamente
                            if (!img.src.includes('/PIP/')) {
                                var nuevaRuta = img.src.replace('assets/', '../assets/');
                                console.log('Corrigiendo ruta de imagen:', img.src, '->', nuevaRuta);
                                img.src = nuevaRuta;
                            }
                        }
                    });
                }
            }, 500);
        }
    });
    
    // Mejorar el manejo de imágenes en los formularios
    document.addEventListener('change', function(e) {
        // Si se selecciona un archivo de imagen
        if (e.target.type === 'file' && e.target.accept && e.target.accept.includes('image')) {
            console.log('Selección de archivo de imagen detectada');
            
            var fileInput = e.target;
            
            // Validar tamaño de archivo
            if (fileInput.files && fileInput.files[0]) {
                var fileSize = fileInput.files[0].size / 1024 / 1024; // en MB
                console.log('Tamaño del archivo:', fileSize.toFixed(2), 'MB');
                
                // Advertir si el archivo es demasiado grande
                if (fileSize > 2) {
                    var warningElement = document.createElement('div');
                    warningElement.className = 'file-warning';
                    warningElement.style.cssText = 'color: red; margin-top: 5px; font-size: 0.8em;';
                    warningElement.textContent = '⚠️ El archivo excede 2MB. Es posible que la carga falle o la imagen se redimensione.';
                    
                    // Eliminar advertencias anteriores
                    var parent = fileInput.parentElement;
                    parent.querySelectorAll('.file-warning').forEach(el => el.remove());
                    
                    // Añadir nueva advertencia
                    parent.appendChild(warningElement);
                }
                
                // Mostrar vista previa
                var preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.style.cssText = 'margin-top: 10px; max-width: 200px;';
                
                var img = document.createElement('img');
                img.style.cssText = 'max-width: 100%; border: 1px solid #ddd; padding: 3px;';
                
                var reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    
                    // Eliminar previsualizaciones anteriores
                    var parent = fileInput.parentElement;
                    parent.querySelectorAll('.image-preview').forEach(el => el.remove());
                    
                    // Añadir nueva previsualización
                    preview.appendChild(img);
                    parent.appendChild(preview);
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    });
}

/**
 * Mejora el manejo de errores en los formularios
 */
function mejorarManejoErrores() {
    // Interceptar envíos de formularios para validaciones adicionales
    document.addEventListener('submit', function(e) {
        var form = e.target;
        
        // Si es un formulario de categoría
        if (form.querySelector('[name="nombre"]') && (form.action.includes('editar_categoria.php') || form.action.includes('insertar_categoria.php'))) {
            console.log('Envío de formulario de categoría detectado');
            
            // Verificar campos obligatorios
            var nombre = form.querySelector('[name="nombre"]').value.trim();
            if (!nombre) {
                e.preventDefault();
                alert('El nombre de la categoría es obligatorio');
                return false;
            }
            
            // Verificar archivos de imagen
            var inputsImagen = form.querySelectorAll('input[type="file"]');
            for (var i = 0; i < inputsImagen.length; i++) {
                var input = inputsImagen[i];
                if (input.files && input.files[0]) {
                    var fileSize = input.files[0].size / 1024 / 1024; // en MB
                    var fileName = input.files[0].name;
                    
                    // Advertir sobre nombres de archivo problemáticos
                    if (fileName.includes(' ')) {
                        console.log('Nombre de archivo con espacios:', fileName);
                        var confirmar = confirm('El archivo "' + fileName + '" contiene espacios en el nombre, lo que puede causar problemas. ¿Desea continuar de todos modos?');
                        if (!confirmar) {
                            e.preventDefault();
                            return false;
                        }
                    }
                    
                    // Verificar tamaño de archivo
                    if (fileSize > 2) {
                        console.log('Archivo grande detectado:', fileName, fileSize.toFixed(2), 'MB');
                        var confirmar = confirm('El archivo "' + fileName + '" es de ' + fileSize.toFixed(2) + 'MB, lo que supera el límite de PHP (2MB). Es posible que se redimensione o que la carga falle. ¿Desea continuar?');
                        if (!confirmar) {
                            e.preventDefault();
                            return false;
                        }
                    }
                }
            }
            
            console.log('Formulario validado correctamente');
        }
    });
    
    // Mejorar manejo de respuestas AJAX
    var originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        this.addEventListener('load', function() {
            if (this.status >= 400) {
                console.error('Error en solicitud AJAX:', this.status, this.statusText);
                
                // Intentar analizar la respuesta JSON
                try {
                    var response = JSON.parse(this.responseText);
                    if (response && response.message) {
                        console.error('Mensaje de error:', response.message);
                        alert('Error: ' + response.message);
                    }
                } catch (e) {
                    // Si no es JSON, mostrar el texto tal cual
                    if (this.responseText) {
                        console.error('Respuesta de error:', this.responseText);
                    }
                }
            }
        });
        originalXHROpen.apply(this, arguments);
    };
} 