# Desafio 13.3 — Campo de configuração no admin


* **Estrutura de Pastas:** O módulo `Webjump_PromoBanner` foi estendido para permitir que o lojista configure o conteúdo do banner via painel administrativo em Stores > Configuration, sem mexer em código:
```bash
app/code/Webjump/PromoBanner/
├── registration.php
├── etc/
│   ├── module.xml
│   ├── config.xml
│   └── adminhtml/
│       └── system.xml
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
  * ***/etc/adminhtml/system.xml:*** Cria uma aba própria no admin e a seção do banner em Stores > Configuration com campos de ativação, título, descrição e código de cupom.

  * ***/etc/config.xml:*** Define os valores padrão para os campos criados, garantindo que o componente funcione normalmente e não quebre caso o campo esteja vazio no admin.

  * ***/ViewModel/Banner.php:*** Atualizado para receber a injeção de dependência de ScopeConfigInterface e ler dinamicamente os valores salvos no painel administrativo, semelhante à configuração OSGi do AEM.

  * ***/view/frontend/templates/banner.phtml:*** Exibe os dados informados pelo lojista aplicando escape de saída (escapeHtml), respeitando a regra de ativação configurada no painel.

