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
| PERMITIR SOMENTE POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: cadastro.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CONFIGURAÇÃO
|--------------------------------------------------------------------------
*/

$host = "localhost";
$dbname = "seguranca";
$user = "root";
$password = "";

/*
|--------------------------------------------------------------------------
| FUNÇÃO DE REDIRECIONAMENTO
|--------------------------------------------------------------------------
*/

function voltarComMensagem(
    string $mensagem,
    string $tipo
): void {

    $_SESSION["mensagem"] = $mensagem;
    $_SESSION["tipoMensagem"] = $tipo;

    header("Location: cadastro.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CONEXÃO
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

    voltarComMensagem(
        "Não foi possível conectar ao sistema.",
        "error"
    );
}

/*
|--------------------------------------------------------------------------
| RECEBE DADOS
|--------------------------------------------------------------------------
*/

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

    voltarComMensagem(
        "Preencha todos os campos.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| NOME
|--------------------------------------------------------------------------
*/

if (
    mb_strlen($nome) < 2 ||
    mb_strlen($nome) > 100
) {

    voltarComMensagem(
        "O nome deve possuir entre 2 e 100 caracteres.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| E-MAIL
|--------------------------------------------------------------------------
*/

if (!filter_var(
    $email,
    FILTER_VALIDATE_EMAIL
)) {

    voltarComMensagem(
        "Digite um e-mail válido.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| SENHA
|--------------------------------------------------------------------------
*/

if (strlen($senha) < 4) {

    voltarComMensagem(
        "A senha deve possuir pelo menos 4 caracteres.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| CONFIRMAÇÃO
|--------------------------------------------------------------------------
*/

if ($senha !== $confirmarSenha) {

    voltarComMensagem(
        "As senhas não coincidem.",
        "warning"
    );
}

/*
|--------------------------------------------------------------------------
| VERIFICA E-MAIL
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

    voltarComMensagem(
        "Este e-mail já está cadastrado.",
        "error"
    );
}

/*
|--------------------------------------------------------------------------
| HASH DA SENHA
|--------------------------------------------------------------------------
*/

$senhaHash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);

/*
|--------------------------------------------------------------------------
| INSERE USUÁRIO
|--------------------------------------------------------------------------
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

/*
|--------------------------------------------------------------------------
| SUCESSO
|--------------------------------------------------------------------------
*/

$_SESSION["mensagem"] =
    "Cadastro realizado com sucesso!";

$_SESSION["tipoMensagem"] =
    "success";

/*
|--------------------------------------------------------------------------
| REDIRECIONA PARA LOGIN
|--------------------------------------------------------------------------
*/

header("Location: login.php");
exit;