<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Algod Credentials
    |--------------------------------------------------------------------------
    |
    | The credentials used to communicate with the Algod daemon.
    | As default, the credentials are used for the TestNet hosted by PureStake.
    | PureStake API Token header: X-API-Key
    | Algorand API Token header: X-Algo-API-Token
    |
    | For more information see:
    | * https://developer.algorand.org/docs/build-apps/setup/
    | * https://www.purestake.com/technology/algorand-api/
    |
    */
    'algod' => [
        'api_url' => 'https://api.testnet.algoexplorer.io',
    ],

    /*
    |--------------------------------------------------------------------------
    | Indexer Credentials
    |--------------------------------------------------------------------------
    |
    | The credentials used to communicate with the Indexer daemon.
    | As default, the credentials are used for the TestNet hosted by PureStake.
    | PureStake API Token header: X-API-Key
    | Algorand API Token header: X-Indexer-API-Token
    | 'api_url' => 'https://testnet-algorand.api.purestake.io/idx2',
    | For more information see:
    | * https://developer.algorand.org/docs/build-apps/setup/
    | * https://www.purestake.com/technology/algorand-api/
    |
    */
    'indexer' => [
        'api_url' => 'https://api.testnet.algoexplorer.io/idx2',
    ],
];
