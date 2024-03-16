<?php
namespace Quantik24;

class PieceQuantik
{
    public const WHITE = 0;
    public const BLACK = 1;
    public const VOID = 0;
    public const CUBE = 1;
    public const CONE = 2;
    public const CYLINDRE = 3;
    public const SPHERE = 4;

    protected int $forme;
    protected int $couleur;
    //constructeur

    private function __construct(int $forme, int $couleur)
    {
        $this->forme = $forme;
        $this->couleur = $couleur;

    }

    public static function initVoid(): PieceQuantik
    {
        return new PieceQuantik(self::VOID, self::WHITE);
    }

    public static function initWhiteCube(): PieceQuantik
    {
        return new PieceQuantik(self::CUBE, self::WHITE);
    }

    public static function initBlackCube(): PieceQuantik
    {
        return new PieceQuantik(self::CUBE, self::BLACK);
    }

    public static function initWhiteCone(): PieceQuantik
    {
        return new PieceQuantik(self::CONE, self::WHITE);
    }

    public static function initBlackCone(): PieceQuantik
    {
        return new PieceQuantik(self::CONE, self::BLACK);
    }

    public static function initWhiteCylindre(): PieceQuantik
    {
        return new PieceQuantik(self::CYLINDRE, self::WHITE);
    }

    public static function initBlackCylindre(): PieceQuantik
    {
        return new PieceQuantik(self::CYLINDRE, self::BLACK);
    }

    public static function initWhiteSphere(): PieceQuantik
    {
        return new PieceQuantik(self::SPHERE, self::WHITE);
    }

    public static function initBlackSphere(): PieceQuantik
    {
        return new PieceQuantik(self::SPHERE, self::BLACK);
    }

    public function getForme(): int
    {
        return $this->forme;
    }

    public function getCouleur(): int
    {
        return $this->couleur;
    }

    public function getJson(): string {
        return '{"forme":'. $this->forme . ',"couleur":'.$this->couleur. '}';
    }

    public static function initPieceFromJson(string $json): PieceQuantik {
        $data = json_decode($json);
        return new PieceQuantik($data->forme, $data->couleur);
    }


    public static function initPieceQuantik(string|object $json): PieceQuantik {
        if (is_string($json)) {
            $props = json_decode($json, true);
            return new PieceQuantik($props['forme'], $props['couleur']);
        }
        else
            return new PieceQuantik($json->forme, $json->couleur);
    }



    public function __toString() : string {
        $s="(";
        if($this->forme == self::VOID){
            $s.="&nbsp;&nbsp;&nbsp;&nbsp;";
        }else{
            switch ($this->forme) {
                case self::CUBE :
                    $s .= "Cu";
                    break;
                case self::CONE :
                    $s .= "Co";
                    break;
                case self::CYLINDRE :
                    $s .= "Cy";
                    break;
                case self::SPHERE :
                    $s .= "Sp";
                    break;

            }
            $s.=",";
            if($this->couleur==self::WHITE){
                $s .= "W";
            } else{
                $s.="B";
            }
        }
        $s.=")";
        return $s;
    }



}
?>