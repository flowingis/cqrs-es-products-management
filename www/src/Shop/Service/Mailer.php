<?php

namespace Shop\Service;


interface Mailer
{
    public function send($from, $to, $message);
}
