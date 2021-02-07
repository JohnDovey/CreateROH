Imports System.Data.SQLite
Imports System.Net.Http
Imports System.Net
Imports System.IO
Imports System.Text
Imports System.Web
Imports System.Threading

Module GrabWebImages

    Dim DBName As String
    'Dim dbImgName As String = ""
    'Dim dbImgPath As String = ""
    Dim dbImgUrl As String = ""
    Dim dbImgUrlComplete As String = ""
    Dim dbPersonNumber As String = ""

    Dim myFileName As String = ""
    Dim myPath As String = ""
    Dim dbRowid As Integer = 0
    Sub Main()

        DBName = Path.Combine(".", "ROHData.Sql3")
        Dim cs As String = "URI=file:" & DBName
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = "SELECT * from PersonImages ;"
                Dim rdr As SQLiteDataReader = cmd.ExecuteReader()
                Using rdr
                    While (rdr.Read())
                        'dbImgName = rdr("ImgName")
                        'dbImgPath = rdr("ImgPath")
                        dbRowid = rdr("id")
                        dbImgUrl = rdr("ImgUrl")
                        dbImgUrlComplete = rdr("ImgUrlComplete")
                        dbPersonNumber = rdr("PersonNumber")
                        myFileName = GetFileName(dbImgUrl)
                        myPath = GetDirName(dbImgUrl)
                        ' Console.WriteLine("Path: " & myPath)
                        Console.WriteLine("PersonNumber: {0}, Path: {1}", dbPersonNumber, myPath)
                        Using cmd2 As New SQLiteCommand(con)
                            cmd2.CommandText = "UPDATE PersonImages set ImgName='" & myFileName & "' where id = " & dbRowid
                            cmd2.ExecuteNonQuery()
                            cmd2.CommandText = "UPDATE PersonImages set ImgPath ='" & myPath & "' where id = " & dbRowid
                            cmd2.ExecuteNonQuery()
                        End Using
                        Try
                            Dim Client As New WebClient
                            Client.DownloadFile(dbImgUrlComplete, ".\" & dbImgUrl)
                            Client.Dispose()
                        Catch ex As Exception
                            Console.WriteLine(ex.Message, "Download Error")
                        End Try
                        ' Console.ReadKey()
                        Thread.Sleep(0)
                    End While
                End Using
            End Using
        End Using
        Console.WriteLine("Hit a key")
        Console.ReadKey()
    End Sub
    Private Function GetFileName(ByVal path As String) As String
        Dim myTmpfilename As String

        Dim sep() As Char = {"/", "\", "//"}
        myTmpfilename = path.Split(sep).Last()

        Return myTmpfilename
    End Function
    Private Function GetDirName(ByVal path As String) As String
        Dim directoryName As String
        directoryName = IO.Path.GetDirectoryName(path)
        Dim fullPath As String
        fullPath = IO.Path.Combine(directoryName)
        fullPath = IO.Path.Combine(".\", directoryName)
        Dim di As DirectoryInfo = Directory.CreateDirectory(".\" & fullPath)
        Return ("." & fullPath)
    End Function
End Module
