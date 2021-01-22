Imports System.Data.SQLite
Imports System.Net.Http
Imports System.Net
Imports System.IO
Imports HtmlAgilityPack
Imports System.Text

Public Class FrmMain

    Public StoreLog As String
    Dim MySubDir As String
    Dim MyDir As String
    Dim DBName As String
    Private Sub FrmMain_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Me.Cursor = Cursors.WaitCursor
        LblDateTime.Text = DateAndTime.Now
        MyDir = Application.StartupPath
        DBName = Path.Combine(MyDir, "ROHData.Sql3")
        LblFileName.Text = DBName
        CheckDatabase(DBName)
        MySubDir = Path.Combine(MyDir, "/pages/")
        Directory.CreateDirectory(MySubDir)
        Cursor = DefaultCursor

    End Sub

    Sub CheckDatabase(MyDbName)
        Dim cs As String = "URI=file:" & MyDbName
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'rawweb' ('id' INTEGER NOT NULL, 'StartTime'	TEXT COLLATE RTRIM,  'EndTime'	TEXT COLLATE RTRIM, 'PageSize'	NUMERIC DEFAULT 0, 'PersonNumber'	NUMERIC DEFAULT 0, 'WebAddress'	TEXT, 'WebPage'	TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
                cmd.ExecuteNonQuery()
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'LastSeq' ('id'	INTEGER NOT NULL DEFAULT 0, 'LastNumber'	NUMERIC NOT NULL DEFAULT 0, PRIMARY KEY('id'));"
                cmd.ExecuteNonQuery()
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'Regiment'  ( 'RegimentID'	INTEGER NOT NULL UNIQUE, 'RegimentName'	TEXT, PRIMARY KEY('RegimentID'));"
                cmd.ExecuteNonQuery()
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'Rank'  ( 'RankID'	INTEGER NOT NULL UNIQUE, 'RankName'	TEXT, 'RankDescription'	TEXT, PRIMARY KEY('RankID'));"
                cmd.ExecuteNonQuery()
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'Unit'  ( 'UnitID'	INTEGER NOT NULL UNIQUE, 'UnitName'	TEXT, PRIMARY KEY('UnitID'));"
                cmd.ExecuteNonQuery()
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'Country'  ( 'CountryID'	INTEGER NOT NULL UNIQUE, 'CountryName'	TEXT, PRIMARY KEY('CountryID'));"
                cmd.ExecuteNonQuery()
                cmd.CommandText = "CREATE TABLE IF NOT EXISTS 'Cemetery' ( 'CemeteryID'	INTEGER NOT NULL UNIQUE, 'CemeteryName'	TEXT, 'Lat'	TEXT, 'Long'	TEXT, PRIMARY KEY('CemeteryID'));"
                cmd.ExecuteNonQuery()

                cmd.CommandText = "CREATE TABLE  IF NOT EXISTS 'PersonInfoRaw' ( 'id'	INTEGER NOT NULL UNIQUE, 'PersonNumber'	INTEGER NOT NULL UNIQUE, 'Name'	TEXT NOT NULL DEFAULT 'Unknown', 'FirstName'	TEXT, 'LastName'	TEXT, 'Rank'	TEXT, 'RankID'	INTEGER, 'Regiment'	TEXT, 'RegimentID'	INTEGER, 'Unit'	TEXT, 'UnitID'	INTEGER, 'DateDeath'	TEXT DEFAULT 'Unknown', 'CauseDeath'	TEXT DEFAULT 'Unknown', 'AddInfo'	TEXT, 'Country'	TEXT, 'CountryID'	INTEGER, 'Cemetery'	INTEGER, 'CemeteryID'	INTEGER, 'CemeteryLat'	TEXT, 'CemeteryLong'	TEXT, 'GraveRef' TEXT, 'DateChecked'	TEXT, 'Initials' TEXT, 'ServiceNo' TEXT, 'Age' TEXT, 'Locality' TEXT, 'LocalityID' INTEGER,   PRIMARY KEY('id')) ;"
                cmd.ExecuteNonQuery()

                'CONSTRAINT 'Regiment' FOREIGN KEY('RegimentID') REFERENCES Regiment), 
                'CONSTRAINT 'Unit' FOREIGN KEY('UnitID') REFERENCES Unit), 
                'CONSTRAINT 'Country' FOREIGN KEY('CountryID') REFERENCES Country), 
                'Constraint 'Rank' FOREIGN KEY('RankID') REFERENCES Rank)

                cmd.ExecuteNonQuery()
                'cmd.CommandText = "INSERT INTO LastSeq VALUES (0,100);"
                'cmd.ExecuteNonQuery()

                cmd.CommandText = "SELECT LastNumber from LastSeq where ID = 0;"
                RecordCounter.Value = cmd.ExecuteScalar()
                CntStartRecord.Text = RecordCounter.Value.ToString

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


    Private Sub BtnFetchData_Click(sender As Object, e As EventArgs) Handles BtnFetchData.Click
        ' Print a message as the page downloads.
        TxtLog.Text = ""
        For MyCount = Val(CntStartRecord.Text) To Val(CntEndRecord.Text)
            TxtLog.AppendText("Downloading page: " & TxtUrl.Text & MyCount & vbCrLf)
            DownloadPage(MyCount)
            Application.DoEvents()
        Next


    End Sub

    Public StartTime As String
    Public EndTime As String
    Public PageSize As Integer
    Public WebAddress As String
    Public WebPage As String
    Public Response As String

    Sub DownloadPage(MyPersNum)
        Me.Cursor = Cursors.WaitCursor
        RecordCounter.Value = MyPersNum

        WebAddress = TxtUrl.Text & MyPersNum
        StartTime = DateTime.Now
        TxtLog.AppendText("Start: " & StartTime & vbCrLf)
        LblDateTime.Text = StartTime
        ' Download the page
        Using Client As New WebClient
            Client.Headers("User-Agent") = "Googlebot/2.38"
            Response = Client.DownloadString(WebAddress)
            'Dim arr() As Byte = client.DownloadData("http://www.dotnetperls.com/")
            WebPage = Response
        End Using
        ' Finished downloading the page
        EndTime = DateTime.Now
        TxtLog.AppendText("End: " & EndTime & vbCrLf)
        PageSize = WebPage.Length
        TxtLog.AppendText(" Size: " & PageSize & vbCrLf)

        LblDateTime.Text = EndTime
        WriteData(MyPersNum, StartTime, EndTime, PageSize, WebAddress, WebPage)
        Cursor = Cursors.Default
    End Sub
    Dim NewMyWebAddress As String
    Dim NewMyWebPage As String



    Sub WriteData(myPersonNumber, myStartTime, MyEndTime, myPageSize, myWebAddress, myWebPage)

        NewMyWebAddress = WebUtility.HtmlEncode(myWebAddress)
        NewMyWebPage = myPersonNumber & ".html"
        NewMyWebPage = Path.Combine(MySubDir, NewMyWebPage)

        ' File.WriteAllLines(NewMyWebPage, myWebPage)
        File.WriteAllText(NewMyWebPage, myWebPage)

        Dim cs As String = "URI=file:" & LblFileName.Text
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = "INSERT INTO rawweb VALUES (null, '" & myStartTime & "', '" & MyEndTime & "', " & myPageSize & ", " & myPersonNumber & ", '" & NewMyWebAddress & "', '" & NewMyWebPage & "');"
                Debug.Print("NewMyWebPage: " & NewMyWebPage)
                Debug.Print("SQL: " & cmd.CommandText)

                cmd.ExecuteNonQuery()

                cmd.CommandText = "UPDATE LastSeq SET LastNumber =" & myPersonNumber & " where id = 0;"
                cmd.ExecuteNonQuery()

            End Using
            con.Close()
        End Using
    End Sub


    Private Sub BtnExtractData_Click(sender As Object, e As EventArgs) Handles BtnExtractData.Click
        TxtLog.Text = ""
        Dim web As New HtmlWeb()
        Dim URI As String
        URI = Path.Combine(MySubDir, "2.html")
        URI = Application.StartupPath & URI
        Dim tmpText As String


        Dim doc As HtmlDocument = web.Load(URI)

        Dim cnt As Integer = 0
        Dim highCnt As Integer = 0

        ' Load array with all <td> elements
        Dim FindCountry(50) As String
        Console.Write("Find Country")
        Dim htmlNodes = doc.DocumentNode.SelectNodes("//tr")
        cnt = 0
        For Each childnode As HtmlNode In htmlNodes.Descendants.Where(Function(n) n.Name = "td")
            FindCountry(cnt) = childnode.InnerHtml
            FindCountry(cnt) = Replace(FindCountry(cnt), Chr(9), Space(1))
            FindCountry(cnt) = Replace(FindCountry(cnt), vbTab, Space(1))
            FindCountry(cnt) = Trim(FindCountry(cnt))
            tmpText = vbCrLf & "TD Element:" & cnt & "- >  " & FindCountry(cnt)
            TxtLog.AppendText(tmpText)
            Console.WriteLine(tmpText)
            cnt += 1
            highCnt = cnt
        Next

        Dim FindOne As Integer = -1
        Dim FindTwo As Integer = -1
        Dim FindThree As Integer = -1

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
        Dim dbLocalityID As String = ""

        For cnt = 0 To (highCnt - 1)
            Console.WriteLine("Cnt: " & cnt)
            ' TD Element:0- >  <div class="tableLabel">Name:</div>
            ' TD Element:  1- >  ABBEY
            FindOne = FindCountry(cnt).IndexOf(">Name:")
            If FindOne >= 0 Then
                dbNamefld = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Name: [{0}]", dbNamefld)
            End If

            ' TD Element: 2- >  <div class="tableLabel">Given Name:</div>
            ' TD Element: 3- >  DAVID ROBERT
            FindOne = FindCountry(cnt).IndexOf(">Given Name:")
            If FindOne >= 0 Then
                dbFirstName = FindCountry(cnt + 1) ' No change
                Console.WriteLine("First Name: [{0}]", dbFirstName)
            End If
            ' TD Element: 4- >  <div class="tableLabel">Initials:</div>
            ' TD Element: 5- >  D R
            FindOne = FindCountry(cnt).IndexOf(">Initials:")
            If FindOne >= 0 Then
                dbInitials = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Initials: [{0}]", dbInitials)
            End If
            ' TD Element:6- >  <div class="tableLabel">Service No:</div>
            ' TD Element: 7- >  7357
            FindOne = FindCountry(cnt).IndexOf(">Service No:")
            If FindOne >= 0 Then
                dbServiceNo = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Service No: [{0}]", dbServiceNo)
            End If
            ' TD Element: 8- >  <div class="tableLabel">Rank:</div>
            ' TD Element: 9- >  Private<div style='float:right;'>Other Casualties of this <a href='view-paginated.php?page=1&rank=429' target='new'>Rank</a></div>
            'Rank=
            ' dbRank = FindCountry(5) 'left up to <div
            ' dbRankNo = FindCountry(5) ' rank=???' (5)
            FindOne = FindCountry(cnt).IndexOf("Rank:")
            If FindOne >= 0 Then
                dbRank = FindCountry(cnt + 1)
                dbRankID = FindCountry(cnt + 1)

                FindTwo = dbRank.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbRank = dbRank.Substring(0, FindTwo)
                    Console.WriteLine("Rank: [{0}]", dbRank)
                End If
                FindTwo = dbRankID.IndexOf("rank=")
                If FindTwo >= 0 Then
                    FindThree = dbRankID.IndexOf("'", FindTwo)
                    dbRankID = dbRankID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
                    Console.WriteLine("RankID: [{0}]", dbRankID)
                End If

            End If
            ' TD Element: 10- >  <div class="tableLabel">Regiment:</div>
            ' TD Element: 11- >  South African Infantry<div style='float:right;'>Other Casualties from this <a href='view-paginated.php?page=1&regiment=807' target='new'>Regiment</a></div>
            FindOne = FindCountry(cnt).IndexOf(">Regiment:")
            If FindOne >= 0 Then
                dbRegiment = FindCountry(cnt + 1)
                dbRegimentID = FindCountry(cnt + 1)

                FindTwo = dbRegiment.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbRegiment = dbRegiment.Substring(0, FindTwo)
                    Console.WriteLine("Regiment: [{0}]", dbRegiment)
                End If
                FindTwo = dbRegimentID.IndexOf("regiment=")
                If FindTwo >= 0 Then
                    FindThree = dbRegimentID.IndexOf("'", FindTwo)
                    dbRegimentID = dbRegimentID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                    Console.WriteLine("RegimentID: [{0}]", dbRegimentID)
                End If
            End If
            'Unit
            ' TD Element: 12- >  <div class="tableLabel">Unit:</div>
            ' TD Element: 13- >  2nd Regt.<div style='float:right;'>Other Casualties from this <a href='view-paginated.php?page=1&unit=925' target='new'>Unit</a></div>
            FindOne = FindCountry(cnt).IndexOf(">Unit:")
            If FindOne >= 0 Then
                dbUnit = FindCountry(cnt + 1)
                dbUnitID = FindCountry(cnt + 1)

                FindTwo = dbUnit.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbUnit = dbUnit.Substring(0, FindTwo)
                    Console.WriteLine("Unit: [{0}]", dbUnit)
                End If
                FindTwo = dbUnitID.IndexOf("unit=")
                If FindTwo >= 0 Then
                    FindThree = dbUnitID.IndexOf("'", FindTwo)
                    dbUnitID = dbUnitID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
                    Console.WriteLine("UnitID: [{0}]", dbUnitID)
                End If

            End If
            'Date of Death

            ' TD Element: 14- >  <div class="tableLabel">Date of Death:</div>
            ' TD Element: 15- >  1916-12-20<div style='float:right;'>Other Casualties on this            <a href ='view-paginated.php?page=1&DoD_YYYY=1916&DoD_MM=12&DoD_DD=20' target='new'>Date</a></div>
            FindOne = FindCountry(cnt).IndexOf(">Date of Death:")
            If FindOne >= 0 Then
                dbDateOfDeath = FindCountry(cnt + 1) ' Left  till <div
                FindTwo = dbDateOfDeath.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbDateOfDeath = dbDateOfDeath.Substring(0, FindTwo)
                    Console.WriteLine("dbDateOfDeath: [{0}]", dbDateOfDeath)
                End If

            End If

            ' TD Element: 16- >  <div class="tableLabel">Age:</div>
            ' TD Element: 17- >  37
            FindOne = FindCountry(cnt).IndexOf(">Age:")
            If FindOne >= 0 Then
                dbAge = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Age: [{0}]", dbAge)
            End If
            'TD Element:18- >  <div class="tableLabel">Cause of Death:</div>
            'TD Element: 19- >  Died of phthisis, at No. 1 General Hospital Wynberg
            FindOne = FindCountry(cnt).IndexOf(">Cause of Death:")
            If FindOne >= 0 Then
                dbCauseOfDeath = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Cause of Death: [{0}]", dbCauseOfDeath)
            End If
            ' TD Element:20- >  <div class="tableLabel">Additional<br>Information:</div>
            ' TD Element: 21- >  <div>Son of Mrs. Elizabeth Ann And the late Thomas Abbey, of 146, Cathcart Rd., Queenstown, Cape Province. His brother also died in service</div>
            FindOne = FindCountry(cnt).IndexOf(">Additional<")
            If FindOne >= 0 Then
                dbAddInfo = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Add Info: [{0}]", dbAddInfo)
            End If
            'Country
            ' TD Element: 22- >  <div class="tableLabel">Country:</div>
            ' TD Element: 23- >  South Africa<div style='float:right;'>Other Casualties commemorated in <a href='view-paginated.php?page=1&country=72' target='new'>South Africa</a></div>
            FindOne = FindCountry(cnt).IndexOf(">Country:")
            If FindOne >= 0 Then
                dbCountry = FindCountry(cnt + 1)
                dbCountryID = FindCountry(cnt + 1)

                FindTwo = dbCountry.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbCountry = dbCountry.Substring(0, FindTwo)
                    Console.WriteLine("Country: [{0}]", dbCountry)
                End If
                FindTwo = dbCountryID.IndexOf("country=")
                If FindTwo >= 0 Then
                    FindThree = dbCountryID.IndexOf("'", FindTwo)
                    dbCountryID = dbCountryID.Substring(FindTwo + 8, FindThree - (FindTwo + 8))
                    Console.WriteLine("CountryID: [{0}]", dbCountryID)
                End If
            End If

            ' TD Element:24- >  <div class="tableLabel">Locality:</div>
            ' TD Element: 25- >  Western Cape<div style='float:right;'>Other Casualties commemorated in <a href='view-paginated.php?page=1&locality=1' target='new'>Western Cape</a></div>
            FindOne = FindCountry(cnt).IndexOf(">Locality:")
            If FindOne >= 0 Then
                dbLocality = FindCountry(cnt + 1)
                dbLocalityID = FindCountry(cnt + 1)

                FindTwo = dbLocality.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbLocality = dbLocality.Substring(0, FindTwo)
                    Console.WriteLine("Locality: [{0}]", dbLocality)
                End If
                FindTwo = dbLocalityID.IndexOf("locality=")
                If FindTwo >= 0 Then
                    FindThree = dbLocalityID.IndexOf("'", FindTwo)
                    dbLocalityID = dbLocalityID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                    Console.WriteLine("LocalityID: [{0}]", dbLocalityID)
                End If
            End If
            ' TD Element:26- >  <div class="tableLabel">Cemetery:</div>
            ' TD Element: 27- >  CAPE TOWN(PLUMSTEAD) CEMETERY<div style='float:right;margin-top:10px;"'>Other Casualties commemorated in this <a href='view-paginated.php?page=1&cemetery=463' target='new'>Cemetery</a></div> 
            FindOne = FindCountry(cnt).IndexOf(">Cemetery:")
            If FindOne >= 0 Then
                dbCemetery = FindCountry(cnt + 1)
                dbCemeteryID = FindCountry(cnt + 1)

                FindTwo = dbCemetery.IndexOf("<div")
                If FindTwo >= 0 Then
                    dbCemetery = dbCemetery.Substring(0, FindTwo)
                    Console.WriteLine("Cemetery: [{0}]", dbCemetery)
                End If
                FindTwo = dbCemeteryID.IndexOf("cemetery=")
                If FindTwo >= 0 Then
                    FindThree = dbCemeteryID.IndexOf("'", FindTwo)
                    dbCemeteryID = dbCemeteryID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                    Console.WriteLine("CemeteryID: [{0}]", dbCemeteryID)
                End If
            End If
            ' TD Element:29- >  <div class="tableLabel">Grave Reference:</div>
            ' TD Element: 30- >  Bl. UR. 15.
            FindOne = FindCountry(cnt).IndexOf(">Grave Reference:")
            If FindOne >= 0 Then
                dbGraveReference = FindCountry(cnt + 1) ' No change
                Console.WriteLine("Grave Ref: [{0}]", dbGraveReference)
            End If

        Next cnt





        Console.Write("End Find Country")



    End Sub



    Private Sub CntStartRecord_TextChanged(sender As Object, e As EventArgs) Handles CntStartRecord.TextChanged
        CntEndRecord.Text = Val(CntStartRecord.Text) + 10
    End Sub
End Class
