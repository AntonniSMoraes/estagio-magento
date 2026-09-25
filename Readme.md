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

# Desafio 15.1 - Grid de administração completo

* **Estrutura de Pastas:** O módulo `Webjump_ProductReviews`, criado no desafio 14.2, foi estendido para permitir a consulta e aprovação das avaliações pelo painel administrativo. A estrutura abaixo apresenta os arquivos adicionados ou atualizados nesta etapa:

```text
app/code/Webjump/ProductReviews/
├── etc/
│   ├── module.xml
│   ├── acl.xml
│   └── adminhtml/
│       ├── routes.xml
│       ├── menu.xml
│       └── di.xml
├── Controller/
│   └── Adminhtml/
│       └── Review/
│           ├── Index.php
│           └── MassApprove.php
└── view/
    └── adminhtml/
        ├── layout/
        │   └── webjump_productreviews_review_index.xml
        └── ui_component/
            └── webjump_productreviews_listing.xml
```

* ***/etc/module.xml:*** Atualizado para declarar as dependências de `Magento_Backend`, `Magento_Ui` e `Magento_Config`, mantendo a dependência existente de `Magento_Catalog`.

* ***/etc/adminhtml/routes.xml:*** Registra a rota administrativa `webjump_productreviews`, permitindo que o Magento direcione as requisições para os controllers do módulo.

* ***/etc/adminhtml/menu.xml:*** Adiciona a opção **Avaliações de produtos** dentro de **Catalog**, vinculando o item à página de listagem e à permissão de acesso.

* ***/etc/acl.xml:*** Define os recursos `Webjump_ProductReviews::reviews` e `Webjump_ProductReviews::export`, permitindo configurar separadamente o acesso às avaliações e à futura exportação nos perfis administrativos.

* ***/Controller/Adminhtml/Review/Index.php:*** Responsável por abrir a página, definir o título e selecionar o item ativo do menu. A constante `ADMIN_RESOURCE` informa a permissão necessária para acessar a tela.

* ***/view/adminhtml/layout/webjump_productreviews_review_index.xml:*** Insere o UI Component da listagem na área de conteúdo da página administrativa, conectando a rota à interface do grid.

* ***/view/adminhtml/ui_component/webjump_productreviews_listing.xml:*** Configura as colunas de ID, autor, SKU do produto, nota, comentário, aprovação e data. Também declara filtros por texto, faixa numérica, status e data, além de ordenação, paginação, seleção de registros e ação em massa com confirmação.

* ***/etc/adminhtml/di.xml:*** Liga o DataProvider do grid a uma collection baseada em `SearchResult`, registrada por `virtualType`, utilizando a tabela e o ResourceModel do módulo. Essa estrutura fornece os registros e o total de resultados no formato esperado pelo componente de listagem.

* ***/Controller/Adminhtml/Review/MassApprove.php:*** Recebe a seleção de avaliações por POST, identifica os registros utilizando `MassAction\Filter` e salva a aprovação por `ReviewRepositoryInterface`. Avaliações já aprovadas são ignoradas, e o usuário recebe uma mensagem com a quantidade alterada. Em caso de erro, os detalhes são registrados nos logs e a mensagem informa quantas avaliações já foram aprovadas.

### Testes Realizados

* **Carregamento do grid:** A página exibiu as cinco avaliações cadastradas no desafio 14.2, com os dados distribuídos nas respectivas colunas.

* **Filtro por texto:** Ao filtrar o comentário pela palavra `tamanho`, a listagem retornou somente a avaliação de Lucas Rocha, cujo comentário contém o termo informado.

* **Filtro por faixa numérica:** Ao aplicar o intervalo de ID de `2` até `4`, o grid retornou três avaliações, correspondentes a Mariana Souza (ID 2), João Pereira (ID 3) e Beatriz Lima (ID 4).

* **Filtro por data:** Ao filtrar o campo **Criada em** de `18/09/2026` até `18/09/2026`, o grid retornou as cinco avaliações cadastradas nessa data. Ao alterar o intervalo para `24/09/2026` até `24/09/2026`, a listagem retornou zero registros e exibiu a mensagem **We couldn't find any records.**

* **Paginação:** O limite foi alterado para `2` registros por página, distribuindo as cinco avaliações em três páginas. Ao acessar a página `2 de 3`, foram exibidas as avaliações de João Pereira (ID 3) e Beatriz Lima (ID 4).

* **Ordenação:** A coluna de ID foi utilizada para alternar a apresentação dos registros entre ordem crescente e decrescente.

* **Aprovação em massa:** Foram selecionadas as avaliações de João Pereira e Lucas Rocha, inicialmente com status `No`. Após a confirmação da ação, o Magento exibiu a mensagem `2 avaliação(ões) aprovada(s).` e os dois registros passaram para `Yes`.

### Teste de Permissão com Perfil Restrito

* **Configuração do perfil:** Foi criado o perfil **Teste 15.1** em **System > Permissions > User Roles**, com **Resource Access** definido como **Custom**. Na aba **Role Resources**, as permissões **Avaliações de produtos** e **Exportar avaliações de produtos** ficaram desmarcadas, mantendo outras permissões administrativas habilitadas.

* **Acesso com usuário de teste:** O usuário `teste`, associado ao perfil restrito, acessou o admin em uma janela InPrivate e tentou abrir diretamente a rota `webjump_productreviews/review/index`.

* **Resultado:** O Magento bloqueou a visualização e apresentou a mensagem **Sorry, you need permissions to view this content.**, comprovando que a proteção funciona no acesso direto à tela.

# Desafio 15.2 - Formulário, configuração e exportação

* **Estrutura de Pastas:** O módulo `Webjump_ProductReviews` foi estendido para permitir a criação, edição e exclusão de avaliações pelo admin, configurar sua exibição na loja e exportar os dados do grid em CSV e Excel XML. A estrutura abaixo apresenta os arquivos adicionados ou atualizados nesta etapa, incluindo o ajuste visual da seção de avaliações:

```text
app/code/Webjump/ProductReviews/
├── etc/
│   ├── acl.xml
│   ├── config.xml
│   └── adminhtml/
│       ├── di.xml
│       └── system.xml
├── Controller/
│   └── Adminhtml/
│       └── Review/
│           ├── NewAction.php
│           ├── Edit.php
│           ├── Save.php
│           └── Delete.php
├── Block/
│   ├── Adminhtml/
│   │   └── Review/
│   │       └── Edit/
│   │           ├── BackButton.php
│   │           └── SaveButton.php
│   └── Product/
│       └── Reviews.php
├── Model/
│   ├── Review.php
│   ├── ReviewValidator.php
│   ├── Review/
│   │   └── DataProvider.php
│   └── Source/
│       └── Rating.php
├── Plugin/
│   └── ExportAuthorization.php
├── Ui/
│   └── Component/
│       ├── ExportButton.php
│       └── Listing/
│           └── Column/
│               └── ReviewActions.php
└── view/
    ├── adminhtml/
    │   ├── layout/
    │   │   ├── webjump_productreviews_review_new.xml
    │   │   └── webjump_productreviews_review_edit.xml
    │   └── ui_component/
    │       ├── webjump_productreviews_form.xml
    │       └── webjump_productreviews_listing.xml
    └── frontend/
        ├── layout/
        │   └── catalog_product_view.xml
        ├── templates/
        │   └── product/
        │       └── reviews.phtml
        └── web/
            └── css/
                └── product-reviews.css
```

* ***/Controller/Adminhtml/Review/NewAction.php e Edit.php:*** Responsáveis por abrir o formulário de criação ou edição. O controller de edição verifica a existência da avaliação pelo repository e define o título da página. O `NewAction` reaproveita essa estrutura para abrir uma nova avaliação. O acesso é protegido por `ADMIN_RESOURCE`.

* ***/Controller/Adminhtml/Review/Save.php:*** Recebe os dados por POST, valida os campos e verifica se o SKU informado pertence a um produto existente. Cria ou recupera a avaliação e salva por `ReviewRepositoryInterface`. Em caso de erro, apresenta uma mensagem ao usuário e preserva os valores preenchidos através de `DataPersistorInterface`.

* ***/Controller/Adminhtml/Review/Delete.php:*** Exclui a avaliação pelo método `deleteById` do repository, recebendo a ação por POST e apresentando uma mensagem de sucesso ou erro ao usuário.

* ***/Model/ReviewValidator.php:*** Centraliza a validação no servidor, exigindo autor, SKU e comentário, verificando os limites de tamanho dos textos e permitindo somente notas inteiras de 1 a 5 e valores válidos de aprovação.

* ***/Model/Review/DataProvider.php:*** Fornece os dados ao formulário utilizando o repository para carregar a avaliação existente. Também define os valores iniciais de uma nova avaliação e recupera os campos preservados após uma tentativa de salvamento com erro.

* ***/Block/Adminhtml/Review/Edit/BackButton.php e SaveButton.php:*** Configuram os botões **Voltar** e **Salvar avaliação**, permitindo retornar ao grid ou enviar os dados do formulário.

* ***/view/adminhtml/layout/webjump_productreviews_review_new.xml e webjump_productreviews_review_edit.xml:*** Inserem o UI Component do formulário na área de conteúdo das páginas de criação e edição.

* ***/view/adminhtml/ui_component/webjump_productreviews_form.xml:*** Declara os campos de autor, SKU, comentário, nota e aprovação, com validação de preenchimento obrigatório. Também conecta o formulário ao DataProvider, aos botões e à rota de salvamento.

* ***/view/adminhtml/ui_component/webjump_productreviews_listing.xml:*** Atualizado com o botão **Nova avaliação**, a coluna de ações por registro e o `exportButton` para exportação em CSV e Excel XML.

* ***/Ui/Component/Listing/Column/ReviewActions.php:*** Adiciona as opções **Editar** e **Excluir** em cada linha do grid. A exclusão exige confirmação e é enviada por POST.

* ***/Ui/Component/ExportButton.php:*** Estende o botão nativo de exportação para ocultá-lo quando o usuário não possui a permissão **Exportar avaliações de produtos**.

* ***/Plugin/ExportAuthorization.php e etc/adminhtml/di.xml:*** Registram e implementam a verificação de permissão nos controllers nativos de exportação CSV e Excel XML. Para o namespace do grid de avaliações, são exigidas as permissões de acesso às avaliações e de exportação, sem modificar os conversores dos demais grids.

* ***/etc/adminhtml/system.xml:*** Cria a seção **Webjump > Avaliações de produtos** em **Stores > Configuration**, contendo os campos **Exibir avaliações na loja** e **Nota mínima para exibição**, configuráveis por Store View.

* ***/etc/config.xml:*** Define os valores padrão da seção: exibição habilitada e nota mínima igual a `1`, permitindo utilizar o módulo antes de salvar uma configuração personalizada.

* ***/Model/Source/Rating.php:*** Fornece as opções de nota de 1 a 5 para o campo de configuração da nota mínima.

* ***/etc/acl.xml:*** Atualizado com o recurso `Webjump_ProductReviews::config`, responsável pelo acesso à seção de configuração, mantendo os recursos de avaliações e exportação criados no desafio 15.1.

* ***/Block/Product/Reviews.php:*** Consulta pelo repository até dez avaliações aprovadas mais recentes do SKU do produto atual, aplicando a nota mínima configurada. Quando a exibição está desabilitada, retorna uma lista vazia para impedir a renderização da seção.

* ***/Model/Review.php:*** Atualizado com `IdentityInterface` e uma tag de cache própria, também utilizada pelo bloco da loja, permitindo a invalidação das páginas relacionadas após alterações nas avaliações.

* ***/view/frontend/layout/catalog_product_view.xml:*** Insere o bloco de avaliações na página do produto e carrega o arquivo CSS responsável pela apresentação da seção.

* ***/view/frontend/templates/product/reviews.phtml:*** Renderiza autor, nota e comentário das avaliações retornadas pelo bloco, aplicando escape de saída. A seção é exibida somente quando existem avaliações disponíveis.

* ***/view/frontend/web/css/product-reviews.css:*** Define espaçamento, separadores e tipografia da seção. O uso de `clear: both` impede que as avaliações contornem a imagem do produto, mantendo o conteúdo em uma linha própria com largura completa.
