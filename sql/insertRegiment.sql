BEGIN TRANSACTION; 
Drop table Regiment;
CREATE TABLE IF NOT EXISTS 'Regiment' (
        'RegimentID'    INTEGER NOT NULL UNIQUE,
        'RegimentName'  TEXT,
        PRIMARY KEY('RegimentID')
);
INSERT INTO Regiment (RegimentID, RegimentName) SELECT RegimentID, Regiment FROM personinforaw GROUP BY RegimentID;
COMMIT; 