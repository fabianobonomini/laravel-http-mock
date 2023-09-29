# laravel-http-mock

this is a simple example of a laravel http mock inside a test


# Lunch test

```console
docker-compose exec app php artisan test --testdox
```
# Create a test 

## Unit test
```console
docker-compose exec app php artisan make:test ExampleTest --unit
```

## Feature test
```console
docker-compose exec app php artisan make:test ExampleTest
```

# Execute a test 
## Filter test
```console
docker-compose exec app php artisan test --filter ExampleTest
```

# Create a controllr
```console
docker-compose exec app php artisan make:controller FormController
```

# Access to the docker 
 ```console
 docker-compose exec app bash
 ```