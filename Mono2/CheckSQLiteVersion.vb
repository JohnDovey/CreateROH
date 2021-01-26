Option Strict On
Imports System.Data.Sqlite
Module CheckSQLiteVersion

    Sub Main()
        Dim cs As String = "URI=file:test.db"
        Using con As New SqliteConnection(cs)
            con.Open()      
            Using cmd As New SqliteCommand(con)
                cmd.CommandText = "SELECT SQLITE_VERSION()"
                Dim version As String = Convert.ToString(cmd.ExecuteScalar())
                Console.WriteLine("SQLite version : {0}", version)
       
            End Using
            con.Close()
        End Using
       
    End Sub
End Module
