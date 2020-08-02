-- #!sqlite
-- #{ easykits
-- #  { init
CREATE TABLE IF NOT EXISTS players
(
    player VARCHAR(26) NOT NULL,
    time INTEGER NOT NULL
);
-- #  }
-- #  { addplayer
-- #    :player string
-- #    :time int
INSERT OR IGNORE INTO players(player, time) VALUES (:player, :time);
-- #  }
-- #  { selectplayer
-- #    :player string
SELECT * FROM players WHERE player=:player;
-- #  }
-- #  { updateplayer
-- #    :player string
-- #    :time int
UPDATE players SET time=:time WHERE player=:player;
-- #  }
-- #}