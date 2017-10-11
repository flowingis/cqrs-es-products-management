<?php

function build(Idephix\Context $context)
{
    $context->local('bin/console broadway:event-store:schema:drop');
    $context->local('bin/console broadway:event-store:schema:init');
    $context->local('mysql -hmysql -uideato -pideato cqrs < /var/www/tests/Fixtures/readmodel.sql');
    $context->test();
}

function buildVagrant(Idephix\Context $context)
{
    $context->local('DATABASE_HOST=127.0.0.1 bin/console broadway:event-store:schema:drop');
    $context->local('DATABASE_HOST=127.0.0.1 bin/console broadway:event-store:schema:init');
    $context->local('mysql -uideato -pideato cqrs < /var/www/tests/Fixtures/readmodel.sql');
    $context->test();
}

function test(Idephix\Context $context)
{
    $context->local('vendor/bin/phpunit -c ./ --colors', false, 300);
}
