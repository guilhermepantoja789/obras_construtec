# Deploy em subdiretorio (HostGator)

Este projeto roda em `https://gdoism.com.br/construtec/diario_obras/` via symlink:

- `~/public_html/construtec/diario_obras` -> `~/diario_obras_core/public`

Se a raiz do host redirecionar tudo para outro app (`g2mhome/public`), a URL do diario pode quebrar com erro `405 Method Not Allowed` em `GET /`.

## Regra obrigatoria em `~/public_html/.htaccess`

Garanta uma excecao antes do fallback geral:

```apache
RewriteRule ^construtec/diario_obras/?$ /construtec/diario_obras/index.php [L]

RewriteCond %{REQUEST_URI} !^/construtec/diario_obras(/|$)
RewriteCond %{REQUEST_URI} !^/g2mhome/public/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ g2mhome/public/$1 [L]
```

## Checklist pos-deploy

1. Confirmar symlink:
   - `readlink -f ~/public_html/construtec/diario_obras`
2. Garantir `~/public_html/construtec/.htaccess` (copiado do deploy):
   - `deploy/hostgator-construtec.htaccess` -> `~/public_html/construtec/.htaccess`
3. Limpar e recriar cache (sem route:cache):
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan view:cache`
4. Validar status HTTP:
   - `curl -I https://gdoism.com.br/construtec/diario_obras`
   - `curl -I https://gdoism.com.br/construtec/diario_obras/`
   - `curl -I https://gdoism.com.br/construtec/diario_obras/login`

Esperado:
- `/construtec/diario_obras` responde `301` ou `302` (nunca `403`).
- `/construtec/diario_obras/` responde `302` (ou `200`) e nao `405`.
- `/construtec/diario_obras/login` responde `200`.

## Erro 403 + jQuery mixed content

Se o navegador mostrar `403 Forbidden` e erros como:

- `Mixed Content: ... http://code.jquery.com/jquery-3.3.1.min.js`
- `jQuery is not defined`

isso **nao vem do Laravel**. E a pagina padrao de erro do HostGator (`/cgi-sys/`), que carrega jQuery em HTTP.

Causa comum: acessar `.../diario_obras` **sem barra final**. O Apache trata o symlink como diretorio e, com `DirectorySlash Off` ou sem regra de redirect, bloqueia com 403.

Correcoes:

1. Remover `DirectorySlash Off` de `public/.htaccess` (ja no repositorio).
2. Manter `~/public_html/construtec/.htaccess` com redirect `diario_obras` -> `diario_obras/`.
3. Manter a excecao em `~/public_html/.htaccess` antes do fallback para `g2mhome/public`.
