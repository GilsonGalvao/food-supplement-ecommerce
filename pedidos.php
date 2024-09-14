<?php 
    session_start();
    $conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

    if ($conn->connect_error){
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Verifica se o usuário está logado e é um cliente
    if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'cliente') {
        header("Location: login.php");
        exit();
    }

    $utilizador_id = $_SESSION['user']['id'];
    $nome_utilizador = $_SESSION['user']['nome']; // Nome do usuário logado
    
    //cancelar pedido
    if (isset($_GET["cancel"]) && !empty($_GET["cancel"])){
        $pedido_id = $_GET["cancel"];

        //verificar se o pedido está com o status em "Processo de separação"
        $sql = "SELECT status FROM encomendas WHERE id = ? and utilizador_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $pedido_id, $utilizador_id);
        $stmt->execute();
        $results = $stmt->get_result();
        $encomenda = $results->fetch_assoc();
        
        if($encomenda && $encomenda["status"] == "Produtos em processo de separação"){
            $sql = "UPDATE encomendas SET status = 'Cancelada' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $pedido_id);
            $stmt->execute();
            echo "Pedido cancelado com sucesso!";
        } else {
            echo "O pedido não pode ser cancelado.";
        }
    }

    // Buscar os pedidos do cliente logado
    $sql = "SELECT * FROM encomendas WHERE utilizador_id = ? ORDER BY data_encomenda DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $utilizador_id); // Correção: Usar apenas $utilizador_id
    $stmt->execute();
    $results = $stmt->get_result(); // Corrigido: Atribuir a variável correta
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus pedidos - ProPeformance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }
        .order-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-container p {
            text-align: center;
        }
        .logo-container{
            background-color: #333; /* Fundo escuro para contraste */
            max-width: 800px;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 40px;
            text-align: center;
        }
        .logo-container img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-info'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
    ?>
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Página Principal</a>    
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
    <div class="container my-5">
        <div class="order-container">
            <div class="logo-container">
                <img src="imagem/logo/Logotipo.png" alt="Logo da ProPerformance">
            </div>
            
            <h2>Meus pedidos</h2>
            <p>Bem-vindo, <?php echo htmlspecialchars($nome_utilizador); ?>! Aqui estão seus pedidos:</p>
            <?php if ($results && $results->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Endereço</th>
                            <th>Produtos</th>
                            <th>Preço Total</th>
                            <th>Método de Pagamento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['morada']; ?></td>
                                <td>
                                    <?php 
                                    $produtos = json_decode($row["produtos"], true);
                                    if ($produtos) : ?>
                                    <ul class="product-list">
                                        <?php foreach($produtos as $produto): ?>
                                            <li><?php echo htmlspecialchars($produto['nome']); ?> - Quantidade: <?php echo $produto["quantidade"]; ?> - Preço: € <?php echo number_format($produto["preco"], 2); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                        Nenhum produto encontrado.
                                    <?php endif; ?>
                                </td>
                                <td>€ <?php echo number_format($row['preco_total'], 2); ?></td>
                                <td><?php echo $row['metodo_pagamento']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td>
                                    <?php if ($row['status'] == 'Produtos em processo de separação'): ?>
                                        <a href="?cancel=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Cancelar Pedido</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum pedido encontrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS e Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
