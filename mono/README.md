# Notes on Mono
---

There seem to be some gotchas compiling VB stuff on Ubuntu/Linux/Mono

The first one is kinda obvious in retrospect. 
You can choose to include either Mono.Data.Sqlite or System.Data.Sqlite with the `IMPORTS` statement.
Depending on which one you choose, just add the relevant DLL to you compile command. For example, for SYSTEM:
- use `IMPORTS System.Data.Sqlite` in your program
- compile with `vbnc CheckSQLiteVersion.vb -r:System.Data.SQLite.dll`
- Resulting file is `CheckSQLiteVersion.exe`
- Add execution permission `chmod 777 CheckSQLiteVersion.eve`
- run the program `./CheckSQLiteVersion.exe`

