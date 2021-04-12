<?php


namespace App\Domain;


class Collectible {

    private string $name;
    private string $description;
    private string $image;

    /**
     * Collectible constructor.
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description, string $image) {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getImage(): string {
        return $this->image;
    }

}
