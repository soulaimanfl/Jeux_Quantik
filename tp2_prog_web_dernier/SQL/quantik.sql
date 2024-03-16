DROP TABLE IF exists QuantikGame;
DROP TABLE IF exists Player;

CREATE TABLE Player (
                        id serial PRIMARY KEY,
                        name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE QuantikGame(
                            gameId serial PRIMARY KEY,
                            playerOne int NOT NULL REFERENCES Player(id),
                            playerTwo int NULL REFERENCES Player(id),
                            gameStatus VARCHAR(100) NOT NULL DEFAULT 'constructed' CHECK ( gameStatus IN ('constructed', 'initialized', 'waitingForPlayer', 'finished')),
                            json text NOT NULL,
                            CONSTRAINT players CHECK ( playerOne<>playerTwo)
);

