<?php
session_start();
$conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];

    // Verificar o estoque disponível no banco de dados
    $sql = "SELECT quantidade FROM Produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produto = $result->fetch_assoc();
    
    if ($produto && $produto['quantidade'] >= $product_quantity) {
        // Criar o item do produto
        $product = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => $product_quantity,
        ];

        // Verificar se o carrinho já existe na sessão
        if (isset($_SESSION['cart'])) {
            // Se o produto já estiver no carrinho, atualizar a quantidade
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    if ($item['quantity'] + $product_quantity <= $produto['quantidade']) {
                        $item['quantity'] += $product_quantity;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente']);
                        exit;
                    }
                    $found = true;
                    break;
                }
            }
            // Se o produto não estiver no carrinho, adicionar
            if (!$found) {
                $_SESSION['cart'][] = $product;
            }
        } else {
            // Se o carrinho não existir, criar um novo
            $_SESSION['cart'] = [$product];
        }

        // Calcular a quantidade total de itens no carrinho
        $totalItensCarrinho = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItensCarrinho += $item['quantity'];
        }

        // Responder com um JSON contendo o total de itens
        echo json_encode(['success' => true, 'totalItensCarrinho' => $totalItensCarrinho]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}
$conn->close();
?>
