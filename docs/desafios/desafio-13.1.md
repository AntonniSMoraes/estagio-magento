# Desafio 13.1 — Primeiro módulo com bloco na home


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
