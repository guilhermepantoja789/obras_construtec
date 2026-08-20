# Construtec Obras

Sistema de gestão de obras da Construtec: landing institucional na raiz do domínio e área autenticada em `/app`.

## Stack Docker

A aplicação sobe com **Nginx + PHP-FPM + worker de fila + Cloudflared**. O MySQL fica **fora** da stack e é apontado por `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD`.

```bash
cp .env.example .env
# Gere a APP_KEY (localmente ou: docker compose run --rm app php artisan key:generate --show)
docker compose up -d --build
```

Acesso local (loopback): `http://127.0.0.1:18427` (`APP_PORT`, padrão 18427). Não fica exposto na LAN.

### MySQL no host

No `.env` use `DB_HOST=host.docker.internal`. O Compose já mapeia esse hostname para o host.

O MySQL precisa aceitar conexões além de `127.0.0.1`:

- `bind-address` em `0.0.0.0` (ou o IP da bridge Docker)
- usuário com permissão para o host do container (não só `localhost`)

Se o banco estiver em outro servidor, use o IP/hostname acessível a partir da rede Docker.

### Cloudflare Tunnel

O `cloudflared` roda **na mesma rede Docker** que o Nginx. Ele **não** usa `APP_PORT` nem `localhost` do servidor.

1. Zero Trust → **Networks** → **Tunnels** → Create a tunnel (Cloudflared).
2. Copie o token para `CLOUDFLARE_TUNNEL_TOKEN` no `.env`.
3. No túnel, **Public Hostname**:
   - Subdomain + Domain: o mesmo de `APP_URL` (ex. `www` + `construtec.app.br`)
   - Type: **HTTP**
   - URL / origem interna: **`http://nginx:80`**
4. `APP_URL=https://www.seudominio.com` e `SESSION_SECURE_COOKIE=true`.

Não use `localhost:18427`, `127.0.0.1:80` nem HTTPS na origem: o Nginx escuta **porta 80** só dentro da rede Compose; o TLS fica no Cloudflare.

Suba a stack com o token definido e o profile do túnel:

```bash
docker compose --profile tunnel up -d --build
```

Sem token, suba só a aplicação:

```bash
docker compose up -d --build
```

### Migrations

Por padrão o container **não** roda migrate. Para aplicar no boot:

```
RUN_MIGRATIONS=true
```

Ou execute uma vez:

```bash
docker compose exec app php artisan migrate --force
```

Uploads ficam no volume `laravel_storage`.

## Rotas

| URL | Destino |
| --- | --- |
| `/` | Site institucional |
| `/app/login` | Login do sistema |
| `/app/dashboard` | Painel (autenticado) |
| `/login` | Redirect para `/app/login` |
| `/dashboard` | Redirect para `/app/dashboard` |
| `/up` | Health check |

## Desenvolvimento local (sem Docker)

PHP 8.3, Composer, Node e MySQL:

```bash
composer setup
php artisan serve
```
