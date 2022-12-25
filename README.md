# MyBook - API

## Sobre

Esta é uma API de uma rede social (Facebook Clone) desenvolvida com finalidade de exercitar conhecimentos em **PHP** e seu framework **Laravel**.

---

## Funcionalidades

- Autenticação:
    - Registro de usuário.
    - Login.
    - Refresh JWT Token.
    - Logout.
- Usuários:
    - Atualização de dados de usuário.
    - Atualização de Avatar.
    - Atualização de Capa.
    - Seguir usuário.
    - Listar Seguidores e Seguindo.
    - Busca de usuários por nome.
- Feed:
    - Visualização de feed do início.
    - Visualização de feed do usuário.
- Posts:
    - Criação de posts como textos ou upload de imagens.
    - Opção de Like nos Posts
    - Criação de comentários nos posts.

---

## Instalação

Clonagem do repositório:

```git clone https://github.com/raferreira96/mybook-api.git```

Instalação das dependências do projeto:

```composer install```

Fazer uma cópia do arquivo `.env.example` e renomear para `.env`, editando os valores para o banco de dados de acordo com a sua situação, após isso executar o comando de criação das tabelas:

```php artisan migrate:fresh```

Criação da chave JWT:

```php artisan jwt:secret```

Executar o servidor:

```php artisan serve```

---

## Tecnologias

- **[Laravel](https://laravel.com/)**
- **[JWT Auth](https://github.com/PHP-Open-Source-Saver/jwt-auth)**

## Especificações

- **PHP**
    - ^ 8.2
- **Composer**
    - ^ 2.4.4

## Contribuições
- **[Rafael Ferreira](https://github.com/raferreira96)**