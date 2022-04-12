# ABC-Company 
Symfony 5.4

## Gereksinimler
    - openssl

## Kurulum v1
Kurulumlarda kullanıcılacak default şifre 'testpassword'. Eğer şifreyi değiştirmek isterseniz lexik_jwt_authentication.yaml ve src\DataFixtures\AppFixtures.php dosyalarından değiştiriniz.
    
    mkdir -p config/jwt
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

## Kurulum v2
    composer install
    .env dosyasında veritabanı ayarlarınızı yapınız.
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load

## Test İşlemi
    Postman Collection'ı içe aktarınız (opsiyonel)

## Default Kullanıcı Bilgileri
    {
        "username": "customer1@abc-company.com",
        "password": "testpassword"
    }   
