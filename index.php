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
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ACME Digital - Portal</title>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            width: 90%;
            max-width: 600px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            text-align: center;
        }

        h1 {
            color: #1e293b;
            margin-bottom: 15px;
        }

        p {
            color: #64748b;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 13px 25px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .logout {
            background: #dc2626;
            color: white;
        }

        .logout:hover {
            background: #b91c1c;
        }

    </style>

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