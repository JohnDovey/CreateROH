Imports System.Data.SQLite
Imports System.Net.Http
Imports System.IO
Imports System.Threading.Tasks

Module GrabWebImages

    Private ReadOnly DBName As String = Path.Combine(".", "ROHData.Sql3")
    Private ReadOnly HttpClient As New HttpClient() With {
        .Timeout = TimeSpan.FromSeconds(60)
    }

    Sub Main()
        My.Application.Log.WriteEntry("=== GrabWebImages Started ===")
        Console.WriteLine("Starting image download process...")

        Dim connectionString As String = $"URI=file:{DBName};Version=3;"

        Using con As New SQLiteConnection(connectionString)
            con.Open()

            Using cmd As New SQLiteCommand("SELECT id, ImgUrl, ImgUrlComplete, PersonNumber 
                                           FROM PersonImages 
                                           WHERE ImgPath IS NULL 
                                           ORDER BY id", con)

                Using rdr As SQLiteDataReader = cmd.ExecuteReader()
                    While rdr.Read()
                        ProcessImageRow(rdr, con)
                    End While
                End Using
            End Using
        End Using

        My.Application.Log.WriteEntry("=== GrabWebImages Completed ===")
        Console.WriteLine("Process finished. Press any key to exit.")
        Console.ReadKey()
    End Sub

    Private Sub ProcessImageRow(rdr As SQLiteDataReader, con As SQLiteConnection)
        Dim id As Integer = CInt(rdr("id"))
        Dim imgUrl As String = rdr("ImgUrl")?.ToString() ?? ""
        Dim imgUrlComplete As String = rdr("ImgUrlComplete")?.ToString() ?? ""
        Dim personNumber As String = rdr("PersonNumber")?.ToString() ?? "Unknown"

        If String.IsNullOrWhiteSpace(imgUrlComplete) Then
            LogWarning($"Skipping record {id} - Empty ImgUrlComplete", personNumber)
            Return
        End If

        Dim fileName As String = GetSafeFileName(imgUrl)
        Dim relativeDir As String = GetRelativeDirectory(imgUrl)
        Dim fullSavePath As String = Path.Combine(relativeDir, fileName)

        Try
            ' Update database first
            UpdateDatabase(con, id, fileName, relativeDir)

            ' Check if file already exists
            If File.Exists(fullSavePath) Then
                Console.WriteLine($"[{personNumber}] Already exists: {fileName}")
                My.Application.Log.WriteEntry($"Skipped (already exists): {fullSavePath}")
                Return
            End If

            ' Ensure directory exists
            Directory.CreateDirectory(relativeDir)

            ' Download
            Console.WriteLine($"Downloading [{personNumber}] -> {fileName}")
            DownloadFileAsync(imgUrlComplete, fullSavePath).Wait()

            My.Application.Log.WriteEntry($"Successfully downloaded: Person={personNumber}, File={fileName}")

        Catch ex As Exception
            My.Application.Log.WriteException(ex, TraceEventType.Error,
                $"Download failed - Person={personNumber}, URL={imgUrlComplete}, Path={fullSavePath}")
            Console.WriteLine($"ERROR [{personNumber}]: {ex.Message}")
        End Try

        ' Small delay to be nice to the server (adjust as needed)
        Threading.Thread.Sleep(300)
    End Sub

    Private Sub UpdateDatabase(con As SQLiteConnection, id As Integer, fileName As String, imgPath As String)
        Using cmd As New SQLiteCommand(con)
            cmd.CommandText = "UPDATE PersonImages SET ImgName = @name, ImgPath = @path WHERE id = @id"
            cmd.Parameters.AddWithValue("@name", fileName)
            cmd.Parameters.AddWithValue("@path", imgPath)
            cmd.Parameters.AddWithValue("@id", id)
            cmd.ExecuteNonQuery()
        End Using
    End Sub

    Private Async Function DownloadFileAsync(url As String, destinationPath As String) As Task
        Using response = Await HttpClient.GetAsync(url)
            response.EnsureSuccessStatusCode()

            Using fileStream As New FileStream(destinationPath, FileMode.Create, FileAccess.Write, FileShare.None)
                Await response.Content.CopyToAsync(fileStream)
            End Using
        End Using
    End Function

    Private Function GetSafeFileName(urlPart As String) As String
        If String.IsNullOrWhiteSpace(urlPart) Then Return "unknown.jpg"

        Dim fileName As String = urlPart.Split({"/"c, "\"c}, StringSplitOptions.RemoveEmptyEntries).LastOrDefault() ?? "image.jpg"

        ' Remove invalid characters
        For Each c In Path.GetInvalidFileNameChars()
            fileName = fileName.Replace(c, "_")
        Next

        ' Ensure extension
        If Not fileName.Contains(".") Then
            fileName &= ".jpg"
        End If

        Return fileName
    End Function

    Private Function GetRelativeDirectory(urlPart As String) As String
        Dim dirPart As String = Path.GetDirectoryName(urlPart) ?? ""
        dirPart = dirPart.TrimStart({"/"c, "\"c})

        Dim relativePath As String = Path.Combine(".", dirPart)

        ' Clean up any double separators
        relativePath = relativePath.Replace("\\", "\")
        Return relativePath
    End Function

    Private Sub LogWarning(message As String, personNumber As String)
        My.Application.Log.WriteEntry(message, TraceEventType.Warning)
        Console.WriteLine($"WARNING [{personNumber}]: {message}")
    End Sub

End Module
