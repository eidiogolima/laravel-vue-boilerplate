# laravel-vue-boilerplate-pt-br-claude

**Português** | [English](README.en.md)

> **Este é um boilerplate.** É um ponto de partida para novos projetos Laravel + Vue, pré-configurado para português do Brasil (pt-BR) e para desenvolvimento com o Claude Code. Não é uma aplicação pronta.

## Objetivo

A ideia deste boilerplate é produzir **código limpo**, com **manutenção facilitada** e **autogerenciado com IA**:

- **Código limpo:** convenções rígidas (Actions com uma única responsabilidade, SOLID, DRY, tipagem estrita, nomes descritivos) deixam o código previsível e fácil de ler.
- **Manutenção facilitada:** arquitetura simples, testes obrigatórios e verificações automáticas na pipeline evitam regressões e mantêm o padrão ao longo do tempo.
- **Autogerenciado com IA:** as regras do projeto ficam documentadas no `CLAUDE.md` e no `AGENTS.md`, para que agentes de IA como o Claude Code escrevam, revisem e mantenham o código seguindo sempre os mesmos padrões.

## O que está incluído

- **Backend:** Laravel 13, PHP 8.5, autenticação com Fortify (login, cadastro, redefinição de senha, verificação de e-mail, 2FA e passkeys)
- **Frontend:** Vue 3, Inertia v3, TypeScript, Tailwind CSS v4, Wayfinder
- **pt-BR por padrão:** `APP_LOCALE=pt_BR`, fuso horário `America/Sao_Paulo` e traduções pt-BR em `lang/pt_BR`
- **Qualidade:** tipagem estrita em todos os arquivos PHP, Pint, PHPStan (Larastan nível 7), Pest e `vp check` no frontend
- **CI:** workflow do GitHub Actions que roda todas as verificações e testes a cada push na `main` e em pull requests
- **Pronto para o Claude Code:** regras do projeto em `CLAUDE.md` e diretrizes do Laravel Boost em `AGENTS.md`

## Primeiros passos

```bash
composer setup
composer dev
```

O `composer setup` instala as dependências, cria o `.env`, gera a chave da aplicação, roda as migrations e faz o build do frontend.

## Scripts

| Comando             | Descrição                                                 |
| ------------------- | --------------------------------------------------------- |
| `composer dev`      | Inicia o ambiente de desenvolvimento                      |
| `composer lint`     | Formata o código PHP com o Pint                           |
| `composer test`     | Roda Pint, PHPStan e a suíte de testes                    |
| `composer ci:check` | Roda todas as verificações executadas pela pipeline de CI |
| `npm run check:fix` | Formata e faz o lint do código do frontend                |

## Convenções

Todas as convenções são obrigatórias e estão descritas no [CLAUDE.md](CLAUDE.md). Em resumo:

- Código em inglês. Português só é permitido em textos exibidos ao usuário, armazenados em `lang/pt_BR/messages.php` e `lang/pt_BR/errors.php`.
- A lógica de negócio fica em Actions com uma única responsabilidade. Services são apenas para integrações externas. Controllers não têm lógica de negócio.
- Form Requests e API Resources são obrigatórios, e listagens são sempre paginadas.
- `declare(strict_types=1)` e tipagem completa em todo o código. Nenhum comentário no código.
- Testes de funcionalidade e unitários são obrigatórios para toda mudança de comportamento.

## Iniciando um novo projeto

1. Copie ou clone este boilerplate.
2. Renomeie o pacote no `composer.json` e defina o `APP_NAME` no `.env`.
3. Substitua este README pela documentação do seu projeto.

## Créditos

Criado e mantido por [Diogo Lima](https://github.com/eidiogolima).
