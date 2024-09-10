<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Description" content="Visualize os itens do seu carrinho de compras na ProPerformance - Loja de Suplementos Online">
    <meta name="keywords" content="carrinho de compras, suplementos online, loja de suplementos, whey protein, creatina, multivitamínicos, ProPerformance">
    <meta name="author" content="Gilson Galvão">
    <title>Carrinho de compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .cart-table th, .cart-table td {
            text-align: center;
            vertical-align: middle;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Carrinho de Compras</h2>
        <form method="POST" action="atualizar_carrinho.php">
            <table class="table table-bordered cart-table">
                <thead class="table-light">
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION["cart"])): ?>
                        <?php $total_geral = 0; ?>
                        <?php foreach($_SESSION["cart"] as $index => $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item["name"]); ?></td>
                                <td>€ <?php echo number_format($item["price"], 2); ?></td>
                                <td>
                                    <input type="number" name="quantidade[<?php echo $index; ?>]" value="<?php echo $item["quantity"]; ?>" min="1" class="form-control">
                                </td>
                                <td><?php echo number_format($item["price"] * $item["quantity"], 2); ?></td>
                                <td>
                                    <button type="submit" name="remover" value="<?php echo $index; ?>" class="btn btn-danger btn-sm">Remover</button>
                                </td>
                            </tr>
                            <?php $total_geral += $item["price"] * $item["quantity"]; ?>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3" class="text-end">Total Geral:</td>
                            <td colspan="2">€ <?php echo number_format($total_geral, 2); ?></td>
                        </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">O carrinho está vazio.</td>
                            </tr>
                        <?php endif; ?>
                </tbody>
            </table>
            <div class="text-end">
                <button type="submit" name="atualizar" class="btn btn-warning">Atualizar Carrinho</button>
                <a href="index.php" class="btn btn-primary">Continuar Comprando</a>
                <?php if(!empty($_SESSION["cart"])): ?>
                    <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>