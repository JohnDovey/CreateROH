Imports System.IO
Imports System.Data.SQLite

Module ExtractLatLong
            Dim dbLat As String = ""
            Dim dbLong As String = ""
            Dim tmpFile As String = ""

            Dim myURI As String
            Dim FindOne As Integer = -1
            Dim FindTwo As Integer = -1
            Dim FindThree As Integer = -1

             Dim MyPersonNumber As String
             Dim tmpSql As String

             Dim DBName As String = "./ROHData.Sql3"

              Dim RecordCounter as Integer = 0
              Dim FileCount as Integer = 0

                Public Const sqlCreatePersonImg As String = "CREATE TABLE IF NOT EXISTS 'PersonImages' ('id' INTEGER Not NULL UNIQUE, 'PersonNumber' INTEGER Not NULL DEFAULT 0, 'ImgUrl' TEXT, 'ImgUrlComplete' TEXT, 'ImgPath' TEXT, 'ImgThumbPath' TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
                Public Const sqlCreatePersonInfoRaw As String = "CREATE TABLE IF NOT EXISTS 'PersonInfoRaw' ( 'id' INTEGER NOT NULL UNIQUE, 'PersonNumber'	INTEGER NOT NULL UNIQUE, 'Name'	TEXT NOT NULL DEFAULT 'Unknown', 'FirstName'	TEXT, 'LastName'	TEXT, 'Rank'	TEXT, 'RankID'	INTEGER, 'Regiment'	TEXT, 'RegimentID'	INTEGER, 'Unit'	TEXT, 'UnitID'	INTEGER, 'DateDeath'	TEXT DEFAULT 'Unknown', 'CauseDeath'	TEXT DEFAULT 'Unknown', 'AddInfo'	TEXT, 'Country'	TEXT, 'CountryID'	INTEGER, 'Cemetery'	INTEGER, 'CemeteryID'	INTEGER, 'CemeteryLat'	TEXT, 'CemeteryLong'	TEXT, 'GraveRef' TEXT, 'DateChecked'	TEXT, 'Initials' TEXT, 'ServiceNo' TEXT, 'Age' TEXT, 'Locality' TEXT, 'LocalityID' INTEGER, 'Citation' TEXT, 'UnitID2' INTEGER, 'Unit2' TEXT,  PRIMARY KEY('id')) ;"
                Public Const sqlCreateRawweb As String = "CREATE TABLE IF NOT EXISTS 'rawweb' ('id' INTEGER NOT NULL, 'StartTime'	TEXT COLLATE RTRIM,  'EndTime'	TEXT COLLATE RTRIM, 'PageSize'	NUMERIC DEFAULT 0, 'PersonNumber'	NUMERIC DEFAULT 0, 'WebAddress'	TEXT, 'WebPage'	TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
                Public Const sqlCreateLastSeq As String = "CREATE TABLE IF NOT EXISTS 'LastSeq' ('id'	INTEGER NOT NULL DEFAULT 0, 'LastNumber'	NUMERIC NOT NULL DEFAULT 0, PRIMARY KEY('id'));"
                Public Const sqlCreateRegiment As String = "CREATE TABLE IF NOT EXISTS 'Regiment'  ( 'RegimentID'	INTEGER NOT NULL UNIQUE, 'RegimentName'	TEXT, PRIMARY KEY('RegimentID'));"
                Public Const sqlCreateRank As String = "CREATE TABLE IF NOT EXISTS 'Rank'  ( 'RankID'	INTEGER NOT NULL UNIQUE, 'RankName'	TEXT, 'RankDescription'	TEXT, PRIMARY KEY('RankID'));"
                Public Const sqlCreateUnit As String = "CREATE TABLE IF NOT EXISTS 'Unit'  ( 'UnitID'	INTEGER NOT NULL UNIQUE, 'UnitName'	TEXT, PRIMARY KEY('UnitID'));"
                Public Const sqlCreateCountry As String = "CREATE TABLE IF NOT EXISTS 'Country'  ( 'CountryID'	INTEGER NOT NULL UNIQUE, 'CountryName'	TEXT, PRIMARY KEY('CountryID'));"
                Public Const sqlCreateCemetery As String = "CREATE TABLE IF NOT EXISTS 'Cemetery' ( 'CemeteryID'	INTEGER NOT NULL UNIQUE, 'CemeteryName'	TEXT, 'Lat'	TEXT, 'Long'	TEXT, PRIMARY KEY('CemeteryID'));"

                Dim CntStartRecord as Integer

Dim MyDir as String
dim MySubDir as String
Dim CS as String 


Sub Main()
         MyDir = "."
        DBName =  "RohData.sql3"
        CS  = "URI=file:" & DBName
        CheckDatabase()
        MySubDir = MyDir & "/pages/"
        Directory.CreateDirectory(MySubDir)
        ExtractData()
End Sub

Sub ExtractData()
'The Search for Latitude and Longitude
            'var lat = '30.83823';
            'var Long = '28.94696';
            for FileCount = 1 to 10
            MyURI = "./pages/" & FileCount & ".html"
            myPersonNumber = FileCount

            tmpFile = File.ReadAllText(myURI)

            FindOne = tmpFile.IndexOf("var lat = '")
            If FindOne >= 0 Then
                FindTwo = tmpFile.IndexOf("'", FindOne + 11)
                ' dbLat = tmpFile.Substring(FindOne + 11, FindTwo)
                dbLat = tmpFile.Substring(FindOne + 11, FindTwo - (FindOne + 11))
                dbLat = trim(dbLat)

                Console.WriteLine("Latitude: " & dbLat & vbCrLf)
            End If

            FindOne = tmpFile.IndexOf("var long = '")
            If FindOne >= 0 Then
                FindTwo = tmpFile.IndexOf("'", FindOne + 12)
                'dbLong = tmpFile.Substring(FindOne + 12, FindTwo)
                dbLong = tmpFile.Substring(FindOne + 12, FindTwo - (FindOne + 11))
                dbLong = dbLong.Replace("'", Space(1))
                dbLong = trim(dbLong)

                Console.WriteLine("Longitude: " & dbLong & vbCrLf)
            End If
            If (dbLat.Length > 0) And (dbLong.Length) > 0 Then
                tmpSql = "update 'PersonInfoRaw'  set CemeteryLat = '" & dbLat & "', CemeteryLong = '" & dbLong & "' where PersonNumber = " & MyPersonNumber & ";"
                dbLat = dbLat.Replace("'", Space(1))
                Console.WriteLine(tmpSql)
                Write_Data_Record(tmpSql)
            End If
            ' End Lat/Long
            ' End Load
        Next FileCount

    End Sub

    Public Sub Write_Data_Record(mySQL as String)
        
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = mySQL
                cmd.ExecuteNonQuery()
            End Using
            con.Close()
        End Using
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

End Module