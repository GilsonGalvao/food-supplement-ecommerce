<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Remover item do carrinho
    if (isset($_POST["remover"])){
        $index = $_POST["remover"];

        //Verifica se o item existe no carrinho antes de removê-lo
        if (isset($_SESSION["cart"][$index])){
            unset($_SESSION["cart"][$index]); //remove o item do carrinho

            // Reindexa o array para manter a consistência
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
    }
    
    // Atualizar quantidades     
    if (isset($_POST["atualizar"]) && isset($_POST["quantidade"])){
        foreach ($_POST["quantidade"] as $index => $nova_quantidade){
            // Verifica se o item ainda existe no carrinho e se a nova quantidade é válida
            if (isset($_SESSION["cart"][$index]) && $nova_quantidade > 0){
                $_SESSION["cart"][$index]["quantity"] = (int)$nova_quantidade;
            }
        }
    }
}

// Redireciona de volta para a página do carrinho
header("Location: visualizar_carrinho.php");
exit();
?>