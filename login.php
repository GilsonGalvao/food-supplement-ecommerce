<?php
    session_start();
    $conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

    if($conn->connect_error){
        die("Erro: ".$conn->connect_error);
    }

    $error = "";

    //Verificar se o formulário foi enviado
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = htmlspecialchars(trim($_POST["username"]));
        $password = htmlspecialchars(trim($_POST["password"]));

        // verificar as credenciais do usuário
        $sql = "SELECT * FROM Utilizadores WHERE nome = ? AND senha = ?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
            $_SESSION["user"] = $user;

            if($user["tipo"] == "admin"){
                header("Location: admin.php");
            } else {
                header("Location: pedidos.php");
            }
            exit();
        } else{
            $error = "Nome do usuário ou senha incorretos.";
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
    <title>Login - ProPeformance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .login-container{
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0, 0.1);
            text-align: center;
        }
        .login-container .logo-wrapper {
            background-color: #333; /* Fundo escuro para contraste */
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .login-container .logo-wrapper img{
            width: 100%;
            max-width: 450px;
            height: auto;
        }
        .login-container h2{
            margin-bottom: 20px;
        }
        .login-container .btn{
            width: 100%;
        }
        .login-container a {
            display: block;
            margin-top: 10px;
        }
        body{
            background: #e9ecef;
        }
    </style>
    <script>
        // Limpar os campos de entrada ao carregar a página
        window.onload = function (){
            document.getElementbyId("username").value = "";
            document.getElementbyId("password").value = "";
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-wrapper">
                <img src="imagem/logo/Logotipo.png" alt="Logo da ProPeformance">
            </div>
            <h2>Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            <a href="recuperar_senha.php" class="text-secondary">Esqueceu sua senha?</a>
            <a href="index.php" class="btn btn-secondary mt-3"><i class="fas fa-home"></i> Voltar</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
