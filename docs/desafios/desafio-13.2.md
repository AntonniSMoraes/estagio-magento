# Desafio 13.2 — Estendendo o comportamento do catálogo


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

