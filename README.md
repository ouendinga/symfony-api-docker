# SF4
Simple APIResfull in Symfony 4 with Dockers

## Dockers

### List
- sf4_apache (run web server)
- sf4_php (run php fpm)
- sf4_phpmyadmin (run db administration)
- sf4_mysql (run db server)

### Build
```bash
docker-compose build
```

### Up dockers (located in root directory)
```bash
docker-compose up -d
```

### Docker down (all assets)
```bash
docker stop $(docker ps -a -q)
```

## Installation
```bash
composer install
```

## Migration (in docker sf4_php)
```bash
docker exec -it sf4_php bash
cd sf4
php bin/console doctrine:migrations:migrate
```

## Create JWT certificade

### 1. Create the jwt directory
```bash
mkdir config/jwt
```

### 2. Generate the private certificate using the pass phrase "symfony4" (or whatever defined in .env -> JWT_PASSPHRASE)
```bash
openssl genrsa -out config/jwt/private.pem -aes256 4096
```
 
### 3. Generate the public certificate using the pass phrase "symfony4" (the same as the previous one)
```bash
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

### 4. Change the owner so that the apache has access
```bash
sudo chown www-data:www-data config/jwt/private.pem config/jwt/public.pem
```

## DB User
User: sf4
Password: sf4

## API Docs
{your_hosting}/api/doc

## Postman Tests
[https://www.getpostman.com/collections/7f3c71401275dc6c8896](https://www.getpostman.com/collections/7f3c71401275dc6c8896)
