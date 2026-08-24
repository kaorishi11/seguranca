<?php
// Jamile de Oliveira Franquilim e Geovanna Kaori Shimada

session_start();

/*
|--------------------------------------------------------------------------
| NÃO PERMITIR CACHE
|--------------------------------------------------------------------------
*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

/*
|--------------------------------------------------------------------------
| SE JÁ ESTIVER LOGADO
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["usuario_id"])) {

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| MENSAGEM FLASH
|--------------------------------------------------------------------------
*/

$mensagem = $_SESSION["mensagem"] ?? "";
$tipoMensagem = $_SESSION["tipoMensagem"] ?? "";

/*
|--------------------------------------------------------------------------
| APAGA A MENSAGEM APÓS LER
|--------------------------------------------------------------------------
*/

unset($_SESSION["mensagem"]);
unset($_SESSION["tipoMensagem"]);

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
        href="assets/css/login.css"
    >

    <title>ACME Digital - Login</title>

    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
    ></script>

</head>

<body>

    <div class="container">

        <h1>ACME Digital</h1>

        <p class="subtitulo">
            Acesse sua conta
        </p>

        <!--
        |--------------------------------------------------------------------------
        | FORMULÁRIO
        |--------------------------------------------------------------------------
        | O formulário envia os dados para process_login.php.
        -->

        <form
            method="POST"
            action="process_login.php"
            id="form-login"
            autocomplete="off"
            novalidate
        >

            <label for="email">
                E-mail
            </label>

            <input
                type="email"
                id="email"
                name="email"
                autocomplete="email"
                maxlength="150"
                placeholder="Digite seu e-mail"
                required
            >

            <label for="senha">
                Senha
            </label>

            <input
                type="password"
                id="senha"
                name="senha"
                autocomplete="current-password"
                maxlength="100"
                placeholder="Digite sua senha"
                required
            >

            <button
                type="submit"
                id="btn-login"
            >
                Entrar
            </button>

        </form>

        <a
            href="cadastro.php"
            class="link"
        >
            Ainda não possui uma conta? Cadastre-se
        </a>

        <div id="mensagem"></div>

    </div>

<?php if ($mensagem !== ""): ?>

<script>

Swal.fire({

    icon:
        <?= json_encode($tipoMensagem) ?>,

    title:
        <?= json_encode(
            $tipoMensagem === "success"
                ? "Sucesso!"
                : "Atenção"
        ) ?>,

    text:
        <?= json_encode($mensagem) ?>,

    confirmButtonText: "OK"

});

</script>

<?php endif; ?>

<script>

/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DO LOGIN
|--------------------------------------------------------------------------
*/

document
    .getElementById("form-login")
    .addEventListener("submit", function(event) {

        const email =
            document
                .getElementById("email")
                .value
                .trim();

        const senha =
            document
                .getElementById("senha")
                .value;

        /*
        |--------------------------------------------------------------------------
        | CAMPOS VAZIOS
        |--------------------------------------------------------------------------
        */

        if (
            email === "" ||
            senha === ""
        ) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title: "Campos obrigatórios",

                text:
                    "Preencha todos os campos antes de continuar.",

                confirmButtonText: "OK"

            });

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | E-MAIL
        |--------------------------------------------------------------------------
        */

        const emailValido =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValido.test(email)) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title: "E-mail inválido",

                text:
                    "Digite um endereço de e-mail válido.",

                confirmButtonText: "OK"

            });

        }

    });

/*
|--------------------------------------------------------------------------
| EVITAR PÁGINA ANTIGA NO BOTÃO VOLTAR
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