<?php

namespace App\Security\Token;

use App\Entity\Client;

class ClientToken
{
    public static function create(Client $client, int $timestamp)
    {
        return (new ClientTokenBuilder)
            ->setCreatedAt((new \DateTime())->getTimestamp())
            ->setExpiration($timestamp)
            ->addPayload("client_email",$client->getEmail())
            ->setSecret("abc123")
            ->build();
    }

    public static function decode(string $token){
        $tabToken = explode(".",$token);
        return json_decode(str_replace(['-', '_', ''], ['+', '/', '='], base64_decode($tabToken[1])),true);
    }

    public static function validate(string $token, string $secret){
        $tabToken = explode(".",$token);
        $signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash_hmac('sha256', $tabToken[0] . "." . $tabToken[1], $secret, true)));
        return $tabToken[2] === $signature;
    }

    public static function dateValid(string $token){
        $tab = self::decode($token);
        return ($tab["creaAt"] + $tab["exp"]) > (new \DateTime())->getTimestamp();
    }
}