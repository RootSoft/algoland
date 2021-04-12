<?php


namespace App\Services;


use Session;

class WalletService {

    public function storeAccount(string $provider, string $publicAddress, array $seedphrase = array()) {
        Session::put('provider', $provider);
        Session::put('address', $publicAddress);
        Session::put('seedphrase', $seedphrase);
    }
}
