<?php
session_start(); // Inicia la sesión

// Destruir todas las variables de sesión
session_unset();
session_destroy();

// Redirigir al login
header('Location: index.php');
exit();
?>
