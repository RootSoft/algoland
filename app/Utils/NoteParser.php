<?php


namespace App\Utils;


use App\Domain\Collectible;
use ParagonIE\ConstantTime\Base64;

class NoteParser {

    /**
     * Parses a base64-encoded note.
     * @param string|null $note
     * @return Collectible
     */
    public static function parseNoteB64(?string $note)
    {
        $data = Base64::decode($note ?? '');
        $arr = json_decode($data, true) ?? [];

        return new Collectible($arr['name'] ?? '', $arr['description'] ?? '', $arr['image']?? '');
    }
}
