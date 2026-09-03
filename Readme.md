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