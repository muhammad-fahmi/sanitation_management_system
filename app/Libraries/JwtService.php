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

        $encode = JWT::encode($payload, $privateKey, 'EdDSA');
        session()->set('jwt', $encode);
        session()->set('jwt_public_key', $publicKey);
    }

    public function decode($encoded_jwt): array
    {
        $publicKey = session()->get('jwt_public_key');
        $decoded_jwt = JWT::decode($encoded_jwt, new Key($publicKey, 'EdDSA'));

        session()->set('user_id', $decoded_jwt->user_id);

        return json_decode(json_encode($decoded_jwt), true);
    }
}