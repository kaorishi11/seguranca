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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css">
    <title>ACME Digital - Portal</title>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">

        <h1>ACME Digital</h1>
        <p class="subtitle">
            <span>✦</span> Portal de acesso da empresa <span>✦</span>
        </p>
        
        <div class="divider">Acesse sua conta</div>

        <div class="botoes">

            <a href="login.php" class="btn btn-entrar">
                <svg viewBox="0 0 24 24">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Entrar
            </a>

            <a href="cadastro.php" class="btn btn-cadastro">
                <svg viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                Criar conta
            </a>

        </div>

        <div class="footer">
            &copy; 2026 <a href="#">ACME Digital</a> — Todos os direitos reservados
        </div>

    </div>

</body>

</html>