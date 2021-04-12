<?php


namespace App;


use MyCLabs\Enum\Enum;

final class AlgorandProvider extends Enum {
    private const ALGOLAND = 'algoland';
    private const ALGOSIGNER = 'algosigner';
    private const MYALGO = 'myalgo';

    public static function ALGOLAND() {
        return new AlgorandProvider(self::ALGOLAND);
    }

    public static function ALGOSIGNER() {
        return new AlgorandProvider(self::ALGOSIGNER);
    }

    public static function MYALGO() {
        return new AlgorandProvider(self::MYALGO);
    }
}
