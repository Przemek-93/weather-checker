# Weather-Checker APP

Technologies:
[PHP 8.1](https://www.php.net/releases/8.1/en.php),
[Symfony 6.2](https://symfony.com/doc/6.2/index.html)

Weather-Checker
The project is a test task carried out for the purpose of recruitment. \
The main functionality is integration with [`IMGW API`](https://danepubliczne.imgw.pl/apiinfo) and displaying temperature readings, humidity, wind force etc. for the specified city. \
The project has a WEB version and an API that is authenticated with a JWT token.

### Preparing environment

* Execute `cp .env.dist .env` to get local copy of .env file and adjust the environment.


* Execute `docker-compose build` and then `docker-compose up -d` to build and run docker environment.


* Execute `docker-compose exec php composer install` to install all composer dependencies.


* Execute `docker-compose exec php bin/console lexik:jwt:generate-keypair` to generate lexik pem keys.


* Execute `docker-compose exec php bin/console doctrine:migrations:migrate` to apply all changes to database.


### Application commands

In order to access the project's API, we must first create a user that we will use for authorization.\
Run `docker-compose exec php bin/console app:create-user --email test@test.pl --password secret` to create new user by given email and password.


### API

The project has an API, all endpoints are defined in the documentation [`api_specification.yaml`](./docs/api_specification.yaml). \
To use the API, we need to provide a JWT token - so firstly we have to create user by `app:create-user` command, then login and fetch bearer token. In each api call we have to use bearer token as authorization type. \
API supports different case sizes and special characters, stations can only be cities from Poland. \
API available at: `http://localhost:8080/api`.


### WEB

The project has an WEB layer, one view has been added displaying the reading results for a given city. \
WEB available at: `http://localhost:8080/pogoda/{station}` for example`http://localhost:8080/pogoda/warszawa`.


### Testing application

* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) \
  execute: `docker-compose exec php vendor/bin/phpcs -p` to show violations. \
  config: [`phpcs.xml`](./phpcs.xml.dist)


* [PHPUnit](https://phpunit.de/) \
  execute: `docker-compose exec php vendor/bin/phpunit` to run all phpunit tests. \
  config: [`phpunit.xml`](./phpunit.xml.dist)
