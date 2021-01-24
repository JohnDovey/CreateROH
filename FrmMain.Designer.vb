<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FrmMain
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()>
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Required by the Windows Form Designer
    Private components As System.ComponentModel.IContainer

    'NOTE: The following procedure is required by the Windows Form Designer
    'It can be modified using the Windows Form Designer.  
    'Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Me.TableLayoutPanel1 = New System.Windows.Forms.TableLayoutPanel()
        Me.RecordCounter = New System.Windows.Forms.Label()
        Me.LblDateTime = New System.Windows.Forms.Label()
        Me.TxtLog = New System.Windows.Forms.TextBox()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.CntEndRecord = New System.Windows.Forms.MaskedTextBox()
        Me.CntStartRecord = New System.Windows.Forms.MaskedTextBox()
        Me.BtnExtractData = New System.Windows.Forms.Button()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.BtnFetchData = New System.Windows.Forms.Button()
        Me.LblFileName = New System.Windows.Forms.Label()
        Me.TxtUrl = New System.Windows.Forms.TextBox()
        Me.ProgressBar = New System.Windows.Forms.ProgressBar()
        Me.TableLayoutPanel1.SuspendLayout()
        Me.GroupBox1.SuspendLayout()
        Me.SuspendLayout()
        '
        'TableLayoutPanel1
        '
        Me.TableLayoutPanel1.ColumnCount = 3
        Me.TableLayoutPanel1.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle())
        Me.TableLayoutPanel1.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 100.0!))
        Me.TableLayoutPanel1.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Absolute, 502.0!))
        Me.TableLayoutPanel1.Controls.Add(Me.RecordCounter, 0, 1)
        Me.TableLayoutPanel1.Controls.Add(Me.LblDateTime, 0, 0)
        Me.TableLayoutPanel1.Controls.Add(Me.TxtLog, 1, 2)
        Me.TableLayoutPanel1.Controls.Add(Me.GroupBox1, 0, 2)
        Me.TableLayoutPanel1.Controls.Add(Me.LblFileName, 1, 0)
        Me.TableLayoutPanel1.Controls.Add(Me.TxtUrl, 1, 1)
        Me.TableLayoutPanel1.Controls.Add(Me.ProgressBar, 1, 3)
        Me.TableLayoutPanel1.Dock = System.Windows.Forms.DockStyle.Left
        Me.TableLayoutPanel1.Location = New System.Drawing.Point(0, 0)
        Me.TableLayoutPanel1.Name = "TableLayoutPanel1"
        Me.TableLayoutPanel1.RowCount = 4
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 48.78049!))
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 51.21951!))
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Absolute, 352.0!))
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Absolute, 20.0!))
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Absolute, 20.0!))
        Me.TableLayoutPanel1.Size = New System.Drawing.Size(776, 470)
        Me.TableLayoutPanel1.TabIndex = 10
        '
        'RecordCounter
        '
        Me.RecordCounter.AutoSize = True
        Me.RecordCounter.Location = New System.Drawing.Point(3, 47)
        Me.RecordCounter.Name = "RecordCounter"
        Me.RecordCounter.Size = New System.Drawing.Size(13, 13)
        Me.RecordCounter.TabIndex = 13
        Me.RecordCounter.Text = "0"
        '
        'LblDateTime
        '
        Me.LblDateTime.AutoSize = True
        Me.LblDateTime.Location = New System.Drawing.Point(3, 0)
        Me.LblDateTime.Name = "LblDateTime"
        Me.LblDateTime.Size = New System.Drawing.Size(22, 13)
        Me.LblDateTime.TabIndex = 12
        Me.LblDateTime.Text = "xxx"
        Me.LblDateTime.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'TxtLog
        '
        Me.TxtLog.AcceptsReturn = True
        Me.TableLayoutPanel1.SetColumnSpan(Me.TxtLog, 2)
        Me.TxtLog.Location = New System.Drawing.Point(171, 100)
        Me.TxtLog.Multiline = True
        Me.TxtLog.Name = "TxtLog"
        Me.TxtLog.ScrollBars = System.Windows.Forms.ScrollBars.Both
        Me.TxtLog.ShortcutsEnabled = False
        Me.TxtLog.Size = New System.Drawing.Size(602, 338)
        Me.TxtLog.TabIndex = 7
        Me.TxtLog.TabStop = False
        Me.TxtLog.Text = "Log Window"
        Me.TxtLog.WordWrap = False
        '
        'GroupBox1
        '
        Me.GroupBox1.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.GroupBox1.AutoSize = True
        Me.GroupBox1.Controls.Add(Me.CntEndRecord)
        Me.GroupBox1.Controls.Add(Me.CntStartRecord)
        Me.GroupBox1.Controls.Add(Me.BtnExtractData)
        Me.GroupBox1.Controls.Add(Me.Label2)
        Me.GroupBox1.Controls.Add(Me.Label1)
        Me.GroupBox1.Controls.Add(Me.BtnFetchData)
        Me.GroupBox1.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.GroupBox1.Location = New System.Drawing.Point(3, 100)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(162, 227)
        Me.GroupBox1.TabIndex = 1
        Me.GroupBox1.TabStop = False
        Me.GroupBox1.Text = "Menu"
        '
        'CntEndRecord
        '
        Me.CntEndRecord.AsciiOnly = True
        Me.CntEndRecord.BeepOnError = True
        Me.CntEndRecord.Culture = New System.Globalization.CultureInfo("en-US")
        Me.CntEndRecord.Location = New System.Drawing.Point(6, 148)
        Me.CntEndRecord.Mask = "00000"
        Me.CntEndRecord.Name = "CntEndRecord"
        Me.CntEndRecord.Size = New System.Drawing.Size(100, 20)
        Me.CntEndRecord.TabIndex = 18
        Me.CntEndRecord.Text = "0"
        Me.CntEndRecord.TextAlign = System.Windows.Forms.HorizontalAlignment.Right
        Me.CntEndRecord.ValidatingType = GetType(Integer)
        '
        'CntStartRecord
        '
        Me.CntStartRecord.AsciiOnly = True
        Me.CntStartRecord.BeepOnError = True
        Me.CntStartRecord.Culture = New System.Globalization.CultureInfo("en-US")
        Me.CntStartRecord.Location = New System.Drawing.Point(6, 99)
        Me.CntStartRecord.Mask = "00000"
        Me.CntStartRecord.Name = "CntStartRecord"
        Me.CntStartRecord.Size = New System.Drawing.Size(100, 20)
        Me.CntStartRecord.TabIndex = 17
        Me.CntStartRecord.Text = "0"
        Me.CntStartRecord.TextAlign = System.Windows.Forms.HorizontalAlignment.Right
        Me.CntStartRecord.ValidatingType = GetType(Integer)
        '
        'BtnExtractData
        '
        Me.BtnExtractData.Location = New System.Drawing.Point(6, 185)
        Me.BtnExtractData.Name = "BtnExtractData"
        Me.BtnExtractData.Size = New System.Drawing.Size(150, 23)
        Me.BtnExtractData.TabIndex = 15
        Me.BtnExtractData.Text = "Extract Data"
        Me.BtnExtractData.UseVisualStyleBackColor = True
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Location = New System.Drawing.Point(6, 132)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(64, 13)
        Me.Label2.TabIndex = 12
        Me.Label2.Text = "End Record"
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Location = New System.Drawing.Point(6, 83)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(67, 13)
        Me.Label1.TabIndex = 10
        Me.Label1.Text = "Start Record"
        '
        'BtnFetchData
        '
        Me.BtnFetchData.Location = New System.Drawing.Point(6, 48)
        Me.BtnFetchData.Name = "BtnFetchData"
        Me.BtnFetchData.Size = New System.Drawing.Size(150, 23)
        Me.BtnFetchData.TabIndex = 6
        Me.BtnFetchData.Text = "Fetch Data from Web"
        Me.BtnFetchData.UseVisualStyleBackColor = True
        '
        'LblFileName
        '
        Me.LblFileName.AutoSize = True
        Me.LblFileName.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.TableLayoutPanel1.SetColumnSpan(Me.LblFileName, 2)
        Me.LblFileName.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.LblFileName.Location = New System.Drawing.Point(171, 0)
        Me.LblFileName.Name = "LblFileName"
        Me.LblFileName.Size = New System.Drawing.Size(53, 15)
        Me.LblFileName.TabIndex = 3
        Me.LblFileName.Text = "FileName"
        Me.LblFileName.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'TxtUrl
        '
        Me.TxtUrl.Anchor = CType((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left), System.Windows.Forms.AnchorStyles)
        Me.TableLayoutPanel1.SetColumnSpan(Me.TxtUrl, 2)
        Me.TxtUrl.Location = New System.Drawing.Point(171, 74)
        Me.TxtUrl.Name = "TxtUrl"
        Me.TxtUrl.Size = New System.Drawing.Size(496, 20)
        Me.TxtUrl.TabIndex = 7
        Me.TxtUrl.Text = "http://www.southafricawargraves.org/search/details.php?id="
        '
        'ProgressBar
        '
        Me.TableLayoutPanel1.SetColumnSpan(Me.ProgressBar, 2)
        Me.ProgressBar.Location = New System.Drawing.Point(171, 452)
        Me.ProgressBar.Maximum = 100000
        Me.ProgressBar.Name = "ProgressBar"
        Me.ProgressBar.Size = New System.Drawing.Size(602, 15)
        Me.ProgressBar.TabIndex = 14
        '
        'FrmMain
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(780, 470)
        Me.Controls.Add(Me.TableLayoutPanel1)
        Me.Name = "FrmMain"
        Me.Text = "Create ROH from WarGraves Project"
        Me.TableLayoutPanel1.ResumeLayout(False)
        Me.TableLayoutPanel1.PerformLayout()
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents TableLayoutPanel1 As TableLayoutPanel
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents CntEndRecord As MaskedTextBox
    Friend WithEvents CntStartRecord As MaskedTextBox
    Friend WithEvents BtnExtractData As Button
    Friend WithEvents Label2 As Label
    Friend WithEvents Label1 As Label
    Friend WithEvents BtnFetchData As Button
    Friend WithEvents TxtUrl As TextBox
    Friend WithEvents LblFileName As Label
    Friend WithEvents TxtLog As TextBox
    Friend WithEvents LblDateTime As Label
    Friend WithEvents RecordCounter As Label
    Friend WithEvents ProgressBar As ProgressBar
End Class
