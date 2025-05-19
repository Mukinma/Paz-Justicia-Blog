<?php
/**
 * Enlace a la herramienta de diagnóstico
 * Incluir este archivo desde adminControl.php con require_once
 */
?>
<div class="admin-tools" style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
    <h4>Herramientas de Administración</h4>
    <ul>
        <li><a href="diagnostico.php" style="color: #007bff; text-decoration: underline;">Diagnóstico del Sistema</a> - Verificar y corregir problemas con la base de datos e imágenes</li>
        <li><a href="../database/ejecutar_sql.php" style="color: #007bff; text-decoration: underline;">Actualizar Estructura de Base de Datos</a> - Ejecutar script para actualizar la estructura de tablas</li>
    </ul>
</div>
<script>
// Añadir alerta para notificar al usuario sobre las herramientas disponibles
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si ya se mostró la alerta (usando almacenamiento local)
    if (!localStorage.getItem('diagnosticoAlertShown')) {
        setTimeout(function() {
            alert('Importante: Se han añadido herramientas de diagnóstico para resolver problemas con las imágenes. Usa el enlace "Diagnóstico del Sistema" al final de la página.');
            localStorage.setItem('diagnosticoAlertShown', 'true');
        }, 1500);
    }
});
</script> 