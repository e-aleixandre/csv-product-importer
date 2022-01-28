# CSV Products importer
## Installation
```shell
git clone https://github.com/e-aleixandre/csv-product-importer.git
cd csv-product-importer
composer install
docker-compose up -d
symfony console doctrine:migrations:migrate
symfony console serve
```

The actual parsing of the CSV files is done asynchronously, so you'll also need to run a worker:
```shell
symfony console messenger:consume async -vv
```
## Import a CSV
Make a `POST` request to `http://localhost:8000/` with a csv file in the body param `inputFile`.
There's a `products.csv` example file in the public folder.

## Checking the results
This branch has no endpoints to check the database.