<?php
namespace Quantik24;

require_once 'Player.php';
require_once 'AbstractGame.php';


class QuantikGame extends AbstractGame
{
    public PlateauQuantik $plateau;
    public ArrayPieceQuantik $piecesBlanches;
    public ArrayPieceQuantik $piecesNoires;
    public array $couleursPlayers;

    public function __construct(int $id, array $players)
    {
        $this->couleursPlayers = array();
        foreach ($players as $player) {
            $this->couleursPlayers[] = $player;
        }
        $this->gameID = $id;
        if (isset($this->couleursPlayers[0])) {
            $this->currentPlayer = $this->couleursPlayers[0]->getId();
        }

        $this->plateau = new PlateauQuantik();
        $this->piecesBlanches = ArrayPieceQuantik::initPiecesBlanches();
        $this->piecesNoires = ArrayPieceQuantik::initPiecesNoires();
        $this->gameStatus = 'constructed';
    }

    public function setId(int $id)
    {
        $this->gameID = $id;
    }

    public function getId(): int
    {
        return $this->gameID;
    }

    public function setPlayer2(Player $player)
    {
        $this->couleursPlayers[1] = $player;
    }

    public function setStatus(string $status)
    {
        $this->gameStatus = $status;
    }

    public function __toString(): string
    {
        if ($this->gameStatus == 'initialized') {
            return 'Partie n°' . $this->gameID . ' lancée par joueur ' . $this->couleursPlayers[0]->getName();
        }
        if ($this->gameStatus == 'waitingForPlayer') {
            if ($this->currentPlayer != $this->couleursPlayers[0]->getId())
                return 'Partie n°' . $this->gameID . ' en attente du joueur ' . $this->couleursPlayers[1]->getName();
            else
                return 'Partie n°' . $this->gameID . ' en attente du joueur ' . $this->couleursPlayers[0]->getName();
        }
        if ($this->gameStatus == 'finished') {
            return 'Partie n°' . $this->gameID . ' terminée';
        }
        return 'Partie n°' . $this->gameID . ' a un problème';

    }

    public function getJson(): string
    {
        $json = '{';
        $json .= '"plateau":' . $this->plateau->getJson();
        $json .= ',"piecesBlanches":' . $this->piecesBlanches->getJson();
        $json .= ',"piecesNoires":' . $this->piecesNoires->getJson();
        $json .= ',"currentPlayer":' . $this->currentPlayer;
        $json .= ',"gameID":' . $this->gameID;
        $json .= ',"gameStatus":' . json_encode($this->gameStatus);
        if (!isset($this->couleursPlayers[1]))
            $json .= ',"couleursPlayers":[' . $this->couleursPlayers[0]->getJson() . ']';
        else
            $json .= ',"couleursPlayers":[' . $this->couleursPlayers[0]->getJson() . ',' . $this->couleursPlayers[1]->getJson() . ']';
        return $json . '}';
    }

    public static function initQuantikGame(string $json): QuantikGame
    {
        $result = new QuantikGame(0, []);
        $tab = json_decode($json);
        $result->plateau = PlateauQuantik::initFromJson(json_encode($tab->plateau));
        $result->piecesBlanches = ArrayPieceQuantik::initFromJson(json_encode($tab->piecesBlanches));
        $result->piecesNoires = ArrayPieceQuantik::initFromJson(json_encode($tab->piecesNoires));
        $result->currentPlayer = $tab->currentPlayer;
        $result->gameID = $tab->gameID;
        $result->gameStatus = $tab->gameStatus;
        $result->couleursPlayers = [];
        foreach ($tab->couleursPlayers as $player)
            $result->couleursPlayers[] = Player::initPlayer(json_encode($player));
        return $result;
    }
}
