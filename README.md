# Site de Bilete folosind Laravel + InertiaJS (Portofoliu Rareș-Ionuț Boza)

Un site complet pentru management-ul evenimentelor cu funționalități de:

-   Conectare/Inregistrare
-   Organizare de evenimente
-   Gestioneare categoriilor de bilete
-   Gestionarea staffului
-   Achizitia biletelor folosind [Stripe](https://stripe.com/en-ro)
-   Validarea biletelor

## Prerequisites

-   [PHP](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [Node.js and npm](https://nodejs.org/)
-   [Laravel Valet](https://laravel.com/docs/10.x/valet) (Pentru MacOS si Linux)
-   [Docker](https://www.docker.com/)
-   [Stripe CLI](https://stripe.com/docs/stripe-cli)

## Project Setup

Clonează depozitul și navighează în directorul proiectului:

```bash
git clone https://github.com/bozarares/site-bilete.git
cd site-bilete
```

Instalează dependețele PHP și Node.js

```bash
composer install
npm install
```

Pornește server-ul mysql si phpmyadmin folosind docker

```bash
docker compose up -d
```

Creează fișierul .env

```bash
cp .env.example .env
```

Completează câmpurile

```env
APP_NAME=

STRIPE_SECRET_KEY=
STRIPE_PUBLIC_KEY=
STRIPE_WEBHOOK_SECRET_KEY=
# stripe.com

MAIN_SALT=
EVENTS_SALT=
STAFF_SALT=
TICKET_CATEGORY_SALT
# orice string random, este recomandat random.org

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
# le regăsești în ./mysql/...
```

Generează cheia aplicației

```bash
php artisan key:generate
```

Efectuează migrația bazei de date si seed-ul

```bash
php artisan migrate:fresh --seed
```

Pornește server-ul frontend vite

```bash
npm run dev
```

Configurează Laravel Valet

```bash
valet link nume_domeniu
valet secure nume_domeniu
```

Conectează-te la Stripe CLI

```bash
stripe login
```

Porneste server-ul webhook

```bash
stripe listen --forward-to https://nume_domeniu.test/webhook
```

Accesează aplicația la URL-ul

```bash
https://nume_domeniu.test
```

## Utilizare

Există 3 conturi autogenerate cu roluri diferite:

```
email: organiser@example.com
parola: password
```

```
email: staff_edit@example.com
parola: password
```

```
email: staff_validate@example.com
parola: password
```

Cardul bancar de test pentru Stripe este

```
Numar card: 4242 4242 4242 4242
Data expirare: 12/34 (Sau orice data in viitor)
CVV: 123 (Sau orice numar de 3 cifre)
```

### Citirea codului QR al biletului cu camera foto nu a fost implementată, dar biletul se poate valida manual accesând pagina de validare a biletelor si introducând codul biletului (din url sau baza de date).
