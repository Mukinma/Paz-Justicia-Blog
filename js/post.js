document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar el botón "Me gusta"
    const likeButton = document.querySelector('.like-button');
    if (likeButton) {
        likeButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            
            // Enviar solicitud AJAX al servidor
            fetch('../ajax/like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar UI
                    const likeCount = this.querySelector('.like-count');
                    likeCount.textContent = data.likes;
                    
                    if (data.liked) {
                        this.classList.add('liked');
                        this.querySelector('i').classList.replace('far', 'fas');
                    } else {
                        this.classList.remove('liked');
                        this.querySelector('i').classList.replace('fas', 'far');
                    }
                } else {
                    // Mostrar mensaje de error
                    showMessage(data.message || 'Debes iniciar sesión para dar me gusta');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Error al procesar la solicitud');
            });
        });
    }
    
    // Función para manejar el botón de compartir
    const shareButton = document.querySelector('.share-button');
    if (shareButton) {
        shareButton.addEventListener('click', function() {
            const url = window.location.href;
            
            // Copiar al portapapeles
            navigator.clipboard.writeText(url).then(function() {
                showMessage('Enlace copiado al portapapeles');
            }, function(err) {
                console.error('No se pudo copiar el texto: ', err);
            });
        });
    }
    
    // Función para mostrar mensajes
    function showMessage(text) {
        const message = document.getElementById('message');
        message.textContent = text;
        message.classList.add('show');
        
        setTimeout(() => {
            message.classList.remove('show');
        }, 3000);
    }
}); 