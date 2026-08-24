# ACME Digital — Sistema de Login e Cadastro

## Sobre o projeto

Este projeto foi desenvolvido para a empresa **ACME Digital** com o objetivo de criar telas de **Login e Cadastro** seguras, funcionais e coerentes com boas práticas de usabilidade.

O sistema possui validações para impedir o envio de campos vazios, tratamento de entradas inválidas, proteção contra **XSS** e **SQL Injection**, além de mensagens de feedback utilizando **SweetAlert**.

Também foram desenvolvidos **testes automatizados utilizando Selenium WebDriver**, responsáveis por verificar os principais comportamentos das telas.

---

## Objetivos

O projeto tem como objetivos:

* Validar campos obrigatórios;
* Impedir o envio de campos vazios;
* Detectar e bloquear entradas suspeitas de XSS;
* Evitar SQL Injection;
* Exibir mensagens amigáveis para o usuário;
* Informar quando o login foi realizado com sucesso;
* Informar quando as credenciais são inválidas;
* Impedir acesso utilizando credenciais incorretas;
* Testar automaticamente os comportamentos do sistema utilizando Selenium;
* Gerar screenshots como evidências dos testes.

---


## Tecnologias utilizadas

* HTML5
* CSS3
* JavaScript
* PHP
* MySQL
* SweetAlert2
* Selenium WebDriver
* Node.js
* Google Chrome
* XAMPP

---

## Estrutura do projeto

A estrutura do projeto é organizada da seguinte forma:

```text
teste_seguranca/
│
├── index.php
├── login.php
├── cadastro.php
│
├── process_login.php
├── process_register.php
│
├── conexao.php
│
├── testeAutomatizado.js
├── package.json
├── relatorio.json
├── README.md
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   ├── js/
│   │   └── script.js
│   │
│   └── screenshots/
│       ├── screenshot_Login_correto.png
│       ├── screenshot_Senha_incorreta.png
│       ├── screenshot_Campo_email_vazio.png
│       ├── screenshot_Campo_senha_vazio.png
│       └── screenshot_Tentativa_de_XSS.png
│
└── banco/
    └── seguranca.sql
```

> Os nomes dos arquivos podem ser alterados de acordo com a estrutura utilizada no projeto.

---

# Banco de dados

O projeto utiliza **MySQL** para armazenar os usuários cadastrados.

A tabela principal utilizada é:

```sql
CREATE DATABASE seguranca;

USE seguranca;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(100) NOT NULL
);
```

## Como configurar o banco

### 1. Instalar o XAMPP

Instale o XAMPP e abra o **XAMPP Control Panel**.

Inicie:

* Apache
* MySQL

### 2. Abrir o phpMyAdmin

Acesse:

```text
http://localhost/phpmyadmin
```

### 3. Criar o banco

Crie um banco chamado:

```text
seguranca
```

Depois execute o arquivo:

```text
banco/seguranca.sql
```

Esse arquivo será responsável por criar a tabela `usuarios`.

---

# Configuração da conexão com o MySQL

No arquivo `conexao.php`, configure os dados do banco:

```php
<?php

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
```

Caso o MySQL esteja configurado com outra senha ou porta, altere os valores de acordo com sua instalação.

---

# Como executar o projeto

## 1. Colocar o projeto no XAMPP

Copie a pasta do projeto para:

```text
C:\xampp\htdocs\
```

Por exemplo:

```text
C:\xampp\htdocs\teste_seguranca
```

## 2. Iniciar o XAMPP

No XAMPP Control Panel, inicie:

```text
Apache
MySQL
```

## 3. Acessar o sistema

Abra o navegador e acesse:

```text
http://localhost/teste_seguranca/
```

A tela de login será exibida.

---

# Segurança

O sistema possui mecanismos para tratar entradas maliciosas e inválidas.

## XSS

Entradas contendo scripts, como:

```html
<script>alert('XSS')</script>
```

não devem ser executadas pelo sistema.

Quando uma entrada suspeita é identificada, o sistema deve apresentar uma mensagem utilizando SweetAlert:

```text
Input inválido
```

---

## SQL Injection

O sistema deve utilizar consultas preparadas para evitar que entradas do usuário sejam interpretadas como comandos SQL.

Exemplo:

```php
$stmt = $conn->prepare(
    "SELECT id, nome, email, senha FROM usuarios WHERE email = ?"
);

$stmt->bind_param("s", $email);

$stmt->execute();
```

Dessa forma, o valor informado pelo usuário não é inserido diretamente na consulta SQL.

---

# SweetAlert

As mensagens do sistema utilizam **SweetAlert2** para apresentar feedback visual ao usuário.

São utilizados diferentes tipos de mensagens:

### Login realizado

```text
Login realizado com sucesso!
```

### Credenciais inválidas

```text
E-mail ou senha incorretos.
```

### Campos vazios

```text
Preencha todos os campos.
```

### Entrada inválida

```text
Input inválido.
```

### Cadastro realizado

```text
Cadastro realizado com sucesso!
```

---

# Testes automatizados

Os testes automatizados foram desenvolvidos utilizando **Selenium WebDriver** com JavaScript.

O arquivo principal dos testes é:

```text
testeAutomatizado.js
```

Os testes verificam os seguintes comportamentos:

| Teste              | Resultado esperado            |
| ------------------ | ----------------------------- |
| Login correto      | Login realizado com sucesso   |
| Senha incorreta    | Acesso negado                 |
| Campo e-mail vazio | Mensagem de campo obrigatório |
| Campo senha vazio  | Mensagem de campo obrigatório |
| Tentativa de XSS   | Mensagem "Input inválido"     |

---

# Instalação do Selenium

É necessário ter o **Node.js** instalado.

Dentro da pasta do projeto, abra o terminal e execute:

```bash
npm init -y
```

Depois instale o Selenium:

```bash
npm install selenium-webdriver
```

---

# Executando os testes

Antes de executar os testes, certifique-se de que:

1. O XAMPP está aberto;
2. O Apache está funcionando;
3. O MySQL está funcionando;
4. O banco de dados foi criado;
5. O projeto está dentro da pasta `htdocs`;
6. O endereço definido em `testeAutomatizado.js` está correto.

No arquivo:

```javascript
const TARGET_URL = "http://localhost/teste_seguranca/index.php";
```

Altere a URL caso o nome da pasta do projeto seja diferente.

Depois execute:

```bash
node testeAutomatizado.js
```

O Selenium abrirá o Google Chrome e realizará os testes automaticamente.

---

# Screenshots dos testes

Após a execução, os screenshots devem ser armazenados em:

```text
assets/screenshots/
```

Exemplos:

```text
assets/screenshots/
├── screenshot_Login_correto.png
├── screenshot_Senha_incorreta.png
├── screenshot_Campo_email_vazio.png
├── screenshot_Campo_senha_vazio.png
└── screenshot_Tentativa_de_XSS.png
```

Essas imagens servem como **evidências da execução dos testes automatizados**.

---

# Relatório dos testes

Após a execução do Selenium, o arquivo:

```text
relatorio.json
```

é gerado automaticamente.

Ele contém informações sobre:

* Nome do teste;
* Status do teste;
* Mensagem recebida;
* Caminho do screenshot.

Exemplo:

```json
[
    {
        "teste": "Login correto",
        "status": "pass",
        "mensagem": "Login realizado com sucesso!",
        "screenshot": "assets/screenshots/screenshot_Login_correto.png"
    }
]
```

---

# Observação sobre o arquivo de testes

No código fornecido pela atividade, o array de testes já possui exemplos como:

```javascript
const testes = [
    {
        email: "admin@teste.com",
        senha: "1234",
        descricao: "Login correto"
    },
    {
        email: "admin@teste.com",
        senha: "errada",
        descricao: "Senha incorreta"
    },
    {
        email: "",
        senha: "1234",
        descricao: "Campo email vazio"
    },
    {
        email: "admin@teste.com",
        senha: "",
        descricao: "Campo senha vazio"
    },
    {
        email: "<script>",
        senha: "1234",
        descricao: "Tentativa de XSS"
    }
];
```

Os dados devem ser ajustados de acordo com os usuários existentes no banco de dados.

---

# Critérios atendidos

O projeto busca atender aos seguintes requisitos da atividade:

* [x] Tela de Login
* [x] Tela de Cadastro
* [x] Validação de campos vazios
* [x] Proteção contra XSS
* [x] Proteção contra SQL Injection
* [x] SweetAlert para feedback
* [x] Bloqueio de credenciais inválidas
* [x] Testes automatizados com Selenium
* [x] Screenshots dos testes
* [x] Relatório dos testes
* [x] Banco de dados MySQL
* [x] Instruções para execução local

---

# Autores

**Projeto acadêmico — ACME Digital**

- kaorishi11
- JamiledeOliveiraFranquilim