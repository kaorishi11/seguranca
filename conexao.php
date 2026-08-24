<?php
// Jamile de Oliveira Franquilim e Geovanna Kaori Shimada

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "seguranca";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

$conn->set_charset("utf8mb4");

?>