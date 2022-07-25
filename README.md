# symfony shop


**Warning ! This version is on front-end rework actualy**

`cd symfony_shop`

## INSTALL & UPDATE DEPENDENCIES

`composer install && composer update`

## CREATE BDD

`php bin/console doctrine:database:create`

## UPDATE BDD

`php bin/console doctrine:migrations:migrate`

## LOAD FIXTURE

`php bin/console doctrine:fixtures:load` 

## RUN SERV ON DEAMON

`symfony server:start -d`

# LOGIN INFO FOR TEST

id: `admin@gmail.com`

pwd : `password`
