<?php
    session_start();
    $conn = new mysqli("localhost","root","nova_senha", "loja_suplementos");

    if($conn->connect_error){
        die("Erro de conexão: ".$conn->connect_error);
    }
    // Verifica se o usuário está logado como administrador
    if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin') {
        header("Location: login.php");
        exit();
    }

    $target_dir = "imagem/produtos/";  
    //adicionar os produtos
    if(isset($_POST["add_product"])){
        $nome = $_POST["nome"];
        $preco = $_POST["preco"];
        $quantidade = $_POST["quantidade"];
        $imagens = [];
        
        // Verificar se o campo de imagens está definido e se há imagens para processar
        if(isset($_FILES["imagens"]) && $_FILES["imagens"]["error"][0] == 0){
            //PROCESSAR UPLOAD DA IMAGEM DE CADA PRODUTO
            foreach ($_FILES["imagens"]["name"] as $key => $image_name) {
                //verificar se o arquivo é uma imagem válida
                $image_file_type = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                $valid_extensions = ["jpg","jpeg","png","gif"];

                if(in_array($image_file_type, $valid_extensions)){
                    $new_image_name = uniqid() . "." . $image_file_type;
    
                    // Caminho completo para salvar a imagem
                    $target_file = $target_dir . $new_image_name;
    
                    // Mover o arquivo para o diretório de uploads
                    if(move_uploaded_file($_FILES["imagens"]["tmp_name"][$key],$target_file)){
                        $imagens[]= $new_image_name;
                    }
                }
            }
            //transformar array de imagens em uma string separada por vírgulas
            if(!empty($imagens)){
                $imagens_str = implode(",", $imagens);
            }else{
                $imagens_str = '';
            }
        }else {
            $imagens_str = ''; // Caso não haja imagens ou o upload falhe
        }    
        //inserir os dados no banco de dados (incluindo as imagens)
        $sql = "INSERT INTO Produtos (nome, preco, quantidade, imagens) VALUES ('$nome','$preco','$quantidade','$imagens_str')";
        if ($conn->query($sql) === TRUE){
            echo "Produto adicionado com sucesso!";
            header("Location: admin.php");
            exit();
        }else{
            echo "Erro: ".$conn->error;
        }
    }

    //Excluir Produto
    if (isset($_GET["delete"])){
        $id = $_GET["delete"];
        //buscar imagens dos produtos antes de deletar
        $sql = "SELECT imagens FROM Produtos WHERE id='$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $imagens = explode(",",$row["imagens"]);

        //deletar as imagens do servidor
        foreach($imagens as $imagem){
            $file_path = $target_dir . $imagem;
            if (!empty($imagem) && file_exists($file_path) && is_file($file_path)){
                unlink($file_path);
            }
        }
        //deletar o produto do banco de dados
        $sql = "DELETE FROM Produtos WHERE id = '$id'";
        if($conn->query($sql) === TRUE){
            echo "Produto excluído com sucesso!";
            header("Location: admin.php");
            exit();
        }else{
            echo "Erro: ".$conn->error;
        }
    }
    //Atualizar status da encomenda
    if (isset($_POST["update_status"])){
        $pedido_id = $_POST["pedido_id"];
        $novo_status = $_POST["novo_status"];

        $sql = "UPDATE encomendas SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_status, $pedido_id);
        
        if ($stmt->execute()){
            echo "Status do pedido atualizado com sucesso!";
            header ("Location: admin.php");
            exit();
        } else{
            echo "Erro ao atualizar o status: ". $stmt->error;
        }
        $stmt->close();
    }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Administração</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            margin-top: 30px;
        }
        .table img {
            max-width: 50px;
            max-height: 50px;
        }
        .btn-logout {
            float: right;
        }
        .table {
            margin-top: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .form-inline {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-inline select, .form-inline button {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container admin-container">
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1>Área do Administrador</h1>
            <a href="logout.php" class="btn btn-danger btn-logout">Sair</a>
        </div>
        <h2 class="mt-5">Cadastrar um novo produto</h2>
        <form method="POST" enctype="multipart/form-data" class="my-3">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" name="preco" step="0.01" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" name="quantidade" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagens do produto</label>
                <input type="file" class="form-control" name="imagens[]" multiple>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Adicionar Produto</button>
        </form>

        <h2 class="mt-5">Produtos Cadastrados</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Imagens</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM Produtos";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()){
                        echo 
                            "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['nome']}</td>
                                <td>{$row['preco']}</td>
                                <td>{$row['quantidade']}</td>
                                <td>";
                                    // Exibir as imagens associadas ao produto
                                    $imagens = explode(",",$row["imagens"]); 
                                    foreach ($imagens as $imagem){
                                        echo "<img src='imagem/produtos/$imagem' alt='Imagem do Produto' width='50' height='50' class='me-2'>";
                                    }

                                    echo
                                        "   </td>
                                            <td>
                                                <a href='admin_edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Editar</a>
                                                <a href='admin.php?delete={$row['id']}' class='btn btn-danger btn-sm'>Excluir</a>
                                            </td>
                                        </tr>";
                    }
                ?>
            </tbody>
        </table>

        <h2 class="mt-5">Gerenciar Encomendas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID do Pedido</th>
                    <th>Nome do Cliente</th>
                    <th>Endereço</th>
                    <th>Produtos</th>
                    <th>Preço Total</th>
                    <th>Método de Pagamento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT e.*, u.nome as cliente_nome FROM encomendas e JOIN utilizadores u ON e.utilizador_id = u.id ORDER BY e.data_encomenda DESC";
                    $result = $conn->query($sql);

                    while ($row = $result->fetch_assoc()){
                        echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['morada']}</td>
                        <td>";
                
                    // Decodificar o JSON e exibir produtos em formato legível
                    $produtos = json_decode($row["produtos"], true);
                    if ($produtos) {
                        echo "<ul class='product-list'>";
                        foreach ($produtos as $produto) {
                            echo "<li>" . htmlspecialchars($produto['nome']) . " - Quantidade: " . $produto["quantidade"] . " - Preço: € " . number_format($produto["preco"], 2) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "Nenhum produto encontrado.";
                    }

                    echo "</td>
                            <td>€ " . number_format($row['preco_total'], 2) . "</td>
                            <td>{$row['metodo_pagamento']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <form method='POST' class='form-inline'>
                                    <input type='hidden' name='pedido_id' value='{$row['id']}'>
                                    <select name='status' class='form-select form-select-sm'>
                                        <option value='Produtos em processo de separação'>Produtos em processo de separação</option>
                                        <option value='Enviado'>Enviado</option>
                                        <option value='Entregue'>Entregue</option>
                                        <option value='Cancelada'>Cancelada</option>
                                    </select>
                                    <button type='submit' name='update_status' class='btn btn-sm btn-primary'>Atualizar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>