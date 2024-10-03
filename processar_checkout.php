<?php
    session_start();

    $tempoExpiracao = 1800;
    if(isset($_SESSION["ultimo_acesso"])){
        $inatividade = time() - $_SESSION["ultimo_acesso"];

        if($inatividade > $tempoExpiracao){
            session_unset();
            session_destroy();
            header("Location: login.php?message=Session expired. Please log in again.");
            exit();
        }
    }
    $_SESSION["ultimo_acesso"] = time();

    $conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Verifica se o usuário está logado
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Certificar-se de que o usuário tem um ID na sessão
    $utilizador_id = $_SESSION['user']['id'] ?? null; // Verificação de existência e atribuição
    $nome_utilizador = $_SESSION['user']['nome'];
    if ($utilizador_id === null && $nome_utilizador === null) {
        die("Erro: Usuário não autenticado.");
    }

    $isAdmin = ($_SESSION['user']['tipo'] === 'admin'); // Verificar se o usuário é admin
    $compra_realizada = false;


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome = $_POST['nome'];
        $endereco = $_POST['endereco'];
        $metodo_pagamento = $_POST['pagamento'];
        $total = 0;
        $produtos_comprados = [];

        // Verificar e atualizar o estoque de cada item no carrinho
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { // Verificar se o carrinho está definido e não está vazio
            foreach ($_SESSION['cart'] as $item) {
                // Verificar se o 'id' e 'quantity' existem no item do carrinho
                if (isset($item['id']) && isset($item['quantity'])) {
                    $produto_id = $item['id'];
                    $quantidade_comprada = $item['quantity'];

                    // Verificar estoque disponível
                    $sql = "SELECT quantidade, preco, nome FROM produtos WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $produto_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $produto = $result->fetch_assoc();

                    if ($produto && $produto['quantidade'] >= $quantidade_comprada) {
                        // Atualizar a quantidade em estoque
                        $nova_quantidade = $produto['quantidade'] - $quantidade_comprada;
                        $sql = "UPDATE Produtos SET quantidade = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $nova_quantidade, $produto_id);
                        $stmt->execute();

                        // Calcular o total
                        $total += $produto['preco'] * $quantidade_comprada;

                        // Adicionar ao array de produtos comprados
                        $produtos_comprados[] = [
                            'nome' => $produto['nome'],
                            'quantidade' => $quantidade_comprada,
                            'preco' => $produto['preco']
                        ];
                    } else {
                        echo "Estoque insuficiente para o produto " . $produto['nome'];
                        exit();
                    }
                } else {
                    echo "Erro: Produto inválido no carrinho.";
                    exit();
                }
            }

            // Registrar a encomenda na base de dados
            $produtos_comprados_json = json_encode($produtos_comprados);
            
            $sql = "INSERT INTO encomendas (utilizador_id, nome, morada, produtos, preco_total, metodo_pagamento) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssds", $utilizador_id, $nome_utilizador, $endereco, $produtos_comprados_json, $total, $metodo_pagamento);
            
            if($stmt->execute()){
                // Limpar o carrinho
                unset($_SESSION['cart']);
                $compra_realizada = true;
            }else {
                echo "Erro ao registrar a compra: " . $stmt->error;
                exit();
            }
        } else {
            echo "Carrinho vazio. Adicione itens ao seu carrinho antes de finalizar a compra.";
        }

        $stmt->close();
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processamento de Checkout - ProPeformance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         .success-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo-container{
            background-color: #333; /* Fundo escuro para contraste */
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .success-container .logo-container img {
            max-width: 100%;
            height: auto;
        }
        .success-container h2 {
            color: #28a745;
            margin-top: 20px;
        }
        .success-container p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .btn-group-custom {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <?php if ($compra_realizada): ?>
            <div class="success-container">
                <div class="logo-container">
                    <img src="imagem/logo/Logotipo.png" alt="Logo da ProPerformance">
                </div>
                <h2>Compra Realizada com Sucesso!</h2>
                <p>Obrigado por comprar conosco! A sua encomenda foi processada com sucesso.</p>
                <div class="btn-group-custom">
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Voltar a Página Inicial</a>
                    <?php if($isAdmin): ?>
                        <a href="admin.php" class="btn btn-warning"><i class="fas fa-cogs"></i> Ir para Admin</a>
                    <?php else: ?>
                        <a href="pedidos.php" class="btn btn-info"><i class="fas fa-box"></i> Ver meus pedidos</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger text-center">
                Erro ao processar a compra. Por favor, tente novamente.
            </div>
        <?php endif; ?>
    </div>
    <script>
        let tempoExpiracao = 30 * 60 * 1000;

        function autoLogout(){
            alert("Sua sessão expirou devido à inatividade.");
            window.location.href = "logout.php";
        }

        let timer = setTimeout(autoLogout, tempoExpiracao);

        function resetTimer(){
            clearTimeout(timer);
            timer = setTimeout(autoLogout, tempoExpiracao);
        }

        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onkeypress = resetTimer;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>