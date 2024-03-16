<?php
namespace Quantik24;
use ArrayAccess;
use Countable;



class ArrayPieceQuantik implements ArrayAccess, Countable
{
    protected array $piecesQuantiks;
    protected int $taille;

    public function __construct()
    {
        $this->taille = 0;
    }

    public function __toString(): string
    {
        $s = '<p>tableau : ';
        for ($i = 0; $i < $this->taille; $i++) {
            $s = $s . $this->piecesQuantiks[$i] . ' ';
        }
        $s = $s . '</p>';
        return $s;
    }
    public function getJson(): string
    {
        $json = "[";
        $jTab = [];
        foreach ($this->piecesQuantiks as $p)
            $jTab[] = $p->getJson();
        $json .= implode(',', $jTab);
        return $json . ']';
    }

    public static function initFromJson(string $json): ArrayPieceQuantik
    {
        $result = new ArrayPieceQuantik();
        $tab = json_decode($json);
        foreach ($tab as $p)
            $result->addPieceQuantik(PieceQuantik::initPieceFromJson(json_encode($p)));
        return $result;
    }


    public static function initArrayPieceQuantik(string|array $json): ArrayPieceQuantik
    {
        $apq = new ArrayPieceQuantik();
        if (is_string($json)) {
            $json = json_decode($json);
        }
        foreach ($json as $j)
            $apq[] = PieceQuantik::initPieceQuantik($j);
        return $apq;
    }


    public function getTaille(): int
    {
        return $this->taille;
    }

    public static function initPiecesNoires() : ArrayPieceQuantik{
        $piecesNoires = new ArrayPieceQuantik();
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackCone());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackCone());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackCube());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackCube());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackCylindre());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackCylindre());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackSphere());
        $piecesNoires->addPieceQuantik(PieceQuantik::initBlackSphere());
        return $piecesNoires;
    }
    public static function initPiecesBlanches() : ArrayPieceQuantik{
        $piecesBlanches = new ArrayPieceQuantik();
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteCone());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteCone());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteCube());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteCube());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteCylindre());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteCylindre());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteSphere());
        $piecesBlanches->addPieceQuantik(PieceQuantik::initWhiteSphere());
        return $piecesBlanches;
    }

    public function getPieceQuantik(int $pos): PieceQuantik
    {
        return $this->piecesQuantiks[$pos];
    }

    public function setPieceQuantik(int $pos, PieceQuantik $piece): void
    {
        $this->piecesQuantiks[$pos] = $piece;
    }

    public function addPieceQuantik(PieceQuantik $piece):void{
        self::setPieceQuantik($this->taille,$piece);
        $this->taille++;
    }

    public function removePieceQuantik(int $pos): void
    {
        array_splice($this->piecesQuantiks, $pos, 1);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->piecesQuantiks[$offset]);
    }

    public function offsetGet($offset): PieceQuantik
    {
        return $this->piecesQuantiks[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->piecesQuantiks[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->piecesQuantiks[$offset]);
    }

    // Implementing Countable interface method

    public function count(): int
    {
        return count($this->piecesQuantiks);
    }

    /**
     * @return ArrayPieceQuantik
     */








}


