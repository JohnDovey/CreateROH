Imports System.IO
Imports System.Data.SQLite
Imports System.Net

Module ExtractLatLong
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


            Dim dbNamefld As String = ""
            Dim dbFirstName As String = ""
            Dim dbInitials As String = ""
            Dim dbServiceNo As String = ""
            Dim dbRank As String = ""
            Dim dbRankID As String = ""
            ' Regiment
            Dim dbRegiment As String = ""
            Dim dbRegimentID As String = ""
            'Unit
            Dim dbUnit As String = ""
            Dim dbUnitID As String = ""
            Dim dbUnit2 As String = ""
            Dim dbUnitID2 As String = ""

            'Date of Death
            Dim dbDateOfDeath As String = ""
            Dim dbAge As String = ""
            Dim dbCauseOfDeath As String = ""
            Dim dbAddInfo As String = ""
            ' Country
            Dim dbCountry As String = ""
            Dim dbCountryID As String = ""
            ' Cemetery
            Dim dbCemetery As String = ""
            Dim dbCemeteryID As String = ""
            Dim dbGraveReference As String = ""
            ' Locality
            Dim dbLocality As String = ""
            Dim dbLocalityID As String = "0"
            ' Other
            Dim dbCitation As String = ""

Sub ExtractData()
'The Search for Data 
    for FileCount = 1 to 10
            MyURI = "./pages/" & FileCount & ".html"
            myPersonNumber = FileCount
 
           ' Load array with all <td> elements
            Dim FindInfo(50) As String
            Dim cnt As Integer = 0
            Dim highCnt as Integer = 0

        ' Loop through each line in array returned by ReadAllLines.
        Dim line As String
        For Each line In File.ReadAllLines(myURI)
            FindOne = line.IndexOf("<td")
            if FindOne > 0 Then
                FindInfo(cnt) = line
                FindInfo(cnt) = Replace(FindInfo(cnt), Chr(9), Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), vbTab, Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), vbLf, Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), vbCr, Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), "<td>", "")
                FindInfo(cnt) = Replace(FindInfo(cnt), "</td>", "")
                
                FindInfo(cnt) = Trim(FindInfo(cnt))
                'console.WriteLine(cnt & " : " & FindInfo(cnt))
                 cnt += 1
                 highCnt = cnt
            end if
        Next

             For cnt = 0 To (highCnt - 1)
                ' TD Element:0- >  <div class="tableLabel">Name:</div>
                ' TD Element:  1- >  ABBEY
                FindOne = FindInfo(cnt).IndexOf(">Name:")
                If FindOne >= 0 Then
                    dbNamefld = FindInfo(cnt + 1) ' No change
                    dbNamefld = dbNamefld.Replace("'", WebUtility.HtmlEncode("'"))
                    console.WriteLine("dbNamefld: " & dbNameFld)
                End If

                ' TD Element: 2- >  <div class="tableLabel">Given Name:</div>
                ' TD Element: 3- >  DAVID ROBERT
                FindOne = FindInfo(cnt).IndexOf(">Given Name:")
                If FindOne >= 0 Then
                    dbFirstName = FindInfo(cnt + 1) ' No change
                    dbFirstName = dbFirstName.Replace("'", WebUtility.HtmlEncode("'"))
                    dbFirstName = StrRep(dbFirstName) ' Remove everythng between <>
                    console.WriteLine("dbFirstName: " & dbFirstName)
                End If

                ' TD Element: 4- >  <div class="tableLabel">Initials:</div>
                ' TD Element: 5- >  D R
                FindOne = FindInfo(cnt).IndexOf(">Initials:")
                If FindOne >= 0 Then
                    dbInitials = FindInfo(cnt + 1) ' No change
                    dbInitials = dbInitials.Replace("'", WebUtility.HtmlEncode("'"))
                    dbInitials = StrRep(dbInitials) ' Remove everythng between <>
                    console.WriteLine("dbInitials: " & dbInitials)
                End If
                ' TD Element:6- >  <div class="tableLabel">Service No:</div>
                ' TD Element: 7- >  7357
                FindOne = FindInfo(cnt).IndexOf(">Service No:")
                If FindOne >= 0 Then
                    dbServiceNo = FindInfo(cnt + 1) ' No change
                    dbServiceNo = dbServiceNo.Replace("'", WebUtility.HtmlEncode("'"))
                    dbServiceNo = StrRep(dbServiceNo) ' Remove everythng between <>
                    console.WriteLine("dbServiceNo: " & dbServiceNo)
                End If
                ' TD Element: 8- >  <div class="tableLabel">Rank:</div>
                ' TD Element: 9- >  Private<div style='float:right;'>Other Casualties of this <a href='view-paginated.php?page=1&rank=429' target='new'>Rank</a></div>
                'Rank=
                ' dbRank = FindInfo(5) 'left up to <div
                ' dbRankNo = FindInfo(5) ' rank=???' (5)
                FindOne = FindInfo(cnt).IndexOf("Rank:")
                If FindOne >= 0 Then
                    dbRank = FindInfo(cnt + 1)
                    dbRankID = FindInfo(cnt + 1)

                    FindTwo = dbRank.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbRank = dbRank.Substring(0, FindTwo)
                        dbRank = dbRank.Replace("'", WebUtility.HtmlEncode("'"))
                        dbRank = StrRep(dbRank) ' Remove everythng between <>
                        console.WriteLine("dbRank: " & dbRank)
                    End If
                    'dbRankID= GetStr(dbRankID,"rank=","'")
                    FindTwo = dbRankID.IndexOf("rank=")
                    If FindTwo >= 0 Then
                        FindThree = dbRankID.IndexOf("'", FindTwo)
                        dbRankID = dbRankID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
                        dbRankID = StrRep(dbRankID) ' Remove everythng between <>
                        console.WriteLine("dbRankID: " & dbRankID)
                    End If
                End If
                ' TD Element: 10- >  <div class="tableLabel">Regiment:</div>
                ' TD Element: 11- >  South African Infantry<div style='float:right;'>Other Casualties from this <a href='view-paginated.php?page=1&regiment=807' target='new'>Regiment</a></div>
                FindOne = FindInfo(cnt).IndexOf(">Regiment:")
                If FindOne >= 0 Then
                    dbRegiment = FindInfo(cnt + 1)
                    dbRegimentID = FindInfo(cnt + 1)

                    FindTwo = dbRegiment.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbRegiment = dbRegiment.Substring(0, FindTwo)
                        console.WriteLine("dbRegiment: " & dbRegiment)
                    End If
                    FindTwo = dbRegimentID.IndexOf("regiment=")
                    If FindTwo >= 0 Then
                        FindThree = dbRegimentID.IndexOf("'", FindTwo)
                        dbRegimentID = dbRegimentID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                        dbRegimentID = StrRep(dbRegimentID) ' Remove everythng between <>
                        console.WriteLine("dbRegimentID: " & dbRegimentID)
                    End If
                    dbRegiment = dbRegiment.Replace("'", WebUtility.HtmlEncode("'"))

                End If
                'Unit
                ' TD Element: 12- >  <div class="tableLabel">Unit:</div>
                ' TD Element: 13- >  2nd Regt.<div style='float:right;'>Other Casualties from this <a href='view-paginated.php?page=1&unit=925' target='new'>Unit</a></div>
                FindOne = FindInfo(cnt).IndexOf(">Unit:")
                If FindOne >= 0 Then
                    dbUnit = FindInfo(cnt + 1)
                    dbUnitID = FindInfo(cnt + 1)

                    FindTwo = dbUnit.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbUnit = dbUnit.Substring(0, FindTwo)
                        dbUnit = StrRep(dbUnit) ' Remove everythng between <>
                        console.WriteLine("dbUnit: " & dbUnit)
                    End If
                    FindTwo = dbUnitID.IndexOf("unit=")
                    If FindTwo >= 0 Then
                        FindThree = dbUnitID.IndexOf("'", FindTwo)
                        dbUnitID = dbUnitID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
                        dbUnitID = StrRep(dbUnitID) ' Remove everythng between <>
                        console.WriteLine("dbUnitID: " & dbUnitID)
                    End If
                    dbUnit = dbUnit.Replace("'", WebUtility.HtmlEncode("'"))
                End If
                ' Unit2
                FindOne = FindInfo(cnt).IndexOf(">Unit 2:")
                If FindOne >= 0 Then
                    dbUnit2 = FindInfo(cnt + 1)
                    dbUnitID2 = FindInfo(cnt + 1)

                    FindTwo = dbUnit2.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbUnit2 = dbUnit2.Substring(0, FindTwo)
                        dbUnit2 = StrRep(dbUnit2) ' Remove everythng between <>
                        console.WriteLine("dbUnit2: " & dbUnit2)
                    End If
                    FindTwo = dbUnitID2.IndexOf("unit2=")
                    If FindTwo >= 0 Then
                        FindThree = dbUnitID2.IndexOf("'", FindTwo)
                        dbUnitID2 = dbUnitID2.Substring(FindTwo + 6, FindThree - (FindTwo + 6))
                        dbUnitID2 = StrRep(dbUnitID2) ' Remove everythng between <>
                        console.WriteLine("dbUnitID2: " & dbUnitID2)
                    End If
                    dbUnit2 = dbUnit2.Replace("'", WebUtility.HtmlEncode("'"))
                End If

                'Date of Death

                ' TD Element: 14- >  <div class="tableLabel">Date of Death:</div>
                ' TD Element: 15- >  1916-12-20<div style='float:right;'>Other Casualties on this            <a href ='view-paginated.php?page=1&DoD_YYYY=1916&DoD_MM=12&DoD_DD=20' target='new'>Date</a></div>
                FindOne = FindInfo(cnt).IndexOf(">Date of Death:")
                If FindOne >= 0 Then
                    dbDateOfDeath = FindInfo(cnt + 1) ' Left  till <div
                    FindTwo = dbDateOfDeath.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbDateOfDeath = dbDateOfDeath.Substring(0, FindTwo)
                        dbDateOfDeath = StrRep(dbDateOfDeath) ' Remove everythng between <>
                        console.WriteLine("dbDateOfDeath: " & dbDateOfDeath)
                    End If
                    dbDateOfDeath = dbDateOfDeath.Replace("'", WebUtility.HtmlEncode("'"))
                End If

                '<td style="width:20%;" valign='top'><div class="tableLabel">Citations:</div></td>
                ' <td valign ='top'>***Not yet accepted for War Grave Status by CWGC, need service file</td>
                FindOne = FindInfo(cnt).IndexOf(">Citations:")
                If FindOne >= 0 Then
                    dbCitation = FindInfo(cnt + 1) ' No change
                    dbCitation = StrRep(dbCitation) ' Remove everythng between <>
                    console.WriteLine("dbCitation: " & dbCitation)
                    dbCitation = dbCitation.Replace("'", WebUtility.HtmlEncode("'"))
                End If

                ' TD Element: 16- >  <div class="tableLabel">Age:</div>
                ' TD Element: 17- >  37
                FindOne = FindInfo(cnt).IndexOf(">Age:")
                If FindOne >= 0 Then
                    dbAge = FindInfo(cnt + 1) ' No change
                    dbAge = StrRep(dbAge) ' Remove everythng between <>
                    console.WriteLine("dbAge: " & dbAge)
                End If
                'TD Element:18- >  <div class="tableLabel">Cause of Death:</div>
                'TD Element: 19- >  Died of phthisis, at No. 1 General Hospital Wynberg
                FindOne = FindInfo(cnt).IndexOf(">Cause of Death:")
                If FindOne >= 0 Then
                    dbCauseOfDeath = FindInfo(cnt + 1) ' No change
                    dbCauseOfDeath = StrRep(dbCauseOfDeath) ' Remove everythng between <>
                    console.WriteLine("dbCauseOfDeath: " & dbCauseOfDeath)
                    dbCauseOfDeath = dbCauseOfDeath.Replace("'", WebUtility.HtmlEncode("'"))

                End If
                ' TD Element:20- >  <div class="tableLabel">Additional<br>Information:</div>
                ' TD Element: 21- >  <div>Son of Mrs. Elizabeth Ann And the late Thomas Abbey, of 146, Cathcart Rd., Queenstown, Cape Province. His brother also died in service</div>
                FindOne = FindInfo(cnt).IndexOf(">Additional<")
                If FindOne >= 0 Then
                    dbAddInfo = FindInfo(cnt + 1) ' No change
                    dbAddInfo = StrRep(dbAddInfo) ' Remove everythng between <>
                    
                    dbAddInfo = dbAddInfo.Replace("<div>", "")
                    dbAddInfo = dbAddInfo.Replace("</div>", "")
                    dbAddInfo = dbAddInfo.Replace("'", WebUtility.HtmlEncode("'"))
                    console.WriteLine("dbAddInfo: " & dbAddInfo)
                End If
                'Country
                ' TD Element: 22- >  <div class="tableLabel">Country:</div>
                ' TD Element: 23- >  South Africa<div style='float:right;'>Other Casualties commemorated in <a href='view-paginated.php?page=1&country=72' target='new'>South Africa</a></div>
                FindOne = FindInfo(cnt).IndexOf(">Country:")
                If FindOne >= 0 Then
                    dbCountry = FindInfo(cnt + 1)
                    dbCountryID = FindInfo(cnt + 1)

                    FindTwo = dbCountry.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbCountry = dbCountry.Substring(0, FindTwo)
                        dbCountry = StrRep(dbCountry) ' Remove everythng between <>
                        console.WriteLine("dbCountry: " & dbCountry)
                    End If
                    FindTwo = dbCountryID.IndexOf("country=")
                    If FindTwo >= 0 Then
                        FindThree = dbCountryID.IndexOf("'", FindTwo)
                        dbCountryID = dbCountryID.Substring(FindTwo + 8, FindThree - (FindTwo + 8))
                        dbCountryID = StrRep(dbCountryID) ' Remove everythng between <>
                        console.WriteLine("dbCountryID: " & dbCountryID)
                    End If
                End If

                ' TD Element:24- >  <div class="tableLabel">Locality:</div>
                ' TD Element: 25- >  Western Cape<div style='float:right;'>Other Casualties commemorated in <a href='view-paginated.php?page=1&locality=1' target='new'>Western Cape</a></div>
                FindOne = FindInfo(cnt).IndexOf(">Locality:")

                If FindOne >= 0 Then
                    dbLocality = FindInfo(cnt + 1)
                    dbLocalityID = FindInfo(cnt + 1)

                    FindTwo = dbLocality.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbLocality = dbLocality.Substring(0, FindTwo)
                        dbLocality = StrRep(dbLocality) ' Remove everythng between <>
                        console.WriteLine("dbLocality: " & dbLocality)
                    End If
                    FindTwo = dbLocalityID.IndexOf("locality=")
                    If FindTwo >= 0 Then
                        FindThree = dbLocalityID.IndexOf("'", FindTwo)
                        dbLocalityID = dbLocalityID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                        dbLocalityID = StrRep(dbLocalityID) ' Remove everythng between <>
                        console.WriteLine("dbLocalityID: " & dbLocalityID)
                    End If
                    dbLocality = dbLocality.Replace("'", WebUtility.HtmlEncode("'"))
                    If Val(dbLocality) < 0 Then dbLocalityID = 0
                End If

                ' TD Element:26- >  <div class="tableLabel">Cemetery:</div>
                ' TD Element: 27- >  CAPE TOWN(PLUMSTEAD) CEMETERY<div style='float:right;margin-top:10px;"'>Other Casualties commemorated in this <a href='view-paginated.php?page=1&cemetery=463' target='new'>Cemetery</a></div> 
                FindOne = FindInfo(cnt).IndexOf(">Cemetery:")
                If FindOne >= 0 Then
                    dbCemetery = FindInfo(cnt + 1)
                    dbCemeteryID = FindInfo(cnt + 1)

                    FindTwo = dbCemetery.IndexOf("<div")
                    If FindTwo >= 0 Then
                        dbCemetery = dbCemetery.Substring(0, FindTwo)
                        dbCemetery = StrRep(dbCemetery) ' Remove everythng between <>
                        console.WriteLine("dbCemetery: " & dbCemetery)
                    End If
                    FindTwo = dbCemeteryID.IndexOf("cemetery=")
                    If FindTwo >= 0 Then
                        FindThree = dbCemeteryID.IndexOf("'", FindTwo)
                        dbCemeteryID = dbCemeteryID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                        dbCemeteryID = StrRep(dbCemeteryID) ' Remove everythng between <>
                        console.WriteLine("dbCemeteryID: " & dbCemeteryID)
                    End If
                    dbCemetery = dbCemetery.Replace("'", WebUtility.HtmlEncode("'"))
                    
                End If
                ' TD Element:29- >  <div class="tableLabel">Grave Reference:</div>
                ' TD Element: 30- >  Bl. UR. 15.
                FindOne = FindInfo(cnt).IndexOf(">Grave Reference:")
                If FindOne >= 0 Then
                    dbGraveReference = FindInfo(cnt + 1) ' No change
                    dbGraveReference = StrRep(dbGraveReference) ' Remove everythng between <>
                        console.WriteLine("dbGraveReference: " & dbGraveReference)
                End If

            Next cnt
            Console.WriteLine("End Find Data: " & FileCount)

            If Val(dbLocalityID) < 0 Then dbLocalityID = "0"
            If Val(dbRankID) < 0 Then dbRankID = "0"
            If Val(dbRegimentID) < 0 Then dbRegimentID = "0"
            If val(dbUnitID) < 1 Then dbUnitID = "0"
            If dbUnit.Length < 1 Then dbUnit = "Unknown"
            If Val(dbUnitID) < 0 Then dbUnitID = "0"
            If dbUnit2.Length < 1 Then dbUnitID2 = ""
            If Val(dbUnitID2) < 0 Then dbUnitID2 = "0"
            If Val(dbCountryID) < 0 Then dbCountryID = "0"
            If Val(dbCemeteryID) < 0 Then dbCemeteryID = "0"

            Dim tmpSql As String = ""

            tmpSql = "insert into 'PersonInfoRaw' ('id','PersonNumber','FirstName','LastName','Rank','RankID','Regiment','RegimentID','Unit','UnitID','DateDeath','CauseDeath','AddInfo','Country','CountryID','Cemetery','CemeteryID','GraveRef','Initials','ServiceNo','Age','Locality','LocalityID', 'Citation', UnitID2, Unit2) VALUES (null,'" & "" & MyPersonNumber & "','" & dbFirstName & "','" & dbNamefld & "','" & dbRank & "', " & dbRankID & ",'" & dbRegiment & "'," & dbRegimentID & ",'" & dbUnit & "'," & dbUnitID & ",'" & dbDateOfDeath & "','" & dbCauseOfDeath & "','" & dbAddInfo & "','" & dbCountry & "'," & dbCountryID & "," & dbCemetery & "'," & dbCemeteryID & ",'" & dbGraveReference & "', '" & dbInitials & "','" & dbServiceNo & "','" & dbAge & "','" & dbLocality & "'," & dbLocalityID & ", '" & dbCitation & "', " & dbUnitID2 & ",'" & dbUnit2 & "');"
            
            Console.WriteLine(tmpSql)
           'Write_Data_Record(tmpSql)
            
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

 Private Function StrRep(xstr as String) as String
        Dim xsub As String
        Dim xstart As Integer = xstr.IndexOf("<")
        Dim xend As Integer = xstr.IndexOf(">")
        if xstart >= 0 and xend >= 0 then
            xsub  = xstr.Substring(xstart, (xend - xstart) + 1)
            xstr  = xstr.Replace(xsub, String.Empty)
        end if

            xstr.Replace("<","")
            xstr.Replace(">","")
      
    Return xstr
    End Function


    Private Function GetStr(xstr as string, StartStr as string, EndStr as string) As String
        Dim xsub As String
        Dim xstart As Integer = xstr.IndexOf(StartStr)
        Dim xend As Integer = xstr.IndexOf(EndStr)
        if xstart >= 0 and xend >= 0 then
            xsub  = xstr.Substring(xstart + StartStr.Length, (xend - xstart) + StartStr.Length)
            'xstr  = xstr.Replace(xsub, String.Empty)
        end if
                    'FindTwo = dbRankID.IndexOf("rank=")
                    'If FindTwo >= 0 Then
                    '    FindThree = dbRankID.IndexOf("'", FindTwo)
                    '    dbRankID = dbRankID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
        Return xstr
    End Function
    
 

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

            End Using
            con.Close()
        End Using
    End Sub

End Module