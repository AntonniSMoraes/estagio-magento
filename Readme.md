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

  # Desafio 13.3 - Campo de configuração no admin

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

  # Desafio 14.1 - A Camada de dados

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

  # Desafio 14.2 - Entidade própria, do banco ao repositório


# Desafio 14.2 - Entidade própria, do banco ao repositório

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