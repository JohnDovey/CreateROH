
BEGIN TRANSACTION; 
Drop table Country;
CREATE TABLE IF NOT EXISTS 'Country' (
        'CountryID'    INTEGER NOT NULL UNIQUE,
        'CountryName'  TEXT,
        PRIMARY KEY('CountryID')
);
INSERT INTO Country (CountryID, CountryName) SELECT CountryID, Country FROM personinforaw GROUP BY CountryID;
COMMIT; 