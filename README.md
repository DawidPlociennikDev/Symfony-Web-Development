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