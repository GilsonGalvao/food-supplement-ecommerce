<?php
    session_start();
    $conn = new mysqli("localhost","root","nova_senha","loja_suplementos");

    if($conn->connect_error){
        die("Erro na conexão: ".$conn->connect_error);
    }

    $id = $_GET['id'];

    $sql = "SELECT * FROM Produtos WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if(isset($_POST["edit_product"])){
        $nome = $_POST["nome"];
        $preco = $_POST["preco"];
        $quantidade = $_POST["quantidade"];

        $sql = "UPDATE Produtos SET nome='$nome', preco = '$preco', quantidade = '$quantidade' WHERE id = '$id'";
        if($conn->query($sql) === TRUE){
            echo "Produto atualizado com sucesso!";
            header("Location: admin.php");
        }else{
            echo "Erro: ".$conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">    
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Editar Produto</h1>
        <form method="POST" class="my-3">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" name="nome" class="form-control" value="<?php echo $row["nome"]; ?>" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="<?php echo $row["preco"]; ?>" required>
            </div>
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" name="quantidade" class="form-control" value="<?php echo $row["quantidade"]; ?>" required>
            </div>
            <button type="submit" name="edit_product" class="btn btn-primary">Atualizar Produto</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>