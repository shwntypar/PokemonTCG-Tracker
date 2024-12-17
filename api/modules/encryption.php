<?php

class Encryption
{
    private static $instance = null;
    private $encryption_key;

    private function __construct()
    {
        try {
            $this->encryption_key = Environment::getInstance()->get('ENCRYPTION_KEY');
        } catch (Exception $e) {
            error_log("Encryption initialization error: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Encrypts data for API responses
     */
    public function encryptResponse($data): string
    {
        try {
            $jsonData = json_encode($data);
            $key = hex2bin($this->encryption_key);
            $iv = openssl_random_pseudo_bytes(16);

            $encrypted = openssl_encrypt(
                $jsonData,
                'aes-256-cbc',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            $combined = $iv . $encrypted;
            return base64_encode($combined);
        } catch (Exception $e) {
            error_log("Response encryption error: " . $e->getMessage());
            throw new Exception("Response encryption failed");
        }
    }

    /**
     * Decrypts data from client requests
     */
    public function decryptRequest($encryptedData)
    {
        try {
            $key = hex2bin($this->encryption_key);
            $combined = base64_decode($encryptedData);

            $iv = substr($combined, 0, 16);
            $encrypted = substr($combined, 16);

            $decrypted = openssl_decrypt(
                $encrypted,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            return json_decode($decrypted);
        } catch (Exception $e) {
            error_log("Request decryption error: " . $e->getMessage());
            throw new Exception("Request decryption failed");
        }
    }

    /**
     * Helper method to process incoming request data
     */
    public function processRequestData()
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json);

            if (isset($data->encrypted)) {
                return $this->decryptRequest($data->encrypted);
            }

            return $data;
        }

        return null;
    }

    /**
     * Helper method to prepare API response
     */
    public function prepareResponse($payload, $remarks, $message, $code): array
    {
        $status = ["remarks" => $remarks, "message" => $message];

        $finalPayload = ($code === 200 && $payload !== null)
            ? $this->encryptResponse($payload)
            : $payload;

        return [
            "status" => $status,
            "payload" => $finalPayload,
            "prepared_by" => "Denzel Manz Perez",
            "timestamp" => date_create()
        ];
    }
}