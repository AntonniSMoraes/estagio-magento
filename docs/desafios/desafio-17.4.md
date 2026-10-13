# Desafio 17.4 — Caixão de Ofertas no Page Builder

* **Estrutura de Pastas:** O módulo `Webjump_PageBuilderHalloween` foi criado para disponibilizar o componente **Caixão de Ofertas** no Page Builder, permitindo configurar blocos promocionais pelo admin. A estrutura abaixo apresenta os arquivos desta etapa:

```text
app/code/Webjump/PageBuilderHalloween/
├── etc/
│   └── module.xml
├── i18n/
│   └── pt_BR.csv
├── view/
│   ├── adminhtml/
│   │   ├── layout/
│   │   │   └── pagebuilder_webjump_caixao_form.xml
│   │   ├── pagebuilder/
│   │   │   └── content_type/
│   │   │       └── webjump_caixao.xml
│   │   ├── ui_component/
│   │   │   └── pagebuilder_webjump_caixao_form.xml
│   │   └── web/
│   │       ├── css/
│   │       │   └── source/
│   │       │       └── _module.less
│   │       ├── js/
│   │       │   └── content-type/
│   │       │       └── caixao/
│   │       │           └── preview.js
│   │       └── template/
│   │           └── content-type/
│   │               └── caixao/
│   │                   └── default/
│   │                       └── preview.html
│   ├── base/
│   │   └── web/
│   │       └── template/
│   │           └── content-type/
│   │               └── caixao/
│   │                   └── default/
│   │                       └── master.html
│   └── frontend/
│       └── web/
│           └── css/
│               └── source/
│                   └── _module.less
└── registration.php
```

* ***/registration.php e etc/module.xml:*** Registram o módulo e sua dependência de `Magento_PageBuilder`.

* ***/view/adminhtml/pagebuilder/content_type/webjump_caixao.xml:*** Define rótulo em português, ícone nativo de banner e seção Media. Permite inserção em linha (`row`) e coluna (`column`). Mapeia os campos aos elementos dos templates, com conversores de texto escapado, imagem e link.

* ***/view/adminhtml/ui_component/pagebuilder_webjump_caixao_form.xml:*** Declara seis campos configuráveis: título, descrição, imagem, link, texto do botão e texto alternativo da imagem. Herda `pagebuilder_base_form` e utiliza o uploader e o seletor de links nativos.

* ***/view/adminhtml/layout/pagebuilder_webjump_caixao_form.xml:*** Insere o UI Component usado ao editar o bloco.

* ***/view/adminhtml/web/js/content-type/caixao/preview.js:*** Estende o preview nativo e cria o uploader ligado ao dataStore do bloco.

* ***/view/adminhtml/web/template/content-type/caixao/default/preview.html:*** Mostra o conteúdo e as opções no editor. O elemento externo tem `pagebuilder-content-type` como primeira classe. O botão é apresentado como span no editor para evitar navegar durante a edição.

* ***/view/base/web/template/content-type/caixao/default/master.html:*** Persiste o HTML que será publicado, com imagem, textos e link real. Os elementos têm os mesmos mapeamentos do preview, permitindo reabrir e editar o conteúdo salvo.

* ***/view/adminhtml e view/frontend/web/css/source/_module.less:*** Aplicam fundo roxo, borda e botão laranja e textos claros no editor e na loja. A fonte do título na loja acompanha o tema.

* ***/i18n/pt_BR.csv:*** Mantém o rótulo **Caixão de Ofertas** em português. Os rótulos dos campos também estão definidos em português no formulário.

### Por que utilizamos Preview e Master?

* **Preview:** Apresenta o bloco no editor do Page Builder e disponibiliza os controles para sua configuração.

* **Master:** Define o HTML salvo e utilizado na página publicada. Os mesmos campos são mapeados nos dois templates para preservar o conteúdo configurado.

### Preservação do Núcleo

* **Módulo próprio:** A configuração, o formulário, os templates e os estilos foram implementados em `Webjump_PageBuilderHalloween`, utilizando os componentes nativos como dependências. Nenhum arquivo do núcleo foi alterado ou copiado integralmente.
