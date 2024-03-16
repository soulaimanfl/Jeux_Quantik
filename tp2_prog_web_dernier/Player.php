<?php
namespace Quantik24;

class Player{
    public string $name;
    public int $id;

    public function getId(): int {
        return $this->id;
    }


    public function setId($id) {
        $this->id = $id;
    }

    // Méthode pour obtenir le nom du joueur
    public function getName() {
        return $this->name;
    }

    // Méthode pour définir le nom du joueur
    public function setName($name) {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return '('.$this->id.')'.$this->name;
    }
    public function getJson():string {
        return '{"name":"'.$this->name.'","id":'.$this->id.'}';
    }

    public static function initPlayer(string $json): Player
    {
        $player = new Player();
        $object = json_decode($json);
        $player->setName($object->name);
        $player->setId($object->id);
        return $player;
    }


}