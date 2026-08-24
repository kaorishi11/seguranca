<?php

session_start();

/*
|--------------------------------------------------------------------------
| CONFIGURAÇÃO
|--------------------------------------------------------------------------
*/

$host = "localhost";
$dbname = "acme_digital";
$user = "root";
$password = "";

$mensagem = "";
$tipoMensagem = "";

/*
|--------------------------------------------------------------------------
| CONEXÃO COM BANCO
|--------------------------------------------------------------------------
*/

try {

    $pdoInicial = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $password
    );

    $pdoInicial->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    /*
     * Cria o banco automaticamente.
     */

    $pdoInicial->exec(
        "CREATE DATABASE IF NOT EXISTS `$dbname`
         CHARACTER SET utf8mb4
         COLLATE utf8mb4_unicode_ci"
    );

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    /*
     * Cria a tabela.
     */

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (

            id INT AUTO_INCREMENT PRIMARY KEY,

            nome VARCHAR(100) NOT NULL,

            email VARCHAR(150) NOT NULL UNIQUE,

            senha VARCHAR(255) NOT NULL,

            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP

        )
    ");

} catch (PDOException $e) {

    die(
        "Não foi possível conectar ao sistema."
    );
}

/*
|--------------------------------------------------------------------------
| CADASTRO
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim(
        $_POST["nome"] ?? ""
    );

    $email = trim(
        $_POST["email"] ?? ""
    );

    $senha = $_POST["senha"] ?? "";

    $confirmarSenha =
        $_POST["confirmarSenha"] ?? "";

    /*
     |--------------------------------------------------------------------------
     | CAMPOS VAZIOS
     |--------------------------------------------------------------------------
     */

    if (
        $nome === "" ||
        $email === "" ||
        $senha === "" ||
        $confirmarSenha === ""
    ) {

        $mensagem =
            "Preencha todos os campos.";

        $tipoMensagem = "warning";
    }

    /*
     |--------------------------------------------------------------------------
     | TAMANHO DO NOME
     |--------------------------------------------------------------------------
     */

    elseif (
        mb_strlen($nome) < 2 ||
        mb_strlen($nome) > 100
    ) {

        $mensagem =
            "O nome deve possuir entre 2 e 100 caracteres.";

        $tipoMensagem = "warning";
    }

    /*
     |--------------------------------------------------------------------------
     | VALIDAÇÃO DO E-MAIL
     |--------------------------------------------------------------------------
     */

    elseif (!filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )) {

        $mensagem =
            "Digite um e-mail válido.";

        $tipoMensagem = "warning";
    }

    /*
     |--------------------------------------------------------------------------
     | VALIDAÇÃO DA SENHA
     |--------------------------------------------------------------------------
     */

    elseif (strlen($senha) < 4) {

        $mensagem =
            "A senha deve possuir pelo menos 4 caracteres.";

        $tipoMensagem = "warning";
    }

    /*
     |--------------------------------------------------------------------------
     | CONFIRMAÇÃO
     |--------------------------------------------------------------------------
     */

    elseif ($senha !== $confirmarSenha) {

        $mensagem =
            "As senhas não coincidem.";

        $tipoMensagem = "warning";
    }

    else {

        /*
         |--------------------------------------------------------------------------
         | VERIFICA SE O E-MAIL JÁ EXISTE
         |--------------------------------------------------------------------------
         */

        $stmt = $pdo->prepare("
            SELECT id
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email
        ]);

        if ($stmt->fetch()) {

            $mensagem =
                "Este e-mail já está cadastrado.";

            $tipoMensagem = "error";

        } else {

            /*
             |--------------------------------------------------------------------------
             | HASH DA SENHA
             |--------------------------------------------------------------------------
             |
             | A senha original NÃO é armazenada no banco.
             |
             */

            $senhaHash = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );

            /*
             |--------------------------------------------------------------------------
             | INSERT SEGURO
             |--------------------------------------------------------------------------
             |
             | Prepared Statement impede SQL Injection.
             |
             */

            $stmt = $pdo->prepare("
                INSERT INTO usuarios
                (
                    nome,
                    email,
                    senha
                )
                VALUES
                (
                    :nome,
                    :email,
                    :senha
                )
            ");

            $stmt->execute([

                ":nome" => $nome,

                ":email" => $email,

                ":senha" => $senhaHash

            ]);

            $mensagem =
                "Cadastro realizado com sucesso!";

            $tipoMensagem = "success";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ACME Digital - Cadastro</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #0f172a,
                #2563eb
            );
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 90%;
            max-width: 450px;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow:
                0 15px 40px
                rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .subtitulo {
            text-align: center;
            color: #64748b;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 17px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
        }

        #mensagem {
            display: none;
        }

    </style>

</head>

<body>

    <div class="container">

        <h1>ACME Digital</h1>

        <p class="subtitulo">
            Crie sua conta
        </p>

        <form
            method="POST"
            id="form-cadastro"
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

        <!-- Elemento disponível para testes Selenium -->
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

    /*
     * Depois de um cadastro bem-sucedido,
     * direciona para o login.
     */

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
    .addEventListener(
        "submit",
        function(event) {

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
             * Campos vazios
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

                    title:
                        "Campos obrigatórios",

                    text:
                        "Preencha todos os campos.",

                    confirmButtonText: "OK"

                });

                return;
            }

            /*
             * E-mail
             */

            const emailValido =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (
                !emailValido.test(email)
            ) {

                event.preventDefault();

                Swal.fire({

                    icon: "warning",

                    title:
                        "E-mail inválido",

                    text:
                        "Digite um endereço de e-mail válido.",

                    confirmButtonText: "OK"

                });

                return;
            }

            /*
             * Senha
             */

            if (senha.length < 4) {

                event.preventDefault();

                Swal.fire({

                    icon: "warning",

                    title:
                        "Senha muito curta",

                    text:
                        "A senha deve possuir pelo menos 4 caracteres.",

                    confirmButtonText: "OK"

                });

                return;
            }

            /*
             * Confirmação
             */

            if (senha !== confirmarSenha) {

                event.preventDefault();

                Swal.fire({

                    icon: "warning",

                    title:
                        "Senhas diferentes",

                    text:
                        "As senhas digitadas não coincidem.",

                    confirmButtonText: "OK"

                });

            }

        }
    );

</script>

</body>

</html>