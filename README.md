# Todoist

## Installation

Run the first shell command to install composer dependencies

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

Prepare project

```shell
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail art key:generate
./vendor/bin/sail art migrate
./vendor/bin/sail art seed
```

## ToDo

- [x] basic creation of Todolists and Todolist Items
- [x] create factories and seeders
- [x] write tests
- [ ] add extras if there's time for it
