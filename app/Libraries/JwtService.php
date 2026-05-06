<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    public function encode(array $payload): void
    {
        $keyPair = sodium_crypto_sign_keypair();

        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));

        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));

        $jwt_encode = JWT::encode($payload, $privateKey, 'EdDSA');

        session()->set('jwt', $jwt_encode);
        session()->set('key', $publicKey);
    }

    public function decode($encoded_jwt)
    {
        $publicKey = session()->get('key');
        $decoded_jwt = JWT::decode($encoded_jwt, new Key($publicKey, 'EdDSA'));

        return json_decode(json_encode($decoded_jwt), true);
    }
}