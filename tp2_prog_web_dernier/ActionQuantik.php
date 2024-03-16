<?php
namespace Quantik24;

// Inclusion de la classe ArrayPieceQuantik pour pouvoir manipuler des ensembles de pièces de jeu.
include "ArrayPieceQuantik.php";

class ActionQuantik {
    // Attribut protégé représentant le plateau de jeu.
    protected PlateauQuantik $plateau;

    // Constructeur de la classe, prenant un plateau de jeu en paramètre et initialisant l'attribut $plateau.
    public function __construct(PlateauQuantik $plateau) {
        $this->plateau = $plateau;
    }

    // Méthode permettant de récupérer l'instance du plateau de jeu.
    public function getPlateau(): PlateauQuantik {
        return $this->plateau;
    }

    // Méthode évaluant si la pose d'une pièce est valide à une position donnée du plateau.
    public function isValidePose(int $rowNum, int $colNum, PieceQuantik $piece): bool {
        // Calcul de la zone du coin à partir des coordonnées de la position.
        $corner = PlateauQuantik::getCornerFromCoord($rowNum, $colNum);
        // Initialisation d'une pièce vide.
        $void = PieceQuantik::initVoid();

        // Vérification si la position est vide, et si la pose de la pièce est valide dans la colonne, la ligne, et le coin concerné.
        if ($this->plateau->getPiece($rowNum, $colNum) == $void &&
            $this->isPieceValide($this->plateau->getCol($colNum), $piece) &&
            $this->isPieceValide($this->plateau->getRow($rowNum), $piece) &&
            $this->isPieceValide($this->plateau->getCorner($corner), $piece)) {
            return true;
        }

        return false;
    }

    // Méthode privée statique vérifiant si l'ajout d'une pièce à un ensemble de pièces ne viole pas les règles du jeu.
    private static function isPieceValide(ArrayPieceQuantik $pieces, PieceQuantik $p):bool {
        for($i=0;$i<count($pieces);$i++){
            // Si une pièce de même forme mais de couleur différente est déjà présente, la pose n'est pas valide.
            if($pieces[$i]->getForme()===$p->getForme()&&$pieces[$i]->getCouleur()!==$p->getCouleur()){
                return false;
            }
        }
        return true;
    }

    // Méthode permettant de poser une pièce à une position spécifiée du plateau.
    public function posePiece(int $rowNum, int $colNum, PieceQuantik $piece):void {
        $this->plateau->setPiece($rowNum, $colNum, $piece);
    }

    // Méthode retournant une chaîne de caractères indiquant si le joueur a gagné, basée sur l'état actuel du jeu.
    public function __toString():String {
        $s = '<p>Vous avez ';
        for($i = 0; $i < PlateauQuantik::NBROWS; $i++) {
            if($this->isRowWin($i) || $this->isColWin($i) || $this->isCornerWin($i)) {
                $s = $s.'Gagné ^^</p>';
                return $s;
            }
        }
        $s = '';
        return $s;
    }

    // Méthode privée statique déterminant si une combinaison de pièces constitue une victoire.
    private static function isComboWin(ArrayPieceQuantik $pieces):bool{
        $somme=0;

        for($i=0;$i<count($pieces);$i++){
            // Somme des formes des pièces; une logique spécifique détermine si cette somme indique une victoire.
            $forme = $pieces[$i]->getForme();
            $somme += $forme;
        }
        if($somme==10){
            return true;
        }
        return false;
    }

    // Méthodes vérifiant si une ligne, une colonne, ou un coin spécifique contient une combinaison gagnante.



public function isRowWin(int $numRow): bool
    {
        $row = $this->plateau->getRow($numRow);
        return $this->isComboWin($row);
    }

    public function isColWin(int $numCol): bool
    {
        $col = $this->plateau->getCol($numCol);
        return $this->isComboWin($col);

    }

    public function isCornerWin(int $dir): bool
    {
        $cor = $this->plateau->getCorner($dir);

        return $this->isComboWin($cor);
    }
}
?>