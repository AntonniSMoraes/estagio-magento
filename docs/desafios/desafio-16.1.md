# Desafio 16.1 — A camada visual


## Decisões e Variáveis Sobrescritas

O `_theme.less` redefine as variáveis da biblioteca do Magento e substitui o arquivo de variáveis do tema pai pelo mecanismo nativo de fallback. Não importamos o `_theme.less` do Luma inteiro: os componentes continuam herdados e as variáveis não redefinidas mantêm os valores base da biblioteca. O `_extend.less` acrescenta regras e refinamentos após a carga dos componentes herdados.

| Variáveis | Valores Definidos | Motivo e Justificativa Técnica |
| :--- | :--- | :--- |
| `@page__background-color`, `@panel__background-color` | `#181020`, `#2a1b3d` | Fundo roxo escuro e superfícies que sustentam a identidade noturna da campanha. |
| `@primary__color`, `@secondary__color`, `@text__color`, `@text__color__muted` | `#f4eef8`, `#c6bbd2` | Textos claros com alta taxa de contraste para leitura confortável sobre superfícies escuras. |
| `@theme__color__primary`, `@theme__color__primary-alt` | `#ff8a3d`, `#a9ef85` | Laranja vibrante para destaques temáticos e verde ácido para estados de sucesso e hover. |
| `@link__color`, `@link__visited__color`, `@link__hover__color`, `@link__active__color` | `#ffad70`, `#d8b6ff`, `#a9ef85` | Estados interativos e preços destacados e facilmente identificáveis. |
| `@font-family__base`, `@font-size__base`, `@line-height__base` | `'Trebuchet MS', Arial, sans-serif`, `16px`, `1.6` | **Legibilidade no storefront:** preserva a experiência de compra com fontes de sistema neutras no corpo do texto. |
| `@heading__font-family__base`, `@heading__font-weight__base`, `@heading__color__base` | `'Creepster', Georgia, serif`, `400`, `#ffad70` | Aplicação da tipografia temática exclusivamente nos títulos e chamadas visuais. |
| `@button__*`, `@button-primary__*` | `#39264d` / `#ff8a3d` (fundos) | Botões secundários integrados à paleta roxa e primários em laranja chamativo, com hover em verde. |
| `@navigation__*`, `@submenu__*` | `#2a1b3d`, `#f4eef8` | Menu principal e submenus uniformizados com a identidade visual em mobile e desktop. |
| `@form-element-input__*` | `#ffffff`, `#181020`, `#8c739e` | Campos de formulário claros com texto escuro para facilitar a digitação e manter a usabilidade nativa. |
| `@border-color__base`, `@focus__color`, `@copyright__background-color` | `#665472`, `#a9ef85`, `#100a17` | Bordas sutis, anel de foco acessível de teclado (`:focus-visible`) e fechamento sóbrio do rodapé. |

### Regras Complementares e Extensões (`_extend.less`)
O `_extend.less` consome variáveis de dimensão da biblioteca (`@indent__s`, `@indent__l`, `@screen__s`, `@screen__m`) e o mixin nativo `.media-width`. Ele ajusta o cabeçalho, a transparência e as sombras do rodapé, a responsividade do menu mobile e cria os efeitos de transparência nos cards de produto sem recorrer a regras invasivas soltas.

### Tipografia Temática e Licenciamento
A fonte local `Creepster-Regular.ttf` foi mapeada via `@font-face` utilizando `@{baseDir}` e `font-display: swap`, evitando requisições externas em tempo de execução.

Nenhum arquivo do núcleo (`vendor/`) ou do tema nativo `Magento/luma` foi copiado ou modificado. A imagem de miniatura foi gerada exclusivamente para a exibição no painel administrativo.