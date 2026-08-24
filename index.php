<?php
session_start();

// Se não estiver logado, volta para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$nome = $_SESSION['usuario_nome'] ?? 'Usuário';

// Proteção contra XSS ao exibir o nome
$nomeSeguro = htmlspecialchars(
    $nome,
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<link rel="stylesheet" href="assets/css/cadastro.css">
<link rel="stylesheet" href="assets/css/login.css">
<link rel="stylesheet" href="assets/css/style.css">
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >
    <title>ACME Digital - Portal</title>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="container">

        <h1>ACME Digital</h1>

        <p>
            Bem-vindo, <strong><?= $nomeSeguro ?></strong>!
        </p>

        <p>
            Você está autenticado no portal.
        </p>

        <a
            href="login.php?logout=1"
            class="btn logout"
        >
            Sair
        </a>

    </div>

</body>

</html>