<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IPFS Credentials
    |--------------------------------------------------------------------------
    |
    | The credentials used to communicate with the IPFS daemon.
    | As default, the credentials for localhost are used.
    |
    | For more information see:
    | * https://docs.ipfs.io/install/
    |
    */
    'ipfs' => [
        'base_url' => 'host.docker.internal',
        'port' => 5001,
    ],

];
