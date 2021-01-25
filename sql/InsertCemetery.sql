# Create Cemetery with Data from PersonInfoRaw
BEGIN TRANSACTION; 
Drop table cemetery;
CREATE TABLE IF NOT EXISTS 'Cemetery' ( 
       'CemeteryID' INTEGER NOT NULL UNIQUE, 
       'CemeteryName'  TEXT,
       'Lat'   TEXT,
       'Long'  TEXT,
  PRIMARY KEY('CemeteryID') 
);
INSERT INTO Cemetery (CemeteryID, CemeteryName, Lat, Long) SELECT CemeteryID, Cemetery, CemeteryLat, CemeteryLong FROM personinforaw GROUP BY CemeteryID;
COMMIT; 