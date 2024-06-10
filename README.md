# Setup

### Run composer install

```
composer install
```

### Run tests

```
vendor/bin/phpunit tests
```

### Config file

```
config/services.yaml
```

Please update config with required endpoints. e.g. exchange rate required access token.
Use your personal access token.

e.g.

```yml
services:
  exchangeratesapi:
    base_uri: "http://api.exchangeratesapi.io/v1/latest?access_key=[your own access token]"
```

# Run command for task

```bash
php app.php input.txt
```
