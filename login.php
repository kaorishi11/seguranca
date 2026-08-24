<?php

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    /*
     * VULNERABILIDADE:
     * Os dados do usuário são colocados diretamente
     * dentro da consulta SQL.
     */

    $conn = new mysqli(
        "localhost",
        "root",
        "",
        "acme_digital"
    );

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    /*
     * SQL INJECTION INTENCIONAL
     */
    $sql = "
        SELECT *
        FROM usuarios
        WHERE email = '$email'
        AND senha = '$senha'
    ";

    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        /*
         * XSS INTENCIONAL:
         * O nome/email recebido é colocado diretamente
         * na página sem tratamento.
         */

        $mensagem = "
            <script>
                alert('Login realizado com sucesso! Bem-vindo, {$usuario['nome']}');
            </script>
        ";

    } else {

        /*
         * XSS INTENCIONAL:
         * O email é exibido diretamente.
         */

        $mensagem = "
            <script>
                alert('Login inválido para: $email');
            </script>
        ";
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - ACME Digital</title>

    <style>

        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(
                135deg,
                #111827,
                #2563eb
            );

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 400px;
            background: white;
            padding: 35px;
            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.3);
        }

        h1 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 20px;

            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 14px;

            border: none;
            border-radius: 7px;

            background: #2563eb;
            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .voltar {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2563eb;
        }

    </style>

</head>

<body>

    <div class="login-box">

        <h1>Login</h1>

        <form method="POST">

            <label for="email">
                E-mail
            </label>

            <input
                type="text"
                id="email"
                name="email"
            >

            <label for="senha">
                Senha
            </label>

            <input
                type="password"
                id="senha"
                name="senha"
            >

            <button
                type="submit"
                id="btn-login"
            >
                Entrar
            </button>

        </form>

        <?php echo $mensagem; ?>

        <a
            href="index.php"
            class="voltar"
        >
            Voltar
        </a>

    </div>

</body>

</html>