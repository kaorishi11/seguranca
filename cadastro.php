<?php

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

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
        INSERT INTO usuarios
        (nome, email, senha)
        VALUES
        ('$nome', '$email', '$senha')
    ";

    if ($conn->query($sql)) {

        /*
         * XSS INTENCIONAL
         */

        $mensagem = "
            <script>
                alert('Cadastro realizado para: $nome');
            </script>
        ";

    } else {

        $mensagem = "
            <script>
                alert('Erro no cadastro: {$conn->error}');
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cadastro - ACME Digital</title>

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
                #16a34a
            );

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cadastro-box {
            width: 420px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.3);
        }

        h1 {
            text-align: center;

            color: #166534;

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

            background: #16a34a;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background: #15803d;
        }

        .voltar {
            display: block;

            text-align: center;

            margin-top: 20px;

            color: #166534;
        }

    </style>

</head>

<body>

    <div class="cadastro-box">

        <h1>
            Criar conta
        </h1>

        <form method="POST">

            <label for="nome">
                Nome
            </label>

            <input
                type="text"
                id="nome"
                name="nome"
            >

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
                id="btn-cadastro"
            >
                Cadastrar
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

</html><?php

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

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
        INSERT INTO usuarios
        (nome, email, senha)
        VALUES
        ('$nome', '$email', '$senha')
    ";

    if ($conn->query($sql)) {

        /*
         * XSS INTENCIONAL
         */

        $mensagem = "
            <script>
                alert('Cadastro realizado para: $nome');
            </script>
        ";

    } else {

        $mensagem = "
            <script>
                alert('Erro no cadastro: {$conn->error}');
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cadastro - ACME Digital</title>

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
                #16a34a
            );

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cadastro-box {
            width: 420px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.3);
        }

        h1 {
            text-align: center;

            color: #166534;

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

            background: #16a34a;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background: #15803d;
        }

        .voltar {
            display: block;

            text-align: center;

            margin-top: 20px;

            color: #166534;
        }

    </style>

</head>

<body>

    <div class="cadastro-box">

        <h1>
            Criar conta
        </h1>

        <form method="POST">

            <label for="nome">
                Nome
            </label>

            <input
                type="text"
                id="nome"
                name="nome"
            >

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
                id="btn-cadastro"
            >
                Cadastrar
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