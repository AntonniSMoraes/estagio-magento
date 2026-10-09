# Desafio 17.1 — Contagem regressiva e selo assombrado

* **Estrutura de Pastas:** O tema `Webjump/noite-assombrada`, criado nos desafios anteriores, foi estendido para apresentar uma contagem regressiva na home e na página de produto, além de um selo nos produtos marcados para a campanha. A estrutura abaixo apresenta os arquivos adicionados ou atualizados nesta etapa:

```text
app/design/frontend/Webjump/noite-assombrada/
├── Magento_Theme/
│   ├── layout/
│   │   ├── cms_index_index.xml
│   │   └── catalog_product_view.xml
│   ├── templates/
│   │   └── html/
│   │       └── contador.phtml
│   └── web/
│       └── template/
│           └── contador.html
├── Magento_Catalog/
│   └── templates/
│       └── product/
│           └── list.phtml
├── Webjump_CatalogExtension/
│   └── templates/
│       └── product/
│           └── badge.phtml
├── i18n/
│   ├── pt_BR.csv
│   └── en_US.csv
└── web/
    ├── js/
    │   └── contador.js
    └── css/
        └── source/
            └── _extend.less
```

* ***/web/js/contador.js:*** Declara um módulo RequireJS com dependências de `uiComponent`, `ko` e `mage/translate`. O componente utiliza `ko.observable` para armazenar os segundos restantes e `ko.computed` para formatar a mensagem em dias, horas, minutos e segundos. O `setInterval` recalcula o tempo a cada segundo com base em `Date.now()`, sem recarregar a página. O resultado é limitado a zero, e o intervalo é encerrado quando a campanha termina. O método `destroy` limpa o intervalo e libera o computed.

* ***/Magento_Theme/templates/html/contador.phtml:*** Define o `scope` chamado `noiteContador` e inicializa o componente por `text/x-magento-init`, utilizando `Magento_Ui/js/core/app`. O componente recebe a data final pela configuração declarada no JSON, e o `getTemplate()` carrega seu template HTML.

* ***/Magento_Theme/web/template/contador.html:*** Apresenta o título pelo binding `translate` e a mensagem pelo binding `text`, mantendo a marcação separada da lógica do JavaScript.

* ***/Magento_Theme/layout/cms_index_index.xml e catalog_product_view.xml:*** Acrescentam o bloco do contador ao container `content` da home e da página de produto. As alterações são mescladas com os layouts existentes, sem substituir os arquivos de layout do núcleo.

* ***/Magento_Catalog/templates/product/list.phtml:*** Cópia completa do template original da listagem instalado no projeto, com uma condição que consulta `is_sustainable` em cada produto. Quando o valor está ativo, apresenta o selo **Oferta assombrada**, traduzido por `__()` e escapado. Quando está desativado ou ausente, não gera o selo nem seu elemento externo. Os demais trechos do original foram preservados, incluindo preço, avaliações, opções e formulário de compra.

* ***/Webjump_CatalogExtension/templates/product/badge.phtml:*** Sobrescreve por fallback o template do selo criado no desafio 14.1, preservando a consulta pelo `ViewModel/ProductBadge.php`. O texto e a apresentação foram adaptados à campanha, mantendo a condição de exibição somente para produtos marcados.

* ***/i18n/pt_BR.csv e en_US.csv:*** Preservam as traduções anteriores e acrescentam o título, a mensagem de tempo restante, o encerramento e o selo. As chamadas `$t()` utilizam as frases originais em inglês, enquanto os CSVs fornecem os textos escolhidos em português. Os parâmetros `%1`, `%2`, `%3` e `%4` recebem dias, horas, minutos e segundos, respectivamente.

* ***/web/css/source/_extend.less:*** Acrescenta a apresentação do contador com fundo roxo semelhante ao banner, borda laranja, título temático e mensagem centralizada. Também define a aparência do selo. As regras utilizam variáveis da biblioteca, como `@panel__background-color`, `@theme__color__primary`, `@text__color` e `@indent__l`, preservando os estilos anteriores do tema.

### Por que reutilizamos o atributo `is_sustainable`?

* **Reutilização solicitada:** O desafio exige aproveitar o atributo de produto criado por Data Patch na Sprint 7. No projeto, esse atributo é `is_sustainable`, criado por `Webjump/CatalogExtension/Setup/Patch/Data/AddSustainableAttribute.php` no desafio 14.1. Não foi necessário criar outro atributo ou executar novamente o patch.

* **Configuração no admin:** O campo aparece em **Catalog > Products > Edit > Content > Produto Sustentável**. O valor **Sim** habilita o selo da campanha; **Não** ou valor ausente não gera seu HTML. O atributo é booleano, opcional, global e possui `used_in_product_listing` habilitado para carregar nas coleções da listagem.

* **Adaptação para a campanha:** O atributo mantém seu significado original de sustentabilidade, mas nesta atividade sua marcação também identifica os produtos com selo assombrado. Essa escolha atende à reutilização exigida e foi documentada porque associa duas características de negócio diferentes.

### Data Final e Traduções

* **Encerramento da campanha:** A data configurada é `2026-11-01T00:00:00-03:00`, representando a meia-noite ao terminar 31/10/2026 em Brasília, conforme a intenção do exemplo do guia. O cálculo utiliza o relógio do navegador. A data aparece no padrão do JavaScript e na configuração do PHTML; o valor enviado pelo PHTML prevalece.

* **Textos do componente:** O título aparece como **Contagem regressiva para o Halloween**, a mensagem ativa como **Tempo Restante: ...** e o encerramento como **A noite de caça acabou! Volte no próximo ano para mais sustos e diversão!**. O selo apresenta **Oferta assombrada**. As quatro mensagens possuem entradas correspondentes nos CSVs, e as traduções JavaScript foram conferidas no `js-translation.json` gerado após o deploy.

### Decisões de Extensão e Manutenção

* **Layout e estilos:** A estrutura foi acrescentada por mesclagem de layout, e a apresentação foi estendida pelo `_extend.less`. O contador é um componente novo carregado pelo caminho `js/contador`, sem necessidade de alias em `requirejs-config.js` ou substituição de componentes nativos por `map`.

* **Templates copiados:** Foram utilizados o original `vendor/magento/module-catalog/view/frontend/templates/product/list.phtml` para a listagem e `app/code/Webjump/CatalogExtension/view/frontend/templates/product/badge.phtml` para o selo do detalhe. As cópias ficam no tema e precisam ser comparadas com os originais em futuras atualizações do Magento ou do módulo. Nenhum arquivo original em `vendor/` ou no Luma foi alterado.

### Testes Realizados

* **Atualização do contador:** O contador foi observado atualizando os segundos sem recarregar a página, e seu funcionamento foi confirmado durante a validação. A lógica também foi verificada com teste de relógio controlado.

* **Tratamento de data passada:** A configuração foi temporariamente alterada para uma data passada. O componente apresentou a mensagem de encerramento em português, sem números negativos. Após o teste e a captura da evidência, a data final da campanha foi restaurada nos dois arquivos.

* **Inicialização declarativa:** O PHTML utiliza `text/x-magento-init` com `Magento_Ui/js/core/app`; o JSON de inicialização e os XMLs de layout passaram na verificação de estrutura.

* **Produto marcado:** A camiseta com SKU `cam-est-26`, com **Produto Sustentável = Sim**, apresentou o selo na listagem e no detalhe. As duas telas foram registradas em prints.

* **Produto não marcado:** O campo da camiseta foi alterado para **Não** no admin. A listagem e o detalhe deixaram de apresentar o selo, sem espaço reservado para seu bloco e sem quebra da página. Outros produtos não marcados também foram registrados na listagem. No código, um valor ausente é tratado como `false` pela mesma condição.

* **Traduções:** O título e o selo foram conferidos na loja. As mensagens personalizadas de contagem e encerramento foram alinhadas aos CSVs, recompiladas e confirmadas no dicionário JavaScript gerado. O print antigo do CSV contém as mensagens anteriores à personalização; os arquivos atuais preservam os textos escolhidos para a campanha.

* **Verificações técnicas:** Os três templates PHTML passaram na validação de sintaxe pelo PHP do container. O módulo `Webjump_CatalogExtension` está habilitado. A comparação do template de listagem confirmou a preservação do original, com apenas a inclusão condicional do selo.

### Aplicação das Alterações

* **Sincronização e deploy:** Os arquivos foram sincronizados com o container e os estáticos gerados para `pt_BR` e `en_US`, utilizando os comandos abaixo na raiz do projeto Docker:

```bash
cd /root/Sites/magento
bin/copytocontainer app/design/frontend/Webjump/noite-assombrada
bin/magento setup:static-content:deploy -f --area frontend --theme Webjump/noite-assombrada pt_BR en_US
bin/magento cache:clean
```

* **Atualização de estilos e traduções:** Quando o deploy conservou conteúdo antigo, foram regenerados apenas os arquivos estáticos e pré-processados do tema. O cache de tradução foi limpo antes de gerar novamente o dicionário. No navegador, a atualização foi conferida com `Ctrl+F5`. Não foi necessário executar `setup:upgrade` para estas alterações, e nenhum arquivo gerado em `pub/static` foi editado manualmente.
