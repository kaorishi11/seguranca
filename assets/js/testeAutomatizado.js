/**
 * ==========================================================
 * TESTES AUTOMATIZADOS - ACME DIGITAL
 * ==========================================================
 *
 * Instalação:
 *
 * npm install selenium-webdriver
 *
 * Execução:
 *
 * node testeAutomatizado.js
 *
 * ==========================================================
 */

const {
    Builder,
    By,
    until
} = require("selenium-webdriver");

const fs = require("fs");
const path = require("path");

// ==========================================================
// CONFIGURAÇÕES
// ==========================================================

const BASE_URL =
    "http://localhost/SELENIUM_JAMILE/seguranca";

const LOGIN_URL =
    `${BASE_URL}/login.php`;

const CADASTRO_URL =
    `${BASE_URL}/cadastro.php`;

const TIMEOUT =
    5000;

const SCREENSHOT_DIR =
    path.join(
        __dirname,
        "screenshots"
    );

const RELATORIO =
    path.join(
        __dirname,
        "relatorio.json"
    );


// ==========================================================
// PREPARAÇÃO
// ==========================================================

fs.mkdirSync(
    SCREENSHOT_DIR,
    {
        recursive: true
    }
);

let relatorio = [];


// ==========================================================
// FUNÇÕES AUXILIARES
// ==========================================================

function nomeSeguro(nome) {

    return nome
        .replace(/\s+/g, "_")
        .replace(
            /[^a-zA-Z0-9_\-]/g,
            ""
        );
}


async function screenshot(
    driver,
    nome
) {

    const base64 =
        await driver.takeScreenshot();

    const arquivo =
        path.join(
            SCREENSHOT_DIR,
            `${nomeSeguro(nome)}.png`
        );

    fs.writeFileSync(
        arquivo,
        base64,
        "base64"
    );

    return arquivo;
}


async function abrirNavegador() {

    return await new Builder()
        .forBrowser("chrome")
        .build();
}


async function obterSweetAlert(
    driver
) {

    const alertas =
        await driver.findElements(
            By.css(".swal2-popup")
        );

    if (
        alertas.length === 0
    ) {

        return "";
    }

    return await alertas[0].getText();
}


function registrar(
    teste,
    status,
    mensagem,
    observacao,
    imagem
) {

    relatorio.push({

        teste,

        status,

        mensagem,

        observacao,

        screenshot: imagem

    });
}


// ==========================================================
// TESTE 1 - LOGIN CORRETO
// ==========================================================

async function testeLoginCorreto() {

    const nome =
        "Login correto";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            LOGIN_URL
        );

        await driver
            .findElement(
                By.id("email")
            )
            .sendKeys(
                "admin@teste.com"
            );

        await driver
            .findElement(
                By.id("senha")
            )
            .sendKeys(
                "1234"
            );

        await driver
            .findElement(
                By.id("btn-login")
            )
            .click();

        /*
         * Aguarda sair da página de login.
         */

        await driver.wait(
            async () => {

                const url =
                    await driver.getCurrentUrl();

                return url.includes(
                    "index.php"
                );

            },
            TIMEOUT
        );

        const url =
            await driver.getCurrentUrl();

        const passou =
            url.includes(
                "index.php"
            );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        if (passou) {

            registrar(
                nome,
                "PASS",
                "Login realizado com sucesso.",
                "Usuário válido recebeu acesso.",
                imagem
            );

        } else {

            registrar(
                nome,
                "FAIL",
                "",
                "Usuário válido não conseguiu acessar.",
                imagem
            );
        }

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_Login_correto"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 2 - SENHA INCORRETA
// ==========================================================

async function testeSenhaIncorreta() {

    const nome =
        "Senha incorreta";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            LOGIN_URL
        );

        await driver
            .findElement(
                By.id("email")
            )
            .sendKeys(
                "admin@teste.com"
            );

        await driver
            .findElement(
                By.id("senha")
            )
            .sendKeys(
                "senha_errada"
            );

        await driver
            .findElement(
                By.id("btn-login")
            )
            .click();

        /*
         * Aguarda o SweetAlert.
         */

        await driver.wait(
            until.elementLocated(
                By.css(".swal2-popup")
            ),
            TIMEOUT
        );

        const mensagem =
            await obterSweetAlert(
                driver
            );

        const url =
            await driver.getCurrentUrl();

        /*
         * O usuário NÃO pode chegar ao index.php.
         */

        const passou =
            !url.includes(
                "index.php"
            ) &&
            mensagem
                .toLowerCase()
                .includes(
                    "inválidos"
                );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            mensagem,
            passou
                ? "Credencial inválida foi bloqueada."
                : "Credencial inválida pode ter recebido acesso.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_Senha_incorreta"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 3 - EMAIL VAZIO
// ==========================================================

async function testeEmailVazio() {

    const nome =
        "E-mail vazio";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            LOGIN_URL
        );

        await driver
            .findElement(
                By.id("senha")
            )
            .sendKeys(
                "1234"
            );

        await driver
            .findElement(
                By.id("btn-login")
            )
            .click();

        await driver.wait(
            until.elementLocated(
                By.css(".swal2-popup")
            ),
            TIMEOUT
        );

        const mensagem =
            await obterSweetAlert(
                driver
            );

        const passou =
            mensagem
                .toLowerCase()
                .includes(
                    "campos"
                );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            mensagem,
            passou
                ? "O envio foi impedido."
                : "O sistema não bloqueou o campo vazio.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_Email_vazio"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 4 - SENHA VAZIA
// ==========================================================

async function testeSenhaVazia() {

    const nome =
        "Senha vazia";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            LOGIN_URL
        );

        await driver
            .findElement(
                By.id("email")
            )
            .sendKeys(
                "admin@teste.com"
            );

        await driver
            .findElement(
                By.id("btn-login")
            )
            .click();

        await driver.wait(
            until.elementLocated(
                By.css(".swal2-popup")
            ),
            TIMEOUT
        );

        const mensagem =
            await obterSweetAlert(
                driver
            );

        const passou =
            mensagem
                .toLowerCase()
                .includes(
                    "campos"
                );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            mensagem,
            passou
                ? "O envio foi impedido."
                : "O sistema não bloqueou a senha vazia.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_Senha_vazia"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 5 - XSS NO LOGIN
// ==========================================================

async function testeXSSLogin() {

    const nome =
        "XSS no Login";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            LOGIN_URL
        );

        const payload =
            '<script>alert("XSS")</script>';

        await driver
            .findElement(
                By.id("email")
            )
            .sendKeys(
                payload
            );

        await driver
            .findElement(
                By.id("senha")
            )
            .sendKeys(
                "1234"
            );

        await driver
            .findElement(
                By.id("btn-login")
            )
            .click();

        await driver.sleep(500);

        /*
         * Se um alert JavaScript aparecer,
         * o payload foi executado.
         */

        let alertou = false;

        try {

            await driver.switchTo().alert();

            alertou = true;

            await driver
                .switchTo()
                .alert()
                .dismiss();

        } catch (e) {

            alertou = false;
        }

        const html =
            await driver.getPageSource();

        const apareceu =
            html.includes(
                '<script>alert("XSS")</script>'
            );

        const passou =
            !alertou &&
            !apareceu;

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            "",
            passou
                ? "Payload XSS não foi executado nem refletido no HTML."
                : "Possível vulnerabilidade XSS encontrada.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_XSS_Login"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 6 - SQL INJECTION
// ==========================================================

async function testeSQLInjection() {

    const nome =
        "SQL Injection";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            LOGIN_URL
        );

        const payload =
            "' OR '1'='1";

        await driver
            .findElement(
                By.id("email")
            )
            .sendKeys(
                payload
            );

        await driver
            .findElement(
                By.id("senha")
            )
            .sendKeys(
                payload
            );

        await driver
            .findElement(
                By.id("btn-login")
            )
            .click();

        await driver.wait(
            until.elementLocated(
                By.css(".swal2-popup")
            ),
            TIMEOUT
        );

        const mensagem =
            await obterSweetAlert(
                driver
            );

        const url =
            await driver.getCurrentUrl();

        /*
         * SQL Injection não pode conceder acesso.
         */

        const passou =
            !url.includes(
                "index.php"
            ) &&
            mensagem
                .toLowerCase()
                .includes(
                    "inválidos"
                );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            mensagem,
            passou
                ? "SQL Injection foi bloqueado."
                : "A tentativa de SQL Injection pode ter concedido acesso.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_SQL_Injection"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 7 - CADASTRO VAZIO
// ==========================================================

async function testeCadastroVazio() {

    const nome =
        "Cadastro com campos vazios";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            CADASTRO_URL
        );

        await driver
            .findElement(
                By.id("btn-cadastro")
            )
            .click();

        await driver.wait(
            until.elementLocated(
                By.css(".swal2-popup")
            ),
            TIMEOUT
        );

        const mensagem =
            await obterSweetAlert(
                driver
            );

        const passou =
            mensagem
                .toLowerCase()
                .includes(
                    "campos"
                );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            mensagem,
            passou
                ? "Cadastro vazio foi bloqueado."
                : "Cadastro vazio não foi bloqueado.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_Cadastro_vazio"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// TESTE 8 - XSS NO CADASTRO
// ==========================================================

async function testeXSSCadastro() {

    const nome =
        "XSS no Cadastro";

    let driver;

    try {

        driver =
            await abrirNavegador();

        await driver.get(
            CADASTRO_URL
        );

        const payload =
            '<script>alert("XSS")</script>';

        await driver
            .findElement(
                By.id("nome")
            )
            .sendKeys(
                payload
            );

        await driver
            .findElement(
                By.id("email")
            )
            .sendKeys(
                "xss_teste@teste.com"
            );

        await driver
            .findElement(
                By.id("senha")
            )
            .sendKeys(
                "1234"
            );

        await driver
            .findElement(
                By.id("confirmarSenha")
            )
            .sendKeys(
                "1234"
            );

        await driver
            .findElement(
                By.id("btn-cadastro")
            )
            .click();

        await driver.sleep(1000);

        let alertou = false;

        try {

            await driver.switchTo().alert();

            alertou = true;

            await driver
                .switchTo()
                .alert()
                .dismiss();

        } catch (e) {

            alertou = false;
        }

        const html =
            await driver.getPageSource();

        const passou =
            !alertou &&
            !html.includes(
                '<script>alert("XSS")</script>'
            );

        const imagem =
            await screenshot(
                driver,
                nome
            );

        registrar(
            nome,
            passou
                ? "PASS"
                : "FAIL",
            "",
            passou
                ? "Payload XSS não foi executado nem refletido."
                : "Possível vulnerabilidade XSS encontrada.",
            imagem
        );

    } catch (erro) {

        let imagem = null;

        if (driver) {

            imagem =
                await screenshot(
                    driver,
                    "ERRO_XSS_Cadastro"
                );
        }

        registrar(
            nome,
            "FAIL",
            erro.message,
            "Erro durante o teste.",
            imagem
        );

    } finally {

        if (driver) {

            await driver.quit();
        }
    }
}


// ==========================================================
// EXECUÇÃO DE TODOS OS TESTES
// ==========================================================

(async () => {

    console.log("");
    console.log(
        "=========================================="
    );

    console.log(
        "      ACME DIGITAL - TESTES SELENIUM"
    );

    console.log(
        "=========================================="
    );

    console.log("");

    await testeLoginCorreto();

    await testeSenhaIncorreta();

    await testeEmailVazio();

    await testeSenhaVazia();

    await testeXSSLogin();

    await testeSQLInjection();

    await testeCadastroVazio();

    await testeXSSCadastro();

    /*
     * Salva relatório.
     */

    fs.writeFileSync(
        RELATORIO,
        JSON.stringify(
            relatorio,
            null,
            2
        )
    );

    // ======================================================
    // RESULTADO
    // ======================================================

    const aprovados =
        relatorio.filter(
            item =>
                item.status === "PASS"
        ).length;

    const reprovados =
        relatorio.filter(
            item =>
                item.status === "FAIL"
        ).length;

    console.log("");

    console.log(
        "=========================================="
    );

    console.log(
        "              RESULTADO"
    );

    console.log(
        "=========================================="
    );

    console.log(
        `Total: ${relatorio.length}`
    );

    console.log(
        `Aprovados: ${aprovados}`
    );

    console.log(
        `Reprovados: ${reprovados}`
    );

    console.log("");

    console.log(
        `Relatório: ${RELATORIO}`
    );

    console.log(
        `Screenshots: ${SCREENSHOT_DIR}`
    );

    console.log(
        "=========================================="
    );

})();