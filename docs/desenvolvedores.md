# Senhaunica-socialite

## Informações para desenvolvedores

### Senhaunica-faker

Em ambiente de desenvolvimento, ao invés de usar a autenticação por senha única, é possivel utilizar a biblioteca [senhaunica-faker](https://github.com/uspdev/senhaunica-faker). Essa biblioteca simula o servidor de autenticação retornando dados fake para a aplicação.

#### Direto na aplicação

Caso deseje ver todos parâmetros retornados na requisição, em `Server.php`:

```php
public function userDetails($data, TokenCredentials $tokenCredentials)
{
    dd($data);
}
```

### Debug

Outra possibilidade é configurar a variável `SENHAUNICA_DEBUG` como `true`. Isso salvará em JSON as informações obtidas de `<Servidor de OAuth1>/wsusuario/oauth/usuariousp` no diretório `storage/app/debug/oauth` por número USP.

Ex: para o número USP 3141592, os dados serão salvos em `storage/app/debug/oauth/3141592.json`.
