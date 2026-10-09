# Desafio 17.2 — Modo assombrado e minicart

* **Estrutura de Pastas:** O tema `Webjump/noite-assombrada` foi estendido para permitir que o cliente ligue e desligue uma versão mais escura da loja, mantendo sua escolha entre páginas. O minicart recebeu uma mensagem que acompanha a quantidade de itens. A estrutura abaixo apresenta os arquivos adicionados ou atualizados nesta etapa:

```text
app/design/frontend/Webjump/noite-assombrada/
├── requirejs-config.js
├── Magento_Theme/
│   ├── layout/
│   │   └── default.xml
│   └── templates/
│       └── html/
│           └── modo-assombrado.phtml
├── Magento_Checkout/
│   └── web/
│       └── template/
│           └── minicart/
│               └── content.html
├── i18n/
│   ├── pt_BR.csv
│   └── en_US.csv
└── web/
    ├── js/
    │   ├── modo-assombrado.js
    │   └── view/
    │       └── minicart-mixin.js
    └── css/
        └── source/
            └── _extend.less
```

* ***/requirejs-config.js:*** Carrega `js/modo-assombrado` em todas as páginas pela seção `deps` e registra o mixin sobre `Magento_Checkout/js/view/minicart`.

* ***/web/js/modo-assombrado.js:*** Recupera a escolha salva no `localStorage` e alterna a classe `modo-assombrado` no elemento `<html>`. Ao clicar no botão, atualiza a classe e grava o estado no navegador, mantendo a escolha durante a navegação sem recarregar a página ao ligar ou desligar.

* ***/Magento_Theme/layout/default.xml:*** Acrescenta o bloco do interruptor ao container `header.panel`, preservando as alterações de layout dos desafios anteriores.

* ***/Magento_Theme/templates/html/modo-assombrado.phtml:*** Renderiza o botão **Modo assombrado**, com texto traduzido por `__()` e escapado por `escapeHtml`. O atributo `aria-pressed` indica se o modo está ativo.

* ***/web/js/view/minicart-mixin.js:*** Estende o componente original do minicart e preserva `this._super()` na inicialização. Acrescenta o `ko.computed` chamado `mensagemAssombrada`, baseado no valor de `getCartParam('summary_count')`. A mensagem acompanha os dados atualizados pelo mecanismo nativo do carrinho e distingue três situações: vazio, de 1 a 12 itens e 13 ou mais. Os textos utilizam `$t()` para tradução.

* ***/Magento_Checkout/web/template/minicart/content.html:*** Cópia completa do template original instalado, acrescida de um parágrafo com o binding `text: mensagemAssombrada`. Preserva a apresentação dos produtos, subtotal, botões e controles de quantidade e remoção.

* ***/web/css/source/_extend.less:*** Acrescenta os estilos do interruptor, das superfícies mais escuras e da mensagem do minicart. As regras do modo são aplicadas pela classe no `<html>`, mantendo a identidade da campanha.

* ***/i18n/pt_BR.csv e en_US.csv:*** Preservam as traduções anteriores e acrescentam o rótulo **Modo assombrado** e as mensagens **Seu caldeirão está vazio**, **Itens no caldeirão: %1** e **Treze itens ou mais. Corajoso.** O parâmetro `%1` recebe a quantidade de itens.

### Por que utilizamos Mixin e não Map?

* **Mixin:** A necessidade era acrescentar uma mensagem ao minicart, mantendo seu funcionamento original. O mixin estende o componente e adiciona o computed; a chamada `this._super()` preserva a inicialização nativa responsável pelas atualizações do carrinho.

* **Map:** Substituiria a implementação JavaScript do minicart por outra, exigindo manter o componente inteiro quando apenas uma extensão era necessária. Por isso foi utilizado o mixin, conforme solicitado no desafio.

### Template Copiado e Manutenção

* **Original:** `vendor/magento/module-checkout/view/frontend/web/template/minicart/content.html`. A cópia no tema acrescenta a apresentação da mensagem por fallback e precisa ser comparada com o original em futuras atualizações do Magento. Nenhum arquivo do núcleo foi alterado.
