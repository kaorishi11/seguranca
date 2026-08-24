<?php

session_start();

/*
|--------------------------------------------------------------------------
| CONFIGURAÇÃO DO BANCO
|--------------------------------------------------------------------------
*/

$host = "localhost";
$dbname = "seguranca";
$user = "root";
$password = "";

/*
|--------------------------------------------------------------------------
| CONEXÃO E CRIAÇÃO AUTOMÁTICA DO BANCO
|--------------------------------------------------------------------------
*/

try {

    // Primeiro conecta ao MySQL sem selecionar banco
    $pdoInicial = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $password
    );

    $pdoInicial->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Cria o banco caso não exista
    $pdoInicial->exec(
        "CREATE DATABASE IF NOT EXISTS `$dbname`
         CHARACTER SET utf8mb4
         COLLATE utf8mb4_unicode_ci"
    );

    // Conecta ao banco
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
    |--------------------------------------------------------------------------
    | CRIAÇÃO DA TABELA
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | USUÁRIO DE TESTE
    |--------------------------------------------------------------------------
    |
    | Login:
    | admin@teste.com
    |
    | Senha:
    | 1234
    |
    */

    $emailTeste = "admin@teste.com";

    $stmtTeste = $pdo->prepare("
        SELECT id
        FROM usuarios
        WHERE email = :email
        LIMIT 1
    ");

    $stmtTeste->execute([
        ":email" => $emailTeste
    ]);

    if (!$stmtTeste->fetch()) {

        $senhaTeste = password_hash(
            "1234",
            PASSWORD_DEFAULT
        );

        $stmtInserirTeste = $pdo->prepare("
            INSERT INTO usuarios
            (nome, email, senha)
            VALUES
            (:nome, :email, :senha)
        ");

        $stmtInserirTeste->execute([
            ":nome" => "Administrador",
            ":email" => $emailTeste,
            ":senha" => $senhaTeste
        ]);
    }

} catch (PDOException $e) {

    /*
     * Não mostramos o erro do banco para o usuário.
     * Isso evita exposição de informações internas.
     */

    die(
        "Não foi possível conectar ao sistema."
    );
}

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if (isset($_GET['logout'])) {

    $_SESSION = [];

    session_destroy();

    header(
        "Location: login.php?logout=sucesso"
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| PROCESSAMENTO DO LOGIN
|--------------------------------------------------------------------------
*/

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /*
     * trim() remove espaços desnecessários.
     */

    $email = trim(
        $_POST["email"] ?? ""
    );

    $senha = $_POST["senha"] ?? "";

    /*
     |--------------------------------------------------------------------------
     | VALIDAÇÃO DE CAMPOS VAZIOS
     |--------------------------------------------------------------------------
     */

    if ($email === "" || $senha === "") {

        $mensagem =
            "Preencha todos os campos.";

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

    else {

        /*
         |--------------------------------------------------------------------------
         | PREPARED STATEMENT
         |--------------------------------------------------------------------------
         |
         | Não concatenamos o e-mail diretamente no SQL.
         | Isso impede SQL Injection.
         |
         */

        $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                email,
                senha
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email
        ]);

        $usuario = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        /*
         |--------------------------------------------------------------------------
         | VERIFICAÇÃO DA SENHA
         |--------------------------------------------------------------------------
         */

        if (
            $usuario &&
            password_verify(
                $senha,
                $usuario["senha"]
            )
        ) {

            /*
             * Regenera o ID da sessão.
             * Ajuda contra Session Fixation.
             */

            session_regenerate_id(true);

            $_SESSION["usuario_id"] =
                $usuario["id"];

            $_SESSION["usuario_nome"] =
                $usuario["nome"];

            $_SESSION["usuario_email"] =
                $usuario["email"];

            /*
             * Redirecionamento para área protegida.
             */

            header(
                "Location: index.php"
            );

            exit;

        } else {

            /*
             * Não informamos se o e-mail existe.
             * Isso evita enumeração de usuários.
             */

            $mensagem =
                "E-mail ou senha inválidos.";

            $tipoMensagem = "error";
        }
    }
}

/*
|--------------------------------------------------------------------------
| MENSAGEM DE LOGOUT
|--------------------------------------------------------------------------
*/

if (
    isset($_GET["logout"]) &&
    $_GET["logout"] === "sucesso"
) {

    $mensagem =
        "Você saiu da sua conta.";

    $tipoMensagem = "success";
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

    <title>ACME Digital - Login</title>

    <!-- SweetAlert2 -->
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
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
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
            margin-bottom: 18px;
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
            Acesse sua conta
        </p>

        <form
            method="POST"
            id="form-login"
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

        <!-- Elemento utilizado pelo Selenium -->
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
| VALIDAÇÃO NO FRONTEND
|--------------------------------------------------------------------------
|
| Também validamos antes de enviar ao servidor.
| A validação do servidor continua sendo obrigatória.
|
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

        if (
            email === "" ||
            senha === ""
        ) {

            event.preventDefault();

            Swal.fire({

                icon: "warning",

                title:
                    "Campos obrigatórios",

                text:
                    "Preencha todos os campos antes de continuar.",

                confirmButtonText: "OK"

            });

            return;
        }

        /*
         * Validação simples de e-mail.
         */

        const emailValido =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (
            !emailValido.test(email)
        ) {

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

</script>

</body>

</html>