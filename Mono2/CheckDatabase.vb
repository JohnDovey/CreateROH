Imports System.IO
Imports System.Data.SQLite

Module CheckDatabase

 Public Const sqlCreatePersonImg As String = "CREATE TABLE IF NOT EXISTS 'PersonImages' ('id' INTEGER Not NULL UNIQUE, 'PersonNumber' INTEGER Not NULL DEFAULT 0, 'ImgUrl' TEXT, 'ImgUrlComplete' TEXT, 'ImgPath' TEXT, 'ImgThumbPath' TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
                Public Const sqlCreatePersonInfoRaw As String = "CREATE TABLE IF NOT EXISTS 'PersonInfoRaw' ( 'id' INTEGER NOT NULL UNIQUE, 'PersonNumber'	INTEGER NOT NULL UNIQUE, 'Name'	TEXT NOT NULL DEFAULT 'Unknown', 'FirstName'	TEXT, 'LastName'	TEXT, 'Rank'	TEXT, 'RankID'	INTEGER, 'Regiment'	TEXT, 'RegimentID'	INTEGER, 'Unit'	TEXT, 'UnitID'	INTEGER, 'DateDeath'	TEXT DEFAULT 'Unknown', 'CauseDeath'	TEXT DEFAULT 'Unknown', 'AddInfo'	TEXT, 'Country'	TEXT, 'CountryID'	INTEGER, 'Cemetery'	INTEGER, 'CemeteryID'	INTEGER, 'CemeteryLat'	TEXT, 'CemeteryLong'	TEXT, 'GraveRef' TEXT, 'DateChecked'	TEXT, 'Initials' TEXT, 'ServiceNo' TEXT, 'Age' TEXT, 'Locality' TEXT, 'LocalityID' INTEGER, 'Citation' TEXT, 'UnitID2' INTEGER, 'Unit2' TEXT,  PRIMARY KEY('id')) ;"
                Public Const sqlCreateRawweb As String = "CREATE TABLE IF NOT EXISTS 'rawweb' ('id' INTEGER NOT NULL, 'StartTime'	TEXT COLLATE RTRIM,  'EndTime'	TEXT COLLATE RTRIM, 'PageSize'	NUMERIC DEFAULT 0, 'PersonNumber'	NUMERIC DEFAULT 0, 'WebAddress'	TEXT, 'WebPage'	TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
                Public Const sqlCreateLastSeq As String = "CREATE TABLE IF NOT EXISTS 'LastSeq' ('id'	INTEGER NOT NULL DEFAULT 0, 'LastNumber'	NUMERIC NOT NULL DEFAULT 0, PRIMARY KEY('id'));"
                Public Const sqlCreateRegiment As String = "CREATE TABLE IF NOT EXISTS 'Regiment'  ( 'RegimentID'	INTEGER NOT NULL UNIQUE, 'RegimentName'	TEXT, PRIMARY KEY('RegimentID'));"
                Public Const sqlCreateRank As String = "CREATE TABLE IF NOT EXISTS 'Rank'  ( 'RankID'	INTEGER NOT NULL UNIQUE, 'RankName'	TEXT, 'RankDescription'	TEXT, PRIMARY KEY('RankID'));"
                Public Const sqlCreateUnit As String = "CREATE TABLE IF NOT EXISTS 'Unit'  ( 'UnitID'	INTEGER NOT NULL UNIQUE, 'UnitName'	TEXT, PRIMARY KEY('UnitID'));"
                Public Const sqlCreateCountry As String = "CREATE TABLE IF NOT EXISTS 'Country'  ( 'CountryID'	INTEGER NOT NULL UNIQUE, 'CountryName'	TEXT, PRIMARY KEY('CountryID'));"
                Public Const sqlCreateCemetery As String = "CREATE TABLE IF NOT EXISTS 'Cemetery' ( 'CemeteryID'	INTEGER NOT NULL UNIQUE, 'CemeteryName'	TEXT, 'Lat'	TEXT, 'Long'	TEXT, PRIMARY KEY('CemeteryID'));"
Dim MyDir as String
dim MySubDir as String
Dim CS as String 

Sub Main()
         MyDir = "."
        DBName =  "RohData.sql3"
        CS  = "URI=file:" & DBName
        CheckDatabase()
End Sub

Sub CheckDatabase()
        
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = sqlCreateRawweb
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreateLastSeq
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreateRegiment
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreateRank
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreateUnit
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreateCountry
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreateCemetery
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreatePersonImg
                cmd.ExecuteNonQuery()
                cmd.CommandText = sqlCreatePersonInfoRaw
                cmd.ExecuteNonQuery()

                'CONSTRAINT 'Regiment' FOREIGN KEY('RegimentID') REFERENCES Regiment), 
                'CONSTRAINT 'Unit' FOREIGN KEY('UnitID') REFERENCES Unit), 
                'CONSTRAINT 'Country' FOREIGN KEY('CountryID') REFERENCES Country), 
                'Constraint 'Rank' FOREIGN KEY('RankID') REFERENCES Rank)

                cmd.ExecuteNonQuery()
                'cmd.CommandText = "INSERT INTO LastSeq VALUES (0,100);"
                'cmd.ExecuteNonQuery()

                cmd.CommandText = "SELECT LastNumber from LastSeq where ID = 0;"
                RecordCounter = cmd.ExecuteScalar()
                CntStartRecord = RecordCounter

                'Dim rdr As SQLiteDataReader = cmd.ExecuteReader()
                'Using rdr
                ' While (rdr.Read())
                ' RecordCounter.Value = rdr("LastNumber")
                ' CntStartRecord.Text = rdr("LastNumber") + 1
                ' End While
                'End Using
            End Using
            con.Close()
        End Using
    End Sub