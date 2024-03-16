<?php

namespace Quantik24;


require_once 'ActionQuantik.php';
class QuantikUIGenerator extends AbstractUIGenerator
{
    public static function getDivPiecesDisponibles(ArrayPieceQuantik $array, int $pos = -1): string {
        $resultat ="<div class='piecesDispo'>";
        for($i = 0; $i < count($array) ; $i++) {
            $getClass = self::getButtonClass($array[$i]);
            $buttonCode = "<button class='$getClass' type='submit' name='active' disabled>";
            $buttonCode .= "<img src='/css/images/$getClass.png'/></button>";
            $resultat .= $buttonCode;
        }
        $resultat .= '</div>';
        return $resultat;
    }

    public static function getFormSelectionPiece(ArrayPieceQuantik $array): string {
        $resultat = "<div class='selectPiece'>";
        $resultat .= "<form action='poserPiece.php' method='get'>";

        for($i = 0; $i < $array->count(); $i++) {
            $getClass = self::getButtonClass($array[$i]);
            $resultat .= "<button class='$getClass' type='submit' name='position_piece' value='$i'><img src='css/images/$getClass.png'/></button>";
        }
        $resultat .= "</form></div>";

        return $resultat;
    }

    public static function getDivPlateauQuantik(PlateauQuantik $plateau): string {
        $resultat = "<div class='plateau'>";
        $resultat .= "<table>";
        for ($i = 0; $i < PlateauQuantik::NBROWS; $i++) {
            $resultat .= "<tr id='ligne'>";
            for ($j = 0; $j < PlateauQuantik::NBCOLS; $j++) {
                $getClass = self::getButtonClass($plateau->getPiece($i,$j));
                $image = $plateau->getPiece($i,$j);
                if($getClass!="vide"){
                    $image = "<img src='/css/images/$getClass.png'/>";
                }
                $resultat .= "<td class='nonVide' id='case'>$image</td>";
            }
            $resultat .= "</tr>"; // Fin de la ligne
        }
        $resultat .= "</table></div>"; // Fin du plateau
        return $resultat;
    }

    public static function getFormPlateauQuantik(PlateauQuantik $plateau, PieceQuantik $piece): string {
        $sRet = "<form action='consulter.php' method='get'>";
        $actions = new ActionQuantik($plateau);
        $void = PieceQuantik::initVoid();
        $sRet .= "<table>";
        for ($i = 0; $i < $plateau::NBROWS; $i++) {
            $sRet .= "<tr id='ligne'>";

            for ($j = 0; $j < $plateau::NBCOLS; $j++) {
                if($plateau->getPiece($i,$j)==$void){
                    if($actions->isValidePose($i,$j,$piece)){
                        $sRet .="<td id='case'class='validePose'><button type'submit' name='poserPlateau' value='$i-$j'>".$plateau->getPiece($i,$j)."</button>";
                    }else{
                        $sRet .="<td id='case'class='nonValide'><button type'submit' name='nonValide' disabled>".$plateau->getPiece($i,$j)."</button>";
                    }
                }else{
                    $getClass = self::getButtonClass($plateau->getPiece($i,$j));
                    $sRet .="<td id='case'class='nonVide'><button type'submit' name='nonVide' disabled><img src='css/images/$getClass.png'/></button>";
                }

                $sRet .= "</td>";
            }
            $sRet .= "</tr>";
        }

        $sRet .= "</table></form>";
        return $sRet;
    }

    public static function getButtonClass(PieceQuantik $pq) {
        if ($pq->getForme() === PieceQuantik::VOID) {
            return "vide";
        } else {
            $forme = "";
            switch ($pq->getForme()) {
                case PieceQuantik::CUBE:
                    $forme = "cube";
                    break;
                case PieceQuantik::CONE:
                    $forme = "cone";
                    break;
                case PieceQuantik::CYLINDRE:
                    $forme = "cylindre";
                    break;
                case PieceQuantik::SPHERE:
                    $forme = "sphere";
                    break;
                default:
                    break;
            }
            $couleur = $pq->getCouleur() === PieceQuantik::WHITE ? "white" : "black";
            return $forme ."_". $couleur;
        }
    }

    public static function getFormBoutonAnnulerChoixPiece(): string {
        $form = "<form action=\"choisirPiece.php\" method=\"post\">";
        $form .= "<input type=\"hidden\" name=\"action\" value=\"annulerChoixPiece\">";
        $form .= "<button class='replay annuler-btn' type=\"submit\">Annuler le choix de la pièce</button>";

        $form .= "</form>";
        return $form;
    }

    public static function getPageErreur(string $message): string
    {
        header("HTTP/1.1 400 Bad Request");
        $resultat = self::getDebutHTML("400 Bad Request");
        $resultat .= "<h2>$message</h2>";
        $resultat .= self::getLienRecommencer();
        $resultat .= self::getFinHTML();
        return $resultat;
    }


    public static function getDivMessageVictoire(int $couleur) : string {

        $resultat ="";
        if($couleur == PieceQuantik::BLACK){
            $resultat .= "<div class='blkVictory'> Les Noirs ont remporté la partie ";
        }elseif ($couleur == PieceQuantik::WHITE){
            $resultat .=  "<div class='whtVictory'> Les Blancs ont remporté la partie ";
        }
        $resultat .= self::getLienRecommencer()."</div>";
        return $resultat;
    }

    public static function getLienRecommencer():string {
        return "<form action='choisirPiece.php' method='post'>
        <input type=\"hidden\" name=\"recommencer\" value=\"recommencer\">
        <button class='replay' type='submit'>Recommencer ?</button></form>";
    }

    public static function getPageSelectionPiece(QuantikGame $quantik, int $couleurActive): string {

        $pageHTML = QuantikUIGenerator::getDebutHTML();
        $pageHTML .= '<table class=jeu>';
        $pageHTML.= "<tr><th>Pieces Blanches </th><th>Plateau</th><th>Menu</th></tr>";
        if ($couleurActive == PieceQuantik::BLACK) {
            $pageHTML .= "<tr><td >".self::getDivPiecesDisponibles($quantik->piecesBlanches, true)."</td>";
            $pageHTML .= "<td rowspan=3>".self::getDivPlateauQuantik($quantik->plateau)."</td>";
            $pageHTML .= "<td rowspan=3>".self::getFormBoutonHome()."</td></tr>";
            $pageHTML.= "<tr><th>Pieces Noires </th></tr>";
            $pageHTML .= "<tr><td>". self::getFormSelectionPiece($quantik->piecesNoires)."</td></tr>";
        } elseif ($couleurActive == PieceQuantik::WHITE) {
            $pageHTML .= "<tr><td >".self::getFormSelectionPiece($quantik->piecesBlanches)."</td>";
            $pageHTML .= "<td rowspan=3>".self::getDivPlateauQuantik($quantik->plateau)."</td>";
            $pageHTML .= "<td rowspan=3>".self::getFormBoutonHome()."</td></tr>";
            $pageHTML.= "<tr><th>Pieces Noires </th></tr>";
            $pageHTML .= "<tr><td>". self::getDivPiecesDisponibles($quantik->piecesNoires, false)."</td></tr>";
        }

        $pageHTML .= '</table>'.self::getFinHTML();
        return $pageHTML;
    }



    public static function getPagePosePiece(QuantikGame $quantik, int $couleurActive, int $posSelection): string {
        $pageHTML = QuantikUIGenerator::getDebutHTML('Jeu Quantik');
        $pageHTML .= '<table class=jeu>';
        $pageHTML.= "<tr><th>Pieces Blanches </th><th>Plateau</th><th>Menu</th></tr>";
        $piece = PieceQuantik::initVoid();
        $pageHTML .= "<tr><td >".self::getDivPiecesDisponibles($quantik->piecesBlanches)."</td>";

        if ($couleurActive == PieceQuantik::WHITE) {
            $piece = $quantik->piecesBlanches->getPieceQuantik($posSelection);
        } else{
            $piece = $quantik->piecesNoires->getPieceQuantik($posSelection);
        }

        $pageHTML .= "<td rowspan=3>".self::getFormPlateauQuantik($quantik->plateau, $piece)."</td>"
            ."<td rowspan=3>".self::getFormBoutonAnnulerChoixPiece()."</td></tr>";
        $pageHTML .= "<tr><th>Pieces Noires </th></tr><tr><td>". self::getDivPiecesDisponibles($quantik->piecesNoires)."</td></tr>"
            . self::getFinHTML();

        return $pageHTML;
    }
    public static function getPageConsulterPartie(QuantikGame $quantik, int $couleurActive): string {
        $pageHTML = QuantikUIGenerator::getDebutHTML();
        $pageHTML .= '<table class=jeu>';
        $pageHTML.= "<tr><th>Pieces Blanches </th><th>Plateau</th><th>Menu</th></tr>";
        $pageHTML .= "<tr><td >".self::getDivPiecesDisponibles($quantik->piecesBlanches)."</td>";
        $pageHTML .= "<td rowspan=3>".self::getDivPlateauQuantik($quantik->plateau)."</td>"
            ."<td rowspan=3>".self::getFormBoutonHome()."</td></tr>";
        $pageHTML .= "<tr><th>Pieces Noires </th></tr><tr><td>". self::getDivPiecesDisponibles($quantik->piecesNoires)."</td></tr>"
            . self::getFinHTML();

        return $pageHTML;


    }

    public static function getFormBoutonHome():string{
        $form="<form action='index.php' method='post'>";
        $form.="<button type='submit'><img src='css/images/exit.svg'/></button>";
        $form.="</form>";
        return $form;
    }












    public static function getPageVictoire(QuantikGame $quantik, int $couleurActive): string {
        $pageHTML = QuantikUIGenerator::getDebutHTML('Jeu Quantik');
        $pageHTML .= '<table class=jeu><tr><td>';
        $pageHTML .= self::getDivMessageVictoire($couleurActive)."</td></tr>"
            ."<tr><th>Plateau</th></tr><tr rowspan=3><td>". self::getDivPlateauQuantik($quantik->plateau)."</td></tr>"
            ."</table>". self::getFinHTML();

        return $pageHTML;
    }



}
