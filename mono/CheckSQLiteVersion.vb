Option Strict On
Imports System.Data.Sqlite

Module Example
    Sub Main()
	            Dim con As SqliteConnection
		            Dim cmd As SqliteCommand
			            
			            Try            
				                Dim cs As String = "Data Source=:memory:"
						            con = New SqliteConnection(cs)
							                con.Open()
									        
									            Dim stm As String = "SELECT SQLITE_VERSION()"
										                cmd = New SqliteCommand(stm, con)
												            
												            Dim version As String = Convert.ToString(cmd.ExecuteScalar())

													                Console.WriteLine("SQLite version : {0}", version)

															        Catch ex As SqliteException

																            Console.WriteLine("Error: " & ex.ToString())

																	            Finally

																		                If cmd IsNot Nothing
																					                cmd.Dispose()
																							            End If

																								                If con IsNot Nothing

																											                Try
																													                    con.Close()
																															                    Catch ex As SqliteException
																																	                        Console.WriteLine("Failed closing connection")
																																				                    Console.WriteLine("Error: " & ex.ToString())
																																						                    Finally
																																								                        con.Close()
																																											                    con.Dispose()
																																													                    End Try

																																															                End If

																																																	        End Try

																																																		    End Sub

																																																	    End Module
