# Desafio 12.1 — Ambiente Magento no Ar

Documentação da instalação e configuração do Magento 2 via WSL2 e Docker no Windows 11, atendendo a todos os critérios de aceite do Desafio 12.1 da Sprint 6.

---

## 1. Passo a Passo da Instalação

1. **Configuração de Recursos no Windows & WSL2:**
   - Habilitação dos recursos `Microsoft-Windows-Subsystem-Linux`, `VirtualMachinePlatform`, `Hyper-V` e `containers`.
   - Instalação da distribuição **Ubuntu 22.04 LTS** no WSL2 (`wsl --set-default-version 2`).
   - Configuração do arquivo `.wslconfig` na máquina host para balancear memória RAM e processadores.

2. **Docker Desktop & Git:**
   - Integração do Docker Desktop ativada com o WSL2/Ubuntu.
   - Configuração do Git dentro do Linux para manter quebras de linha em padrão Unix (`core.autocrlf false` e `core.eol lf`).

3. **Criação do Projeto:**
   - Todo o projeto foi clonado e executado obrigatoriamente dentro do sistema de arquivos nativo do WSL (`~/Sites/magento`).
   - Baixado o template oficial do `docker-magento` (Mark Shust).

4. **Instalação do Magento:**
   - Execução do script de download apontando a edição e versão:
     ```bash
     bin/download community 2.4.8-p1
     ```
   - Execução do setup para levantar a stack de containers e rodar o instalador:
     ```bash
     bin/setup magento.test
     ```

5. **Configurações Pós-instalação:**
   - Mapeamento de domínios em `/etc/hosts` (Ubuntu) e `C:\Windows\System32\drivers\etc\hosts` (Windows):
     ```text
     127.0.0.1 magento.test
     ```
   - Ativação do modo desenvolvedor:
     ```bash
     bin/magento deploy:mode:set developer
     ```
   - Criação do usuário administrativo próprio via CLI:
     ```bash
     bin/magento admin:user:create
     ```

---

## 2. Problemas Enfrentados e Soluções

### Problema 1: Falha na identificação da versão no `bin/download`
- **Sintoma:** Ao rodar `bin/download 2.4.8-p1`, o script interpretou o valor como edição e executou internamente `magento/project-2.4.8-p1-edition=2.4.9`, resultando no erro:
  `Could not find package magento/project-2.4.8-p1-edition with version 2.4.9`. A pasta `src/` não foi baixada e comandos posteriores (`bin/setup`, `bin/copytocontainer`) falharam com `chmod: cannot access 'bin/magento'`.
- **Causa:** Ausência do argumento explícito da edição do Magento no script.
- **Solução:** Limpeza total dos volumes e containers (`bin/stop`, `docker compose down -v`), sanitização das quebras de linha dos scripts (`sed -i 's/\r$//'`) e execução do comando especificando a edição:
  ```bash
  bin/download community 2.4.8-p1
  ```

<br>

# Desafio 12.2 - Explorando a loja e o catálogo

### O que é Website, Store e Store View?

* **Website:** Local onde se definem os limites do negócio, separando base de clientes, produtos e formas de transação.
  * *Exemplo:* Um cliente pode ter uma loja de roupas e outra de eletrônicos; para isso, ele tem 2 websites, cada um com sua base de clientes, formas de pagamentos, regras de negócio e produtos.

* **Store:** É o lugar onde se define a estrutura de catálogo e navegação, permitindo a seleção do menu principal e dos produtos que compõem a árvore do catálogo.
  * *Exemplo:* Dentro do website de roupas, você cria a store "Loja Adulto" (com menu principal contendo: Masculino, Feminino, Acessórios) e outra store "Loja Kids" (com menu principal contendo: Bebês, Meninos, Meninas). Ambas pertencem à mesma marca e compartilham o carrinho de compras, mas têm navegações independentes.

* **Store View:** Define a visualização da store, podendo configurar idiomas diferentes para a mesma página. O catálogo se mantém o mesmo, mas a interface e textos se adaptam ao idioma do usuário.

# Desafio 13.1 - Primeiro módulo com bloco na home

* **Estrutura de Pastas:** O módulo criado segue o modelo apresentado na aula `Semana 13: Módulos, Injeção de Dependência e Templates`, tendo sido criado em: (magento/src/app/code/Webjump/PromoBanner). O diretório foi dividido da seguinte forma:
```bash
app/code/Webjump/PromoBanner/
├── registration.php
├── etc/
│   └── module.xml
├── ViewModel/
│   └── Banner.php
└── view/
    └── frontend/
        ├── layout/
        │   └── cms_index_index.xml
        ├── templates/
        │   └── banner.phtml
        └── web/css/source/
                    └── _module.less
```

  * ***/registration.php e etc/module.xml:*** Responsáveis por informar ao magento sobre a existência do módulo, quais módulos ele depende e garante que eles sejam carregados antes.

  * ***/ViewModel/Banner.php:*** Implementação do conteúdo que aparecerá no model, ou seja, a lógica de apresentação, semelhante ao Sling Model do AEM.

  * ***/view/frontend/layout/cms_index_index.xml:*** Limita a utilização do módulo, informando ao magento onde ele deve ser renderizado.

  * ***/view/frontend/templates/banner.phtml:*** É a representação visual do módulo em Html, usado para aplicar estilização no componente, consumindo as classes css e os dados do ViewModel.

  * ***/view/frontend/web/css/source/_module.less*:** Contém as classes .css do módulo, este arquivo não requer importação, ele é instalado junto do módulo, sendo mesclado no CSS global compilado do tema.


# Desafio 13.2 - Estendendo o comportamento do catálogo

* **Estrutura de Pastas:** O módulo criado segue as boas práticas de extensão sem edição do núcleo (`vendor/`), tendo sido criado em: `magento/src/app/code/Webjump/CatalogExtension`. O diretório foi dividido da seguinte forma:
```bash
app/code/Webjump/CatalogExtension/
├── registration.php
├── etc/
│   ├── module.xml
│   ├── events.xml
│   └── frontend/
│       └── di.xml
├── Plugin/
│   └── ProductPlugin.php
└── Observer/
    └── LogProductSaveObserver.php
```

  * ***/registration.php e etc/module.xml:*** Responsáveis por registrar o módulo no Magento, definir a versão e garantir o carregamento prévio do Magento_Catalog.

  * ***/etc/frontend/di.xml:*** Declara a injeção de dependência e ativa o plugin especificamente na área da vitrine (frontend), evitando interferir na edição interna de nomes no painel administrativo.

  * ***/Plugin/ProductPlugin.php:*** Implementa a lógica do Plugin do tipo after interceptando o método getName() de Magento\Catalog\Model\Product, adicionando o sufixo  - [Exclusivo Webjump] ao nome do produto na loja.

  * ***/etc/events.xml:*** Registra o evento nativo catalog_product_save_after, apontando qual classe de Observer deve ser executada quando um produto for salvo.

  * ***/Observer/LogProductSaveObserver.php:*** Implementa a interface ObserverInterface, escuta o salvamento do produto e grava no log (var/log/debug.log / system.log) uma mensagem contendo SKU, ID e Nome do item.

  ### Por que utilizamos Plugin em um caso e Observer no outro?

  * **Plugin (afterGetName):** Foi utilizado porque a necessidade era interceptar um método público e alterar diretamente o valor retornado por ele em tempo de execução (acrescentar o sufixo no texto retornado pela função).

  * **Observer (catalog_product_save_after):** Foi utilizado porque a intenção era apenas escutar uma ação que aconteceu no ciclo do Magento (o salvamento de um produto) para disparar um efeito colateral (gerar uma linha de log), sem a necessidade de modificar o fluxo original ou o valor retornado da execução.