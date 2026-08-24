<?php

session_start();

/*
|--------------------------------------------------------------------------
| NÃO PERMITIR CACHE
|--------------------------------------------------------------------------
| Isso evita que o navegador mantenha a página protegida
| disponível depois do logout.
*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

/*
|--------------------------------------------------------------------------
| VERIFICAÇÃO DE LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| DADOS DO USUÁRIO
|--------------------------------------------------------------------------
*/

$nome = $_SESSION["usuario_nome"] ?? "Usuário";

$nomeSeguro = htmlspecialchars(
    $nome,
    ENT_QUOTES,
    "UTF-8"
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

    <meta
        http-equiv="Cache-Control"
        content="no-store, no-cache, must-revalidate"
    >

    <meta
        http-equiv="Pragma"
        content="no-cache"
    >

    <meta
        http-equiv="Expires"
        content="0"
    >

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <title>ACME Digital - Portal</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="container">

        <h1>ACME Digital</h1>

        <p>
            Bem-vindo,
            <strong><?= $nomeSeguro ?></strong>!
        </p>

        <p>
            Você está autenticado no portal.
        </p>

        <!--
        |--------------------------------------------------------------------------
        | LOGOUT
        |--------------------------------------------------------------------------
        | O logout agora é enviado para process_login.php
        | através de POST.
        -->

        <form
            method="POST"
            action="process_login.php"
            id="form-logout"
        >

            <input
                type="hidden"
                name="acao"
                value="logout"
            >

            <button
                type="submit"
                class="btn logout"
            >
                Sair
            </button>

        </form>

    </div>

<script>

/*
|--------------------------------------------------------------------------
| EVITAR EXIBIÇÃO DA PÁGINA PELO CACHE DO BOTÃO VOLTAR
|--------------------------------------------------------------------------
*/

window.addEventListener("pageshow", function(event) {

    if (event.persisted) {

        window.location.reload();

    }

});

</script>

</body>

</html>