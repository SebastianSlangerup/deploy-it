# Deploy-it

## Tekniske krav
* Docker eller lign. (Projektet er lavet med Laravel Sail)
* ngrok

# Projektopsætning (step-by-step)
1. Klon projektet: `git clone git@github.com:SebastianSlangerup/deploy-it.git && cd deploy-it`
2. Opsæt .env filen: `cp .env.example .env`
3. Installer Sail: 
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```
Resten af guiden går ud fra at du har opsat et `sail` alias. Hvis du ikke har det, kan du udskifte `sail` med 
`./vendor/bin/sail` i de næste skridt.
4. Start projektet op: `sail up -d`<br>
5. Generer din app key: `sail artisan key:generate`<br>
6. Migrer din database: `sail artisan migrate:fresh --seed`<br>
7. Byg front-end assets: `sail npm run dev`

Hvis du oplever problemer med composer, så prøv at genkør `composer install` inde fra sail:
`sail composer install`
