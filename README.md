# Docker setup
- copy over .env.dist to .env and fill in EXCHANGE_RATE_APILAYER_KEY if need to run on live input
- **docker-compose build**
- **docker-compose up -d php-dev**
- inside container do **composer install**
# Unit tests
- run **./vendor/phpunit/phpunit/phpunit tests** inside container
# Live data
- add input.txt data or use input.txt.dist
- run **php app.php input.txt[.dist]** inside container
# Extra notes for live data case
- current bin lookup service provider rate limit is rather small / ~ 10/min according to doc, but in reality appears to be less than 10/hour
- exchange rates api limit is 100 requests per month per apikey, caching not implemented
