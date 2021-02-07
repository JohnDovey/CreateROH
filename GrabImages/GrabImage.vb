Imports System.Data.SQLite
Imports System.Net.Http
Imports System.Net
Imports System.IO
Imports System.Text
Module GrabWebImages

    Dim DBName As String
    Sub Main()

        DBName = Path.Combine(".", "ROHData.Sql3")
        Dim cs As String = "URI=file:" & DBName
        Using con As New SQLiteConnection(cs)
            con.Open()
            Using cmd As New SQLiteCommand(con)
                cmd.CommandText = ""
                cmd.ExecuteNonQuery()
            End Using

        End Using
    End Sub

End Module
