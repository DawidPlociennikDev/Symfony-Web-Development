## Symfony note

Create project via composer

```bash
  composer create-project symfony/skeleton my-project
```
    
Start project
```bash
  php -S 127.0.0.1:8000 -t public
```

Check project requirements
```bash
  symfony check:requirements
```

Twig install via composer
```bash
  composer require twig
```

Install database via composer
```bash
  composer require doctrine
```

All commands available in Symfony
```bash
  bin/console
```

Maker to create for example Controllers
```bash
  composer require maker
```

Maker create controller
```bash
  bin/console make:controller DefaultController
```

ORM composer
```bash
  composer require orm
```

create Database
```bash
  bin/console doctrine:database:create
```

create Entity
```bash
  bin/console make:entity User
```

create Migration
```bash
  bin/console make:migration
```

run Migration
```bash
  bin/console doctrine:migrations:migrate
```

symfony services list
```bash
  bin/console debug:container
```

composer logger install
```bash
  composer require logger
```

clear cache
```bash
  bin/console cache:clear
```

composer assets path 
```bash
  composer require symfony/asset
```

webpack for CSS and JavaScript
```bash
  npm install @symfony/webpack-encore --save-dev
  composer require encore
```

JQuery library
```bash
  npm install --save jquery
```

compile webpack
```bash
  ./node_modules/.bin/encore production
```

webpack live 
```bash
  ./node_modules/.bin/encore dev --watch
```

composer security bundle package 
```bash
  composer require symfony/security-bundle 
```

composer orm fixtures
```bash
  composer require orm-fixtures --dev 
```

doctrine list
```bash
  bin/console list doctrine
```

delete entire database, remove all migration files and generate new migration
```bash
  bin/console doctrine:schema:drop -n -q --force --full-database
```

delete entire database
```bash
  bin/console doctrine:schema:drop --force
```

remove all migration files
```bash
  rm migrations/*.php
```

create fixtures
```bash
  bin/console make:fixtures
```

run fixtures load
```bash
  bin/console doctrine:fixtures:load
```

profiler - dev toolbar
```bash
  composer require --dev symfony/profiler-pack
```

profiler dumper
```bash
  composer require --dev symfony/var-dumper
```

profiler panel dump
```bash
  composer req debug
```

extra bundle package
```bash
  composer require sensio/framework-extra-bundle
```

remove entire database, remove migrations folder, create migrations and run migrations
```bash
  bin/console doctrine:schema:drop -n -q --force --full-database && rm migrations/*.php && bin/console make:migration && bin/console doctrine:migrations:migrate -n -q
```

proxy manager bridge
```bash
  composer require symfony/proxy-manager-bridge
```

autowiring - list services autowiring
```bash
  bin/console debug:autowiring
```

debug container
```bash
  bin/console debug:container {container_name}
```

cache
```bash
  composer require symfony/cache
```

list all listeners
```bash
  bin/console debug:event-dispatcher
```

create subscriver class
```bash
  bin/console make:subscriber
```

package for forms
```bash
  composer require symfony/form
```

create form class
```bash
  bin/console make:form VideoFormType
```

update migration file
```bash
  bin/console doctrine:migrations:diff
```

validation package
```bash
  composer require symfony/validator doctrine/annotations
```

mailer
```bash
  composer require symfony/mailer
```

install phpunit package
```bash
  composer require --dev symfony/test-pack
```

create testing file
```bash
  php bin/console make:test
```

run test
```bash
  php bin/phpunit
```

something to test needed
```bash
  composer require --dev dbrekelmans/bdi && vendor/bin/bdi detect drivers
```

Security user
```bash
  bin/console make:user
```

package for security user
```bash
  composer require symfony/orm-pack symfony/form symfony/security-bundle symfony/validator
```

password hasher
```bash
  composer require symfony/password-hasher
```

checking security
```bash
  composer require symfony/security-bundle
```

expression-language package
```bash
  composer require symfony/expression-language
```

voter
```bash
  bin/console make:voter
```

unit test to test services
```bash
  bin/console make:unit-test
```

functional test to test controllers
```bash
  bin/console make:functional-test
```

install xdebug extension on linux
```bash
  sudo apt-get install php-xdebug
```

restart apache server on linux
```bash
  sudo service apache2 restart
```

coverage tests
```bash
  php bin/phpunit --coverage-text
```

translation
```bash
  composer require symfony/translation
```

annotations package for routes controller
```bash
  composer require annotations
```

install apache-pack htaccess
```bash
  composer require symfony/apache-pack
```
## Symfony deploy

change .env.local to .env and fill production data

optimize after git deploy project !IT'S NECESSERY!
```bash
  composer install --no-dev --optimize-autoloader
```

run new packages
```bash
  composer dump-autoload --optimize --no-dev --classmap-authoritative
```

create database
```bash
  bin/console doctrine:database:create
```

generate migration files
```bash
  bin/console doctrine:migrations:generate
```

run Migration
```bash
  bin/console doctrine:migrations:migrate
```

clear cache on the production 
```bash
  bin/console cache:clear --env=prod --no-debug
```


    