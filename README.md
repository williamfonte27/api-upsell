# Api UpSell - Open Exchange Rates

## How to Install

After cloning the project, run `composer install` command to install all dependencies.

## How to use

First run the command `php -S localhost:8000 -t public`, then access the url `http://localhost:8000/api/docs`
to learn how to consume the endpoints.

[Local Documentation](http://localhost:8000/api/docs)


## Endpoints
    currencies - List all currencies
    rates/{currency} - List all exchange rates for the reported currency
    rates-between/{currencyX}/{currencyY} - List exchange rate between CurrencyX and CurrencyY
