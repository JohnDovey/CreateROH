
BEGIN TRANSACTION; 
Drop table RANK;
CREATE TABLE IF NOT EXISTS 'Rank' (
        'RankID'    INTEGER NOT NULL UNIQUE,
        'RankName'  TEXT,
        PRIMARY KEY('RankID')
);
INSERT INTO Rank (RankID, RankName) SELECT RankID, Rank FROM personinforaw GROUP BY Rankid;
COMMIT; 