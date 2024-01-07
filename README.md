# Docker setup
- copy over .env.dist to .env and fill in EXCHANGE_RATE_APILAYER_KEY if need to run on live input
- docker-compose build
- docker-compose up -d php-dev
- inside container do composer install
# Live data
- add input.txt data or use input.txt.dist
- run php app.php input.txt[.dist] inside container
# Tests
- run ./vendor/phpunit/phpunit/phpunit tests inside container