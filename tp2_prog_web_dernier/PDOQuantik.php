<?php

namespace Quantik24;
require_once 'Player.php';

use PDO;
use PDOStatement;
use Quantik24\Player;


class PDOQuantik
{
    private static PDO $pdo;

    public static function initPDO(string $sgbd, string $host, string $db, string $user, string $password, string $nomTable = ''): void
    {
        switch ($sgbd) {
            case 'pgsql':
                self::$pdo = new PDO('pgsql:host=' . $host . ' dbname=' . $db . ' user=' . $user . ' password=' . $password);
                break;
            default:
                exit ("Type de sgbd non correct : $sgbd fourni, 'mysql' ou 'pgsql' attendu");
        }

        // pour récupérer aussi les exceptions provenant de PDOStatement
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* requêtes Préparées pour l'entitePlayer */
    private static PDOStatement $createPlayer;
    private static PDOStatement $selectPlayerByName;

    /******** Gestion des requêtes relatives à Player *************/
    public static function createPlayer(string $name): Player
    {
        if (!isset(self::$createPlayer))
            self::$createPlayer = self::$pdo->prepare('INSERT INTO Player(name) VALUES (:name)');
        self::$createPlayer->bindValue(':name', $name, PDO::PARAM_STR);
        self::$createPlayer->execute();
        return self::selectPlayerByName($name);
    }

    public static function selectPlayerByName(string $name): ?Player
    {
        if (!isset(self::$selectPlayerByName))
            self::$selectPlayerByName = self::$pdo->prepare('SELECT * FROM Player WHERE name=:name');
        self::$selectPlayerByName->bindValue(':name', $name, PDO::PARAM_STR);
        self::$selectPlayerByName->execute();
        $player = self::$selectPlayerByName->fetchObject('Quantik24\Player');
        return ($player) ? $player : null;
    }

    /* requêtes préparées pour l'entiteGameQuantik */
    private static PDOStatement $createGameQuantik;
    private static PDOStatement $saveGameQuantik;
    private static PDOStatement $addPlayerToGameQuantik;
    private static PDOStatement $selectGameQuantikById;
    private static PDOStatement $selectAllGameQuantik;
    private static PDOStatement $selectAllGameQuantikByPlayerName;

    /******** Gestion des requêtes relatives à QuantikGame *************/

    /**
     * initialisation et execution de $createGameQuantik la requête préparée pour enregistrer une nouvelle partie
     */
    public static function createGameQuantik(string $playerName, string $json): void
    {
        if (!isset(self::$createGameQuantik)) {
            self::$createGameQuantik = self::$pdo->prepare('INSERT INTO QuantikGame (playerOne, gameStatus, json) VALUES (:playerOne, :gameStatus, :json)');
        }

        try {

            self::$pdo->beginTransaction();
            $player = self::selectPlayerByName($playerName);
            if (!$player) {
                echo "Le joueur n'existe pas.";
                return;
            }


            $playerId = $player->getId();
            $gameStatus = 'constructed';

            self::$createGameQuantik->bindValue(':playerOne', $playerId, PDO::PARAM_INT);
            self::$createGameQuantik->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
            self::$createGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
            self::$createGameQuantik->execute();

            self::$pdo->commit();


        } catch (PDOException $e) {
            self::$pdo->rollBack();
            echo "Erreur lors de la création de la partie : " . $e->getMessage();
        }
    }


    /**
     * initialisation et execution de $saveGameQuantik la requête préparée pour changer
     * l'état de la partie et sa représentation json
     */


    public static function saveGameQuantik(string $gameStatus, string $json, int $gameId): void
    {
        if (!isset(self::$saveGameQuantik)) {
            self::$saveGameQuantik = self::$pdo->prepare('UPDATE QuantikGame SET gameStatus = :gameStatus, json = :json WHERE gameId = :gameId');
        }

        try {
            self::$pdo->beginTransaction();


            self::$saveGameQuantik->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
            self::$saveGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
            self::$saveGameQuantik->bindValue(':gameId', $gameId, PDO::PARAM_INT);
            self::$saveGameQuantik->execute();

            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            echo "Erreur lors de la sauvegarde de la partie : " . $e->getMessage();
        }
    }



    /**
     * initialisation et execution de $addPlayerToGameQuantik la requête préparée pour intégrer le second joueur
     */
    public static function addPlayerToGameQuantik(string $playerName, int $gameId): void
    {
        if (!isset(self::$addPlayerToGameQuantik)) {
            self::$addPlayerToGameQuantik = self::$pdo->prepare('UPDATE QuantikGame SET json = :json, playerTwo = :playerTwo, gameStatus = :gameStatus WHERE gameId = :gameId');
        }

        try {

            self::$pdo->beginTransaction();
            $game = self::getGameQuantikById($gameId);
            $player = self::selectPlayerByName($playerName);

            if (!$player) {
                echo "Le joueur n'existe pas.";
                return;
            }
            $gameStatus = 'waitingForPlayer';
            $playerId = $player->getId();
            $game->setPlayer2($player);
            $game->setStatus($gameStatus);
            $json=$game->getJson();

            self::$addPlayerToGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
            self::$addPlayerToGameQuantik->bindValue(':playerTwo', $playerId, PDO::PARAM_INT);
            self::$addPlayerToGameQuantik->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
            self::$addPlayerToGameQuantik->bindValue(':gameId', $gameId, PDO::PARAM_INT);

            self::$addPlayerToGameQuantik->execute();

            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            echo "Erreur lors de l'ajout du joueur à la partie : " . $e->getMessage();
        }
    }



    /**
     * initialisation et execution de $selectAllGameQuantikById la requête préparée pour récupérer
     * une instance de quantikGame en fonction de son identifiant
     */
    public static function getGameQuantikById(int $gameId): ?QuantikGame
    {
        if (!isset(self::$selectGameQuantikById)) {
            self::$selectGameQuantikById = self::$pdo->prepare('SELECT json FROM QuantikGame WHERE gameId = :gameId');
        }

        try {
            self::$selectGameQuantikById->bindValue(':gameId', $gameId, PDO::PARAM_INT);
            self::$selectGameQuantikById->execute();

            $json = self::$selectGameQuantikById->fetchColumn();
            $game = QuantikGame::initQuantikGame($json);

            return $game;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération de la partie : " . $e->getMessage();
            return null;
        }
    }


    /**
     * initialisation et execution de $selectAllGameQuantik la requête préparée pour récupérer toutes
     * les instances de quantikGame
     */
    public static function getAllGameQuantik(): array
    {
        /* TODO */

        if (!isset(self::$selectAllGameQuantik)) {
            self::$selectAllGameQuantik = self::$pdo->prepare('SELECT * FROM QuantikGame');
        }
        $games=array();
        try {
            self::$selectAllGameQuantik->execute();

            // Récupérer toutes les lignes de la table QuantikGame
            $rows = self::$selectAllGameQuantik->fetchAll(PDO::FETCH_ASSOC);

            // Parcourir les lignes et créer des instances de QuantikGame
            foreach ($rows as $row) {
                $game = QuantikGame::initQuantikGame($row['json']);
                $games[] = $game;
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération de toutes les parties : " . $e->getMessage();
        }

        return $games;
    }



    /**
     * initialisation et execution de $selectAllGameQuantikByPlayerName la requête préparée pour récupérer les instances
     * de quantikGame accessibles au joueur $playerName
     * ne pas oublier les parties "à un seul joueur"
     */
    public static function getAllGameQuantikByPlayerName(string $playerName): array
    {


        if (!isset(self::$selectAllGameQuantikByPlayerName)) {
            self::$selectAllGameQuantikByPlayerName = self::$pdo->prepare('SELECT * FROM QuantikGame WHERE playerOne = (SELECT id FROM Player WHERE name = :playerName) OR playerTwo = (SELECT id FROM Player WHERE name = :playerName)');
        }
        $games = array();

        try {
            self::$selectAllGameQuantikByPlayerName->bindValue(':playerName',$playerName , PDO::PARAM_STR);
            self::$selectAllGameQuantikByPlayerName->execute();

            $lignes = self::$selectAllGameQuantikByPlayerName->fetchAll(PDO::FETCH_ASSOC);

            foreach ($lignes as $ligne) {

                $game = QuantikGame::initQuantikGame($ligne['json']);
                $games[] = $game;
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des parties du joueur : " . $e->getMessage();
        }

        return $games;
    }


    /**
     * initialisation et execution de la requête préparée pour récupérer
     * l'identifiant de la dernière partie ouverte par $playername
     */
    public static function getLastGameIdForPlayer(string $playerName): int
    {
        if (!isset(self::$getLastGameIdForPlayer)) {
            self::$getLastGameIdForPlayer = self::$pdo->prepare('SELECT MAX(gameId) FROM QuantikGame WHERE playerOne = (SELECT id FROM Player WHERE name = :playerName) OR playerTwo = (SELECT id FROM Player WHERE name = :playerName)');
        }
        $lastGameId = 0;

        try {
            self::$getLastGameIdForPlayer->bindValue(':playerName', $playerName, PDO::PARAM_STR);
            self::$getLastGameIdForPlayer->execute();

            $lastGameId = self::$getLastGameIdForPlayer->fetchColumn();
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération de l'identifiant de la dernière partie ouverte par le joueur : " . $e->getMessage();
        }

        return $lastGameId;
    }

    public static function getLastInsertId($name) {
        return self::$pdo->lastInsertId($name);
    }


}
