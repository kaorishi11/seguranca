<?php

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
| CONFIGURAÇÃO DO BANCO
|--------------------------------------------------------------------------
*/

$host = "localhost";
$dbname = "seguranca";
$user = "root";
$password = "";

/*
|--------------------------------------------------------------------------
| FUNÇÃO DE MENSAGEM
|--------------------------------------------------------------------------
*/

function redirecionarComMensagem(
    string $mensagem,
    string $tipo = "error"
): void {

    $_SESSION["mensagem"] = $mensagem;
    $_SESSION["tipoMensagem"] = $tipo;

    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| PERMITIR SOMENTE POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CONEXÃO
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | CONECTA AO MYSQL
    |--------------------------------------------------------------------------
    */

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
    |--------------------------------------------------------------------------
    | CRIA BANCO
    |--------------------------------------------------------------------------
    */

    $pdoInicial->exec(
        "CREATE DATABASE IF NOT EXISTS `$dbname`
         CHARACTER SET utf8mb4
         COLLATE utf8mb4_unicode_ci"
    );

    /*
    |--------------------------------------------------------------------------
    | CONECTA AO BANCO
    |--------------------------------------------------------------------------
    */

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
    | CRIA TABELA
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

} catch (PDOException $e) {

    redirecionarComMensagem(
        "Não foi possível conectar ao sistema.",
        "error"
    );
}

/*
|--------------------------------------------------------------------------
| IDENTIFICA A AÇÃO
|--------------------------------------------------------------------------
*/

$acao = $_POST["acao"] ?? "login";

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if ($acao === "logout") {

    /*
    |--------------------------------------------------------------------------
    | APAGA TODOS OS DADOS DA SESSÃO
    |--------------------------------------------------------------------------
    */

    $_SESSION = [];

    /*
    |--------------------------------------------------------------------------
    | REMOVE COOKIE DA SESSÃO
    |--------------------------------------------------------------------------
    */

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DESTRÓI A SESSÃO
    |--------------------------------------------------------------------------
    */

    session_destroy();

    /*
    |--------------------------------------------------------------------------
    | CRIA NOVA SESSÃO PARA A MENSAGEM
    |--------------------------------------------------------------------------
    */

    session_start();

    $_SESSION["mensagem"] =
        "Você saiu da sua conta com sucesso.";

    $_SESSION["tipoMensagem"] =
        "success";

    /*
    |--------------------------------------------------------------------------
    | REDIRECIONA
    |--------------------------------------------------------------------------
    */

    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

$email = trim(
    $_POST["email"] ?? ""
);

$senha = $_POST["senha"] ?? "";

/*
|--------------------------------------------------------------------------
| CAMPOS VAZIOS
|--------------------------------------------------------------------------
*/

if (
    $email === "" ||
    $senha === ""
) {

    redirecionarComMensagem(
        "Preencha todos os campos.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DE E-MAIL
|--------------------------------------------------------------------------
*/

if (!filter_var(
    $email,
    FILTER_VALIDATE_EMAIL
)) {

    redirecionarComMensagem(
        "Digite um e-mail válido.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| BUSCA USUÁRIO
|--------------------------------------------------------------------------
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
| VERIFICA SENHA
|--------------------------------------------------------------------------
*/

if (
    !$usuario ||
    !password_verify(
        $senha,
        $usuario["senha"]
    )
) {

    redirecionarComMensagem(
        "E-mail ou senha inválidos.",
        "error"
    );
}

/*
|--------------------------------------------------------------------------
| REGENERA ID DA SESSÃO
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

/*
|--------------------------------------------------------------------------
| SALVA DADOS DO USUÁRIO
|--------------------------------------------------------------------------
*/

$_SESSION["usuario_id"] =
    $usuario["id"];

$_SESSION["usuario_nome"] =
    $usuario["nome"];

$_SESSION["usuario_email"] =
    $usuario["email"];

/*
|--------------------------------------------------------------------------
| LOGIN REALIZADO
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;