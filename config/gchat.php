<?php

return [
    'secret' => env('GCHAT_HS256_SECRET'),
    'issuer' => env('GCHAT_JWT_ISS', 'erp-auth'),
    'audience' => env('GCHAT_JWT_AUD', 'gchat'),
    'ttl' => (int) env('GCHAT_JWT_TTL', 3600),
];


