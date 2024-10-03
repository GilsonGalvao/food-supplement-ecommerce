<?php
    session_start();
    $conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
        $username = trim(htmlspecialchars($_POST['username']));
        $password = htmlspecialchars($_POST['password']);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $data_nascimento = htmlspecialchars($_POST['data_nascimento']);
        $morada = trim(htmlspecialchars($_POST['morada']));

        // Verificação de campos vazios
        if (empty($username) || empty($password) || empty($email) || empty($data_nascimento) || empty($morada)) {
            $error = "Todos os campos são obrigatórios!";
        } elseif (!filter_var($email, FILTER_SANITIZE_EMAIL)) {
            $error = "E-mail inválido.";
        } else {
            // Calcular a idade do utilizador
            $birthDate = new DateTime($data_nascimento);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
            if ($age < 18) {
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
                        header("Location: index.php");
                        exit();
                    } else {
                        $error = "Erro ao registrar usuário.";
                    }
                }
                $stmt->close();  
            }
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro de Usuário - ProPeformance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .auth-container {
        max-width: 500px;
        margin: 30px auto;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
    }
    .auth-container h2 {
        text-align: center;
    }
    .auth-container .btn {
        width: 100%;
    }
    body {
        background-color: #e9ecef;
    }
</style>
</head>
<body>
<div class="container my-5">
    <div class="auth-container">
        <h2 class="mb-4">Registrar-se</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="" onsubmit="return validateRegisterForm();">
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
    </div>
</div>

<script>
    function validateRegisterForm (){
        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const dataNascimento = document.getElementById("data_nascimento").value;
        const morada = document.getElementById("morada").value.trim();
        
        if (!username || !email || !password || !dataNascimento || !morada) {
            alert('Todos os campos devem ser preenchidos.');
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailRegex.test(email)){
            alert('Por favor, insira um e-mail válido.');
            return false;
        }

        const birthDate = new Date(dataNascimento);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age < 18){
            alert("Você deve ter pelo menos 18 anos para se registrar.");
            return false;
        }

        return true;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>