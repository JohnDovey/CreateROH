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
    Public Const sqlCreatePersonImg As String = "CREATE TABLE IF NOT EXISTS 'PersonImages' ('id' INTEGER Not NULL UNIQUE, 'PersonNumber' INTEGER Not NULL DEFAULT 0, 'ImgUrl' TEXT, 'ImgUrlComplete' TEXT, 'ImgPath' TEXT, 'ImgThumbPath' TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
    Public Const sqlCreatePersonInfoRaw As String = "CREATE TABLE  IF NOT EXISTS 'PersonInfoRaw' ( 'id' INTEGER NOT NULL UNIQUE, 'PersonNumber'	INTEGER NOT NULL UNIQUE, 'Name'	TEXT NOT NULL DEFAULT 'Unknown', 'FirstName'	TEXT, 'LastName'	TEXT, 'Rank'	TEXT, 'RankID'	INTEGER, 'Regiment'	TEXT, 'RegimentID'	INTEGER, 'Unit'	TEXT, 'UnitID'	INTEGER, 'DateDeath'	TEXT DEFAULT 'Unknown', 'CauseDeath'	TEXT DEFAULT 'Unknown', 'AddInfo'	TEXT, 'Country'	TEXT, 'CountryID'	INTEGER, 'Cemetery'	INTEGER, 'CemeteryID'	INTEGER, 'CemeteryLat'	TEXT, 'CemeteryLong'	TEXT, 'GraveRef' TEXT, 'DateChecked'	TEXT, 'Initials' TEXT, 'ServiceNo' TEXT, 'Age' TEXT, 'Locality' TEXT, 'LocalityID' INTEGER, 'Citation' TEXT, PRIMARY KEY('id')) ;"
    Public Const sqlCreateRawweb As String = "CREATE TABLE IF NOT EXISTS 'rawweb' ('id' INTEGER NOT NULL, 'StartTime'	TEXT COLLATE RTRIM,  'EndTime'	TEXT COLLATE RTRIM, 'PageSize'	NUMERIC DEFAULT 0, 'PersonNumber'	NUMERIC DEFAULT 0, 'WebAddress'	TEXT, 'WebPage'	TEXT, PRIMARY KEY('id' AUTOINCREMENT));"
    Public Const sqlCreateLastSeq As String = "CREATE TABLE IF NOT EXISTS 'LastSeq' ('id'	INTEGER NOT NULL DEFAULT 0, 'LastNumber'	NUMERIC NOT NULL DEFAULT 0, PRIMARY KEY('id'));"
    Public Const sqlCreateRegiment As String = "CREATE TABLE IF NOT EXISTS 'Regiment'  ( 'RegimentID'	INTEGER NOT NULL UNIQUE, 'RegimentName'	TEXT, PRIMARY KEY('RegimentID'));"
    Public Const sqlCreateRank As String = "CREATE TABLE IF NOT EXISTS 'Rank'  ( 'RankID'	INTEGER NOT NULL UNIQUE, 'RankName'	TEXT, 'RankDescription'	TEXT, PRIMARY KEY('RankID'));"
    Public Const sqlCreateUnit As String = "CREATE TABLE IF NOT EXISTS 'Unit'  ( 'UnitID'	INTEGER NOT NULL UNIQUE, 'UnitName'	TEXT, PRIMARY KEY('UnitID'));"
    Public Const sqlCreateCountry As String = "CREATE TABLE IF NOT EXISTS 'Country'  ( 'CountryID'	INTEGER NOT NULL UNIQUE, 'CountryName'	TEXT, PRIMARY KEY('CountryID'));"
    Public Const sqlCreateCemetery As String = "CREATE TABLE IF NOT EXISTS 'Cemetery' ( 'CemeteryID'	INTEGER NOT NULL UNIQUE, 'CemeteryName'	TEXT, 'Lat'	TEXT, 'Long'	TEXT, PRIMARY KEY('CemeteryID'));"



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
                RecordCounter.Text = cmd.ExecuteScalar()
                CntStartRecord.Text = RecordCounter.Text

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
        ProgressBar.Value = 0
        ProgressBar.Minimum = Val(CntStartRecord.Text)
        ProgressBar.Maximum = Val(CntEndRecord.Text)

        For MyCount = Val(CntStartRecord.Text) To Val(CntEndRecord.Text)
            TxtLog.AppendText("Downloading page: " & TxtUrl.Text & MyCount & vbCrLf)
            DownloadPage(MyCount)
            ProgressBar.Value = MyCount
            If TxtLog.TextLength > 10000 Then TxtLog.ResetText()
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
        RecordCounter.Text = MyPersNum.ToString

        WebAddress = TxtUrl.Text & MyPersNum
        StartTime = DateTime.Now
        TxtLog.AppendText("Start: " & StartTime & vbCrLf)
        LblDateTime.Text = StartTime
        ' Download the page
        Using Client As New WebClient
            Client.Headers("User-Agent") = "Googlebot/2.38"
            Response = Client.DownloadString(WebAddress)
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
                cmd.CommandText = $"INSERT INTO rawweb VALUES (null, '{myStartTime}', '{MyEndTime}', {myPageSize},{myPersonNumber}, '{NewMyWebAddress}', '{NewMyWebPage}');"
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
        TxtLog.ResetText()
        Dim web As New HtmlWeb()
        Dim myURI As String
        Dim cnt As Integer = 0
        Dim highCnt As Integer = 0
        Dim tmpText As String
        Dim FileCount As Integer = 0
        Dim MyPersonNumber As String
        ProgressBar.Value = 0
        ProgressBar.Minimum = Val(CntStartRecord.Text)
        ProgressBar.Maximum = Val(CntEndRecord.Text)
        For FileCount = Val(CntStartRecord.Text) To Val(CntEndRecord.Text)
            TxtLog.AppendText("Start Person : " & FileCount.ToString & vbCrLf)
            MyPersonNumber = FileCount
            myURI = Path.Combine(MySubDir, FileCount & ".html")
            myURI = Application.StartupPath & myURI
            Dim doc As HtmlDocument = web.Load(myURI)

            ' Load array with all <td> elements
            Dim FindInfo(50) As String

            Dim htmlNodes = doc.DocumentNode.SelectNodes("//tr")
            cnt = 0
            For Each childnode As HtmlNode In htmlNodes.Descendants.Where(Function(n) n.Name = "td")
                FindInfo(cnt) = childnode.InnerHtml
                FindInfo(cnt) = Replace(FindInfo(cnt), Chr(9), Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), vbTab, Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), vbLf, Space(1))
                FindInfo(cnt) = Replace(FindInfo(cnt), vbCr, Space(1))
                FindInfo(cnt) = Trim(FindInfo(cnt))
                tmpText = vbCrLf & "TD Element:" & cnt & "- >  " & FindInfo(cnt)

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
            Dim dbLocalityID As String = "0"
            ' Other
            Dim dbCitation As String = ""

            For cnt = 0 To (highCnt - 1)
                ' TD Element:0- >  <div class="tableLabel">Name:</div>
                ' TD Element:  1- >  ABBEY
                FindOne = FindInfo(cnt).IndexOf(">Name:")
                If FindOne >= 0 Then
                    dbNamefld = FindInfo(cnt + 1) ' No change
                    dbNamefld = dbNamefld.Replace("'", WebUtility.HtmlEncode("'"))
                End If

                ' TD Element: 2- >  <div class="tableLabel">Given Name:</div>
                ' TD Element: 3- >  DAVID ROBERT
                FindOne = FindInfo(cnt).IndexOf(">Given Name:")
                If FindOne >= 0 Then
                    dbFirstName = FindInfo(cnt + 1) ' No change
                    dbFirstName = dbFirstName.Replace("'", WebUtility.HtmlEncode("'"))
                End If

                ' TD Element: 4- >  <div class="tableLabel">Initials:</div>
                ' TD Element: 5- >  D R
                FindOne = FindInfo(cnt).IndexOf(">Initials:")
                If FindOne >= 0 Then
                    dbInitials = FindInfo(cnt + 1) ' No change
                    dbInitials = dbInitials.Replace("'", WebUtility.HtmlEncode("'"))
                End If
                ' TD Element:6- >  <div class="tableLabel">Service No:</div>
                ' TD Element: 7- >  7357
                FindOne = FindInfo(cnt).IndexOf(">Service No:")
                If FindOne >= 0 Then
                    dbServiceNo = FindInfo(cnt + 1) ' No change
                    dbServiceNo = dbServiceNo.Replace("'", WebUtility.HtmlEncode("'"))
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
                    End If
                    FindTwo = dbRankID.IndexOf("rank=")
                    If FindTwo >= 0 Then
                        FindThree = dbRankID.IndexOf("'", FindTwo)
                        dbRankID = dbRankID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
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
                    End If
                    FindTwo = dbRegimentID.IndexOf("regiment=")
                    If FindTwo >= 0 Then
                        FindThree = dbRegimentID.IndexOf("'", FindTwo)
                        dbRegimentID = dbRegimentID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
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
                    End If
                    FindTwo = dbUnitID.IndexOf("unit=")
                    If FindTwo >= 0 Then
                        FindThree = dbUnitID.IndexOf("'", FindTwo)
                        dbUnitID = dbUnitID.Substring(FindTwo + 5, FindThree - (FindTwo + 5))
                    End If
                    dbUnit = dbUnit.Replace("'", WebUtility.HtmlEncode("'"))
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
                    End If
                    dbDateOfDeath = dbDateOfDeath.Replace("'", WebUtility.HtmlEncode("'"))
                End If
                '<td style="width:20%;" valign='top'><div class="tableLabel">Citations:</div></td>
                ' <td valign ='top'>***Not yet accepted for War Grave Status by CWGC, need service file</td>
                FindOne = FindInfo(cnt).IndexOf(">Citations:")
                If FindOne >= 0 Then
                    dbCitation = FindInfo(cnt + 1) ' No change
                    dbCitation = dbCitation.Replace("'", WebUtility.HtmlEncode("'"))
                End If

                ' TD Element: 16- >  <div class="tableLabel">Age:</div>
                ' TD Element: 17- >  37
                FindOne = FindInfo(cnt).IndexOf(">Age:")
                If FindOne >= 0 Then
                    dbAge = FindInfo(cnt + 1) ' No change

                End If
                'TD Element:18- >  <div class="tableLabel">Cause of Death:</div>
                'TD Element: 19- >  Died of phthisis, at No. 1 General Hospital Wynberg
                FindOne = FindInfo(cnt).IndexOf(">Cause of Death:")
                If FindOne >= 0 Then
                    dbCauseOfDeath = FindInfo(cnt + 1) ' No change
                    dbCauseOfDeath = dbCauseOfDeath.Replace("'", WebUtility.HtmlEncode("'"))

                End If
                ' TD Element:20- >  <div class="tableLabel">Additional<br>Information:</div>
                ' TD Element: 21- >  <div>Son of Mrs. Elizabeth Ann And the late Thomas Abbey, of 146, Cathcart Rd., Queenstown, Cape Province. His brother also died in service</div>
                FindOne = FindInfo(cnt).IndexOf(">Additional<")
                If FindOne >= 0 Then
                    dbAddInfo = FindInfo(cnt + 1) ' No change
                    dbAddInfo = dbAddInfo.Replace("<div>", "")
                    dbAddInfo = dbAddInfo.Replace("</div>", "")
                    dbAddInfo = dbAddInfo.Replace("'", WebUtility.HtmlEncode("'"))
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
                    End If
                    FindTwo = dbCountryID.IndexOf("country=")
                    If FindTwo >= 0 Then
                        FindThree = dbCountryID.IndexOf("'", FindTwo)
                        dbCountryID = dbCountryID.Substring(FindTwo + 8, FindThree - (FindTwo + 8))
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
                    End If
                    FindTwo = dbLocalityID.IndexOf("locality=")
                    If FindTwo >= 0 Then
                        FindThree = dbLocalityID.IndexOf("'", FindTwo)
                        dbLocalityID = dbLocalityID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
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
                    End If
                    FindTwo = dbCemeteryID.IndexOf("cemetery=")
                    If FindTwo >= 0 Then
                        FindThree = dbCemeteryID.IndexOf("'", FindTwo)
                        dbCemeteryID = dbCemeteryID.Substring(FindTwo + 9, FindThree - (FindTwo + 9))
                    End If
                    dbCemetery = dbCemetery.Replace("'", WebUtility.HtmlEncode("'"))
                End If
                ' TD Element:29- >  <div class="tableLabel">Grave Reference:</div>
                ' TD Element: 30- >  Bl. UR. 15.
                FindOne = FindInfo(cnt).IndexOf(">Grave Reference:")
                If FindOne >= 0 Then
                    dbGraveReference = FindInfo(cnt + 1) ' No change
                End If

            Next cnt
            'Console.WriteLine("End Find Data")

            If Val(dbLocalityID) < 0 Then dbLocalityID = "0"
            If Val(dbRankID) < 0 Then dbRankID = "0"
            If Val(dbRegimentID) < 0 Then dbRegimentID = "0"
            If dbUnitID.Length < 1 Then dbUnitID = "0"
            If dbUnit.Length < 1 Then dbUnit = "Unknown"
            If Val(dbUnitID) < 0 Then dbUnitID = "0"
            If Val(dbCountryID) < 0 Then dbCountryID = "0"
            If Val(dbCemeteryID) < 0 Then dbCemeteryID = "0"

            Dim tmpSql As String
            tmpSql = $"insert into 'PersonInfoRaw' ('id','PersonNumber','FirstName','LastName','Rank','RankID','Regiment','RegimentID','Unit','UnitID','DateDeath','CauseDeath','AddInfo','Country','CountryID','Cemetery','CemeteryID','GraveRef','Initials','ServiceNo','Age','Locality','LocalityID', 'Citation') VALUES (null,'{MyPersonNumber}','{dbFirstName}','{dbNamefld}','{dbRank}',{dbRankID},'{dbRegiment}',{dbRegimentID},'{dbUnit}',{dbUnitID},'{dbDateOfDeath}','{dbCauseOfDeath}','{dbAddInfo}','{dbCountry}',{dbCountryID},'{dbCemetery}',{dbCemeteryID},'{dbGraveReference}', '{dbInitials}','{dbServiceNo}','{dbAge}','{dbLocality}',{dbLocalityID}, '{dbCitation}');"
            Console.WriteLine(tmpSql)
            Write_Data_Record(tmpSql)

            ' Load Array with IMG tags
            ' Load array with all <td> elements
            Dim FindIMG(50) As String
            Dim ImgURL As String = "http://www.southafricawargraves.org"
            Dim tmpUrl As String = ""
            Dim tmpUrlComplete As String = ""
            Console.WriteLine("Find IMG")
            ' .SelectNodes("./img")
            'Dim htmlNodesIMG = doc.DocumentNode.SelectNodes("//*[@id='tabs']") (//*[@id='PhotoFilename1']
            Dim htmlNodesIMG = doc.DocumentNode.SelectNodes("//img")
            cnt = 0

            For Each img As HtmlAgilityPack.HtmlNode In doc.DocumentNode.SelectNodes("//img")
                'For Each childnode As HtmlNode In htmlNodesIMG.Descendants
                ' .Attributes("src").Value()
                'Console.WriteLine(img.Attributes("src").Value)
                FindOne = img.Attributes("src").Value.IndexOf("CAPTCHA")
                If FindOne <= 0 Then
                    FindIMG(cnt) = img.Attributes("src").Value
                    FindIMG(cnt) = Replace(FindIMG(cnt), Chr(9), Space(1))
                    FindIMG(cnt) = Replace(FindIMG(cnt), vbTab, Space(1))
                    FindIMG(cnt) = Replace(FindIMG(cnt), vbLf, Space(1))
                    FindIMG(cnt) = Replace(FindIMG(cnt), vbCr, Space(1))
                    FindIMG(cnt) = Replace(FindIMG(cnt), "'", WebUtility.UrlEncode("'"))
                    FindIMG(cnt) = Trim(FindIMG(cnt))
                    'tmpUrl = WebUtility.UrlEncode(FindIMG(cnt))
                    tmpUrl = FindIMG(cnt)
                    'tmpUrlComplete = WebUtility.UrlEncode(ImgURL & FindIMG(cnt))
                    tmpUrlComplete = ImgURL & tmpUrl
                    tmpSql = $"INSERT INTO 'PersonImages' ('id', 'PersonNumber', 'ImgUrl', 'ImgUrlComplete') VALUES (null, '{MyPersonNumber}', '{tmpUrl}', '{WebUtility.HtmlEncode(tmpUrlComplete)}') ;"

                    Console.WriteLine(tmpSql)
                    Write_Data_Record(tmpSql)
                End If
                cnt += 1
                highCnt = cnt
            Next
            Console.WriteLine("End Find IMG")

            'The Search for Latitude and Longitude
            'var lat = '30.83823';
            'var Long = '28.94696';
            Dim dbLat As String = ""
            Dim dbLong As String = ""
            Dim tmpFile As String = ""
            tmpFile = File.ReadAllText(myURI)
            FindOne = tmpFile.IndexOf("var lat = '")
            If FindOne >= 0 Then
                FindTwo = tmpFile.IndexOf("'", FindOne + 11)
                ' dbLat = tmpFile.Substring(FindOne + 11, FindTwo)
                dbLat = tmpFile.Substring(FindOne + 11, FindTwo - (FindOne + 11))
                Console.WriteLine("Latitude: " & dbLat & vbCrLf)
            End If

            FindOne = tmpFile.IndexOf("var Long = '")
            If FindOne >= 0 Then
                FindTwo = tmpFile.IndexOf("'", FindOne + 12)
                'dbLong = tmpFile.Substring(FindOne + 12, FindTwo)
                dbLong = tmpFile.Substring(FindOne + 12, FindTwo - (FindOne + 11))
                Console.WriteLine("Longitude: " & dbLong & vbCrLf)
            End If
            If (dbLat.Length > 0) And (dbLong.Length) > 0 Then
                tmpSql = 
            End If
            ' End Lat/Long
            ' End Load

            RecordCounter.Text = FileCount.ToString
            TxtLog.AppendText("End Person : " & FileCount.ToString & vbCrLf)
            Application.DoEvents()
            ProgressBar.Value = FileCount
            If TxtLog.TextLength > 10000 Then TxtLog.ResetText()
        Next FileCount

    End Sub
    Public Sub Write_Data_Record(mySQL)
        Dim cs As String = "URI=file:" & DBName
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = mySQL
                cmd.ExecuteNonQuery()
            End Using
            con.Close()
        End Using
    End Sub


    Private Sub CntStartRecord_TextChanged(sender As Object, e As EventArgs) Handles CntStartRecord.TextChanged
        CntEndRecord.Text = Val(CntStartRecord.Text) + 10
    End Sub


End Class
