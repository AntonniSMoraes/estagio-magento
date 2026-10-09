# Desafio 14.1 — A Camada de dados


* **Estrutura de Pastas:** O módulo `Webjump_CatalogExtension` foi estendido para criar um atributo EAV de produto via código (Data Patch) e exibir um selo na página de detalhes do item:
```bash
app/code/Webjump/CatalogExtension/
├── registration.php
├── Setup/
│   └── Patch/
│       └── Data/
│           └── AddSustainableAttribute.php
├── ViewModel/
│   └── ProductBadge.php
└── view/
    └── frontend/
        ├── layout/
        │   └── catalog_product_view.xml
        └── templates/
            └── product/
                └── badge.phtml
```

* ***/Setup/Patch/Data/AddSustainableAttribute.php:*** Data Patch responsável por criar o atributo booleano is_sustainable no banco de dados via código, registrando a execução na tabela patch_list e vinculando o campo ao grupo "Conteúdo" do catálogo.

* ***/ViewModel/ProductBadge.php:*** Implementa a lógica que recupera o produto atual (current_product) e verifica se ele possui o atributo de sustentabilidade ativo, isolando a regra da camada de visualização.

* ***/view/frontend/layout/catalog_product_view.xml:*** Injeta o bloco do selo na página de detalhes do produto (catalog_product_view), posicionando-o acima do bloco de preço e passando o ViewModel como argumento.

* ***/view/frontend/templates/product/badge.phtml:*** Renderiza visualmente o selo verde com texto escapado caso o produto seja sustentável, garantindo que nada quebre e nada seja exibido caso o produto não possua a flag ativa.

  ### Por que foi escolhido o escopo Global (`SCOPE_GLOBAL`)?

* **Natureza do Atributo:** O selo define uma característica que pertence à confecção do produto em si, e não à forma como ele é apresentado ou vendido. 
  * *Exemplo:* Se a "Camiseta Estágio 2026" é produzida com algodão orgânico ou material reciclado, ela continua sendo sustentável independentemente de ser visualizada no Brasil ou no exterior. Trata-se de uma propriedade do item, da mesma forma que seu peso ou dimensões físicas.

* **Evitar inconsistências cadastrais (Store View):** O escopo `Store View` serve para dados que mudam de acordo com o idioma ou a região (como nome e descrição traduzidos). Se utilizássemos `Store View` para a sustentabilidade, o lojista seria obrigado a marcar "Yes" em cada idioma cadastrado na loja. Caso esquecesse de preencher na visão em inglês, o produto seria exibido como ecológico na loja brasileira, mas comum na loja internacional.

* **Estrutura no Banco de Dados (EAV):** Como vimos na arquitetura EAV, o Magento grava valores em tabelas verticais (`catalog_product_entity_int`). Com o escopo Global, o sistema cria apenas um único registro no banco (`store_id = 0`) para aquele produto, sem gerar linhas duplicadas para cada visão de loja e sem sobrecarregar as consultas e índices.

