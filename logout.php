<?php
    session_start();
    session_unset();  // Remove todas as variáveis de sessão
    session_destroy(); // Destrói a sessão
    $_SESSION['message'] = "Logout realizado com sucesso.";
    header("Location: login.php");
    exit();
?>