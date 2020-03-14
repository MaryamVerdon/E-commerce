<?php

namespace App\Security\Token;

use App\Entity\Client;

class ClientTokenBuilder
{
    private $header = ['typ' => 'JWT', 'alg' => 'HS256'];
    private $payload = [];
    private $secret;

    public function __construct()
    {
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function addPayload($key, $value): self
    {
        $this->payload[$key] = $value;
        return $this;
    }

    public function setExpiration(int $timestamp): self
    {
        $this->payload['exp'] = $timestamp;
        return $this;
    }

    public function setCreatedAt(int $createdAt): self
    {
        $this->payload['creaAt'] = $createdAt;
        return $this;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;
        return $this;
    }

    public function build(){
        $header = json_encode($this->header);
        $payload = json_encode($this->payload);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return new JWT($base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature,$this->secret);
    }
}