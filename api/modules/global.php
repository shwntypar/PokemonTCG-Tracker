<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/environment.php';
require_once __DIR__ . '/../modules/encryption.php';

class GlobalMethods
{
    protected $encryption;

    public function __construct()
    {
        $this->encryption = Encryption::getInstance();
    }

    protected function sendPayload($payload, $remarks, $message, $code)
    {
        return $this->encryption->prepareResponse($payload, $remarks, $message, $code);
    }
}