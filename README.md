## Symfony-gb - Test App

### Requirements:
- [Apache HTTP Server `2.4`](https://httpd.apache.org/docs/2.4/install.html)
- [PHP `>=8.1`](https://www.php.net/releases/8.1/en.php)
- [MySQL `8.0`](https://www.mysql.com/)
- [Symfony `6.3`](https://symfony.com/releases/6.3)
- [Node.js `18.17.1` (includes npm 9.6.7)](https://nodejs.org/en/download) or higher

### 1. Clone repository
```bash
    git clone git@github.com:alexfer/symfony-gb.git
```
### 2. Prepare configuration
You should
```bash
    cd symfony-gb/
    cp .env.original .env
```

### 3. Install dependencies use Composer
Use [Composer](https://getcomposer.org/) install to download and install the package.
```bash
    composer install
```

### 4. Creating a database and fill it with data
```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
```
### 4. Install JavaScript dependencies & Compile scripts
```bash
    npm install
    npm run dev --watch
```