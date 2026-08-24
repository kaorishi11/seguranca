<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ACME Digital</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #111827, #2563eb);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            width: 420px;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #1e3a8a;
            margin-bottom: 15px;
        }

        p {
            color: #555;
            margin-bottom: 30px;
        }

        .botoes {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        a {
            text-decoration: none;
            padding: 14px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            background: #2563eb;
        }

        a:hover {
            background: #1d4ed8;
        }

        .cadastro {
            background: #16a34a;
        }

        .cadastro:hover {
            background: #15803d;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>ACME Digital</h1>

        <p>
            Portal de acesso da empresa
        </p>

        <div class="botoes">

            <a href="login.php">
                Entrar
            </a>

            <a href="cadastro.php" class="cadastro">
                Criar conta
            </a>

        </div>

    </div>

</body>

</html>