<?php
/**
 * Archivo para incluir desde adminControl.php
 * Añade el script de corrección JS y la alerta de diagnóstico
 */
?>
<!-- Script para corregir problemas con rutas de imágenes -->
<script src="js_fix.js"></script>

<!-- Alerta de diagnóstico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si ya se mostró la alerta (usando almacenamiento local)
    if (!localStorage.getItem('diagnosticoAlertShown')) {
        setTimeout(function() {
            alert('Importante: Se han añadido herramientas de diagnóstico para resolver problemas con las imágenes. Las encontrarás al final de la página.');
            localStorage.setItem('diagnosticoAlertShown', 'true');
        }, 1500);
    }
});
</script> 