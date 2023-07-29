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

remove all migration files
```bash
  rm migrations/*.php
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



