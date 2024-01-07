# Docker setup
- copy over .env.dist to .env and fill in EXCHANGE_RATE_APILAYER_KEY if need to run on live input
- docker-compose build
- docker-compose up -d php-dev
- inside container do composer install
# Unit tests
- run ./vendor/phpunit/phpunit/phpunit tests inside container
# Live data
- add input.txt data or use input.txt.dist
- run php app.php input.txt[.dist] inside container
# Extra notes for live data case
- note that one of provided transaction bins is no longer recognized by bin lookup service (in rather weird way) / i moved it to last / added specific exception
- current bin lookup service provider rate limit is rather small / ~ 10/min allegedly, rate limit backoff on 429 not handled in any way
- exchange rates api limit is 100 requests per month per apikey, caching not implemented
