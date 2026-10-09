# Desafio 14.2 — Entidade própria, do banco ao repositório


* **Estrutura de Pastas:** O módulo `Webjump_ProductReviews` foi criado para implementar uma entidade personalizada de avaliações de produto do zero, utilizando Declarative Schema, camada completa de persistência e Service Contracts:
```bash
app/code/Webjump/ProductReviews/
├── registration.php
├── etc/
│   ├── module.xml
│   ├── di.xml
│   ├── db_schema.xml
│   └── db_schema_whitelist.json
├── Api/
│   ├── ReviewRepositoryInterface.php
│   └── Data/
│       └── ReviewInterface.php
├── Model/
│   ├── Review.php
│   ├── ReviewRepository.php
│   └── ResourceModel/
│       ├── Review.php
│       └── Review/
│           └── Collection.php
└── Setup/
    └── Patch/
        └── Data/
            └── AddSampleReviews.php
```
* ***/etc/db_schema.xml e db_schema_whitelist.json:*** Declaração da tabela webjump_product_reviews via Declarative Schema, contendo colunas para ID, SKU do produto, autor, comentário, nota, status e data. O arquivo whitelist gerado atua como trava de integridade para as colunas e chaves.

* ***/Api/ReviewRepositoryInterface.php e Api/Data/ReviewInterface.php:*** Contratos de API (Service Contracts) que definem os métodos públicos de persistência (save, getById, getList, delete) e transporte de dados, isolando a camada externa dos detalhes de banco.

* ***/Model/Review.php, ResourceModel e Collection:*** Implementação das camadas de dados. O Model encapsula a lógica da entidade, o ResourceModel faz as operações de banco na tabela específica, e a Collection gerencia conjuntos de registros e iterações.

* ***/Model/ReviewRepository.php e etc/di.xml:*** Implementação do repositório ligado à interface por <preference> no di.xml. O método getList foi configurado com CollectionProcessor para suportar SearchCriteria com filtros e limite de registros.

* ***/Setup/Patch/Data/AddSampleReviews.php:*** Data Patch responsável por popular 5 avaliações de exemplo utilizando diretamente a interface do repositório, registrando sua execução na tabela patch_list.
  ### Por que tabela própria e não EAV neste caso?

* **Estrutura de campos fixa:** O EAV foi feito para entidades onde cada item tem características completamente diferentes (como produtos, onde uma camiseta precisa de tamanho e uma geladeira precisa de voltagem). Uma avaliação de cliente tem sempre a mesma estrutura fechada: autor, nota, comentário, status e SKU. Não faz sentido criar campos dinâmicos no admin para isso.

* **Simplicidade e Performance:** No modelo EAV, salvar uma única avaliação exigiria espalhar os dados em várias tabelas (`_varchar`, `_text`, `_int`) e depois juntar tudo com vários `JOINs` para conseguir ler. Com uma tabela própria comum (*flat*), o registro inteiro é salvo e consultado em uma única linha, tornando as buscas muito mais rápidas e sem depender de reindexação.

* **Separação de responsabilidades:** A avaliação não é uma característica intrínseca do produto (como peso ou cor), mas sim uma interação gerada pelo cliente sobre aquele produto. Criar uma tabela separada mantém o catálogo limpo e isola essa regra de negócio.
