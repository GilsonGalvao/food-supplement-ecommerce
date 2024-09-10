<?php  
    session_start();
    $conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Verificar se o usuário está logado
    $isLoggedIn = isset($_SESSION['user']);
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
        $username = htmlspecialchars(trim($_POST['username']));
        $password = htmlspecialchars(trim($_POST['password']));
        $email = htmlspecialchars(trim($_POST['email']));
        $data_nascimento = $_POST['data_nascimento'];
        $morada = htmlspecialchars(trim($_POST['morada']));

        // Verificação de campos vazios
        if (empty($username) || empty($password) || empty($email) || empty($data_nascimento) || empty($morada)) {
            $error = "Todos os campos são obrigatórios!";
        } else {
            // Calcular a idade do utilizador
            $idade = date_diff(date_create($data_nascimento), date_create('now'))->y;
            if ($idade < 18) {
                $error = "Você deve ter 18 anos ou mais para se registrar.";
            } else {
                // Verificar se o usuário já existe
                $sql = "SELECT * FROM utilizadores WHERE nome = ?"; 
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error = "Usuário já cadastrado!";
                } else {
                    // Inserir novo usuário
                    $sql = "INSERT INTO utilizadores (nome, senha, tipo, email, data_nascimento, morada) VALUES (?, ?, 'cliente', ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $username, $password, $email, $data_nascimento, $morada);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $_SESSION["user"] = [
                            "id" => $stmt->insert_id,
                            "nome" => $username,
                            "tipo" => "cliente"
                        ];
                        header("Location: checkout.php");
                        exit();
                    } else {
                        $error = "Erro ao registrar usuário.";
                    }
                }
                $stmt->close();  
            }
        }
    }

    // Processar o login de usuários existentes
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $username = $_POST['login_username'];
        $password = $_POST['login_password'];

        $sql = "SELECT * FROM utilizadores WHERE nome = ? AND senha = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION["user"] = $user;
            header("Location: checkout.php");
            exit();
        } else {
            $error = "Nome do usuário ou senha incorreto!";
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
    <title>Finalizar compra - ProPeformance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .auth-container, .checkout-container {
            max-width: 500px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }
        .checkout-container {
            display: <?php echo $isLoggedIn ? "block" : "none"; ?>;
        }
        .auth-container h2, .checkout-container h2 {
            text-align: center;
        }
        .auth-container .btn, .checkout-container .btn {
            width: 100%;
        }
        body {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <?php if (!$isLoggedIn): ?>
            <div class="auth-container">
                <h2 class="mb-4">Registrar-se</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3"> 
                        <label for="username" class="form-label">Nome do Usuário:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3"> 
                        <label for="password" class="form-label">Senha:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3"> 
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3"> 
                        <label for="data_nascimento" class="form-label">Data de Nascimento:</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="mb-3"> 
                        <label for="morada" class="form-label">Morada:</label>
                        <input type="text" class="form-control" id="morada" name="morada" required>
                    </div>
                    <button type="submit" name="register" id="register" class="btn btn-success">Registrar-se</button>
                </form>
                <hr>
                <h2 class="mb-4">Login</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="login_username" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="login_username" name="login_username" required>
                    </div>
                    <div class="mb-3">
                        <label for="login_password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="login_password" name="login_password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Entrar</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="checkout-container">
            <h2 class="mb-4">Finalizar Compra</h2>
            <form method="POST" action="processar_checkout.php"> <!-- Enviar para processar_checkout.php -->
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
                <div class="mb-3">
                    <label for="pagamento" class="form-label">Método de Pagamento</label>
                    <select class="form-select" name="pagamento" id="pagamento" required>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Boleto Bancário">Boleto Bancário</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Confirmar Compra</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

