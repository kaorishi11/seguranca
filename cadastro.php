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
        href="assets/css/cadastro.css"
    >

    <title>ACME Digital - Cadastro</title>

    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
    ></script>

</head>

<body>

    <div class="container">

        <h1>ACME Digital</h1>

        <p class="subtitulo">
            Crie sua conta
        </p>

        <form
            method="POST"
            action="process_register.php"
            id="form-cadastro"
            autocomplete="off"
            novalidate
        >

            <label for="nome">
                Nome
            </label>

            <input
                type="text"
                id="nome"
                name="nome"
                maxlength="100"
                autocomplete="name"
                placeholder="Digite seu nome"
                required
            >

            <label for="email">
                E-mail
            </label>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                autocomplete="email"
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
                maxlength="100"
                autocomplete="new-password"
                placeholder="Digite sua senha"
                required
            >

            <label for="confirmarSenha">
                Confirmar senha
            </label>

            <input
                type="password"
                id="confirmarSenha"
                name="confirmarSenha"
                maxlength="100"
                autocomplete="new-password"
                placeholder="Digite a senha novamente"
                required
            >

            <button
                type="submit"
                id="btn-cadastro"
            >
                Criar conta
            </button>

        </form>

        <a
            href="login.php"
            class="link"
        >
            Já possui uma conta? Entrar
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

}).then(() => {

    <?php if ($tipoMensagem === "success"): ?>

    window.location.href = "login.php";

    <?php endif; ?>

});

</script>

<?php endif; ?>

<script>

/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DO CADASTRO
|--------------------------------------------------------------------------
*/

document
    .getElementById("form-cadastro")
    .addEventListener("submit", function(event) {

        const nome =
            document
                .getElementById("nome")
                .value
                .trim();

        const email =
            document
                .getElementById("email")
                .value
                .trim();

        const senha =
            document
                .getElementById("senha")
                .value;

        const confirmarSenha =
            document
                .getElementById("confirmarSenha")
                .value;

        /*
        |--------------------------------------------------------------------------
        | CAMPOS VAZIOS
        |--------------------------------------------------------------------------
        */

        if (
            nome === "" ||
            email === "" ||
            senha === "" ||
            confirmarSenha === ""
        ) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title: "Campos obrigatórios",

                text:
                    "Preencha todos os campos.",

                confirmButtonText: "OK"

            });

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NOME
        |--------------------------------------------------------------------------
        */

        if (
            nome.length < 2 ||
            nome.length > 100
        ) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title: "Nome inválido",

                text:
                    "O nome deve possuir entre 2 e 100 caracteres.",

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

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SENHA
        |--------------------------------------------------------------------------
        */

        if (senha.length < 4) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title: "Senha muito curta",

                text:
                    "A senha deve possuir pelo menos 4 caracteres.",

                confirmButtonText: "OK"

            });

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CONFIRMAÇÃO
        |--------------------------------------------------------------------------
        */

        if (senha !== confirmarSenha) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title: "Senhas diferentes",

                text:
                    "As senhas digitadas não coincidem.",

                confirmButtonText: "OK"

            });

        }

    });

/*
|--------------------------------------------------------------------------
| EVITAR CACHE AO VOLTAR
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