<?php
    session_start();
    $conn = new mysqli("localhost", "root", "nova_senha", "loja_suplementos");

    if($conn->connect_error){
        die("Erro na conexão: ".$conn->connect_error);
    }

    // Função para calcular a quantidade total de itens no carrinho
    function calcularTotalItensCarrinho (){
        $totalItens = 0;

        if (isset($_SESSION["cart"]) && $_SESSION["cart"] !== null){
            foreach ($_SESSION["cart"] as $item){
                $totalItens += $item["quantity"];
            }
        }
        return $totalItens;
    }

    $totalItensCarrinho = calcularTotalItensCarrinho();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Description" content="Loja de suplementos online">
    <meta name="keywords" content="Suplementos online, suplementos para mulheres, comprar suplementos,loja de suplementos, suplementos esportivos, suplementos alimentares, suplementos para ganho de massa muscular, proteínas em pó para atletas, suplementos pré-treino, whey protein, creatina, suplementos naturais, suplementos veganos, multivitamínicos, BCAA, ômega 3, Suplementos em Porto">
    <meta name="author" content="Gilson Galvão">
    <title>ProPeformance - Online Supplement Store</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .product-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-card img{
            width: 100%;
            height: 300px;
            object-fit:contain;
        }
        .product-card .card-body{
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .carousel-inner img {
            width: 100%;
            height: 300px; /* Definir uma altura fixa para as imagens do carrossel */
            object-fit: contain; /* Manter a proporção da imagem e preencher o espaço */
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black; /* Tornar os ícones visíveis */
        }
        .card-body {
            flex: 1;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 text-center text-md-start">          
                    <img src="imagem/logo/Logotipo.png" alt="Logo da empresa" width="200" height="100" class="me-3">  
                </div>
                <div class="col-12 col-md-4 text-center">
                    <h1 class="mb-0">Seu suplemento está aqui!</h1>
                </div>
                <div class="col-12 col-md-4 text-center text-md-end mt-3 mt-md-0">
                    <a href="login.php" class="btn btn-light me-2">
                        <i class="fas fa-user"></i> Login
                    </a>
                    <a href="visualizar_carrinho.php" class="btn btn-light">
                        <i class="fas fa-shopping-cart"></i> Carrinho (<span id="cart-count"><?php echo $totalItensCarrinho; ?></span>)
                    </a>
                </div> 
            </div>          
        </div>
    </header>

    <main class="container my-5">
        <h2 class="text-center mb-4">Produtos Disponíveis</h2>
        <div class="row">
            <?php 
            $sql = "SELECT * FROM Produtos";
            $result = $conn->query($sql);
            
            while($row = $result->fetch_assoc()){
                echo 
                "<div class='col-md-4 mb-4'>
                    <div class='card product-card'>";
                    $imagens = explode(",", $row["imagens"]);
                    if (count($imagens) > 1){
                        echo "<div id='carousel" . $row['id'] . "' class='carousel slide' data-bs-ride='carousel'>";
                        echo "<div class='carousel-inner'>";

                        foreach ($imagens as $index => $imagem) {
                            $active = $index === 0 ? "active" : "";
                            echo "<div class='carousel-item " . $active . "'>";
                            echo "<img src='imagem/produtos/" . $imagem . "' class='d-block w-100' alt='Imagem do Produto'>";
                            echo "</div>";                        
                        }

                        echo "</div>"; // Fechar carousel-inner
                        echo 
                        '<button class="carousel-control-prev" type="button" data-bs-target="#carousel' . $row['id'] . '" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel' . $row['id'] . '" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                        </button>
                        </div>'; // Fechar carousel
                    } else {
                        echo '<img src="imagem/produtos/' . $imagens[0] . '" class="card-img-top" alt="Imagem do Produto" style="height: 300px; object-fit: contain;">';
                    }
                    echo    "<div class='card-body'>
                            <h5 class='card-title'>{$row['nome']}</h5>
                            <p class='card-text'>Preço: € {$row['preco']}</p>
                            <p class='card-text'>Quantidade disponível: {$row['quantidade']}</p>
                            <form method='POST' onsubmit='return adicionarAoCarrinho(this);'>
                                <input type='hidden' name='product_id' value='{$row['id']}'>
                                <input type='hidden' name='product_name' value='{$row['nome']}'>
                                <input type='hidden' name='product_price' value='{$row['preco']}'>
                                <input type='hidden' name='product_quantity' value='1'> 
                                <button type='submit' class='btn btn-primary'>Adicionar ao Carrinho</button>
                            </form>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
    </main>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start align-items-center mb-3 mb-md-0">
                    <img src="imagem/logo/Logotipo.png" alt="Logo da empresa" width="300" height="150" class="me-2 mb-4">
                    <p class="mb-0">&copy; 2024 ProPerformance | Todos os direitos reservados</p>
                </div>
                <div class="col-md-3 text-center text-md-start mb-3 mb-md-0">
                    <h5>Contatos</h5>
                    <p>Email: contato@properformance.com</p>
                    <p>Telefone: +351 912 345 678</p>
                    <h5>Endereço</h5>
                    <p>Rua Exemplo, 123</p>
                    <p>Maia, Porto, Portugal</p>
                </div>
                <div class="col-md-3 text-center text-md-start">
                    <h5>Formas de Pagamento</h5>
                    <p><i class="fas fa-credit-card"></i> Cartão de Crédito</p>
                    <p><i class="fas fa-credit-card"></i> Cartão de Débito</p>
                    <p><i class="fas fa-money-bill-wave"></i> Boleto Bancário</p>
                    <p><i class="fab fa-paypal"></i> PayPal</p>
                </div>
            </div>
        </div>
    </footer>
   
    <script>
        function adicionarAoCarrinho(form) {
                // Impede o envio normal do formulário
                event.preventDefault();

                // Cria um objeto FormData a partir do formulário
                var formData = new FormData(form);

                // Envia os dados via AJAX
                fetch('adicionar_ao_carrinho.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza o número de itens no carrinho
                        document.getElementById('cart-count').textContent = data.totalItensCarrinho;
                        alert('Produto adicionado ao carrinho!');
                    } else {
                        alert('Erro ao adicionar produto ao carrinho.');
                    }
                })
                .catch(error => console.error('Erro:', error));

                return false; // Impede o envio normal do formulário
        }
    </script>
    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>