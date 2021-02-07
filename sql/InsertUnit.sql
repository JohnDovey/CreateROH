
BEGIN TRANSACTION; 
Drop table Unit;
CREATE TABLE IF NOT EXISTS 'Unit' (
        'UnitID'    INTEGER NOT NULL UNIQUE,
        'UnitName'  TEXT,
        PRIMARY KEY('UnitID')
);
INSERT INTO Unit (UnitID, UnitName) SELECT UnitID, Unit FROM personinforaw GROUP BY Unitid;
COMMIT; 