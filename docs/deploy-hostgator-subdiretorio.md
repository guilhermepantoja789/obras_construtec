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
2. Limpar e recriar cache (sem route:cache):
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan view:cache`
3. Validar status HTTP:
   - `curl -I https://gdoism.com.br/construtec/diario_obras/`
   - `curl -I https://gdoism.com.br/construtec/diario_obras/login`

Esperado:
- `/construtec/diario_obras/` responde `302` (ou `200`) e nao `405`.
- `/construtec/diario_obras/login` responde `200`.
