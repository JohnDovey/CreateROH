<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmMain
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()> _
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
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.CntEndRecord = New System.Windows.Forms.MaskedTextBox()
        Me.CntStartRecord = New System.Windows.Forms.MaskedTextBox()
        Me.BtnExtractData = New System.Windows.Forms.Button()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.BtnFetchData = New System.Windows.Forms.Button()
        Me.LblDateTime = New System.Windows.Forms.Label()
        Me.LblFileName = New System.Windows.Forms.Label()
        Me.RecordCounter = New System.Windows.Forms.NumericUpDown()
        Me.TxtUrl = New System.Windows.Forms.TextBox()
        Me.TxtLog = New System.Windows.Forms.TextBox()
        Me.GroupBox1.SuspendLayout()
        CType(Me.RecordCounter, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
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
        Me.GroupBox1.Location = New System.Drawing.Point(12, 12)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(162, 425)
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
        'LblDateTime
        '
        Me.LblDateTime.AutoSize = True
        Me.LblDateTime.Location = New System.Drawing.Point(184, 42)
        Me.LblDateTime.Name = "LblDateTime"
        Me.LblDateTime.Size = New System.Drawing.Size(22, 13)
        Me.LblDateTime.TabIndex = 2
        Me.LblDateTime.Text = "xxx"
        Me.LblDateTime.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'LblFileName
        '
        Me.LblFileName.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.LblFileName.AutoSize = True
        Me.LblFileName.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.LblFileName.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.LblFileName.Location = New System.Drawing.Point(180, 22)
        Me.LblFileName.Name = "LblFileName"
        Me.LblFileName.Size = New System.Drawing.Size(53, 15)
        Me.LblFileName.TabIndex = 3
        Me.LblFileName.Text = "FileName"
        Me.LblFileName.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'RecordCounter
        '
        Me.RecordCounter.AutoSize = True
        Me.RecordCounter.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.RecordCounter.CausesValidation = False
        Me.RecordCounter.Cursor = System.Windows.Forms.Cursors.No
        Me.RecordCounter.Enabled = False
        Me.RecordCounter.Location = New System.Drawing.Point(604, 12)
        Me.RecordCounter.Maximum = New Decimal(New Integer() {100000, 0, 0, 0})
        Me.RecordCounter.Name = "RecordCounter"
        Me.RecordCounter.Size = New System.Drawing.Size(120, 16)
        Me.RecordCounter.TabIndex = 4
        Me.RecordCounter.ThousandsSeparator = True
        '
        'TxtUrl
        '
        Me.TxtUrl.Location = New System.Drawing.Point(180, 63)
        Me.TxtUrl.Name = "TxtUrl"
        Me.TxtUrl.Size = New System.Drawing.Size(608, 20)
        Me.TxtUrl.TabIndex = 7
        Me.TxtUrl.Text = "http://www.southafricawargraves.org/search/details.php?id="
        '
        'TxtLog
        '
        Me.TxtLog.AcceptsReturn = True
        Me.TxtLog.Location = New System.Drawing.Point(187, 111)
        Me.TxtLog.Multiline = True
        Me.TxtLog.Name = "TxtLog"
        Me.TxtLog.ScrollBars = System.Windows.Forms.ScrollBars.Both
        Me.TxtLog.ShortcutsEnabled = False
        Me.TxtLog.Size = New System.Drawing.Size(601, 326)
        Me.TxtLog.TabIndex = 7
        Me.TxtLog.TabStop = False
        Me.TxtLog.Text = "Log Window"
        Me.TxtLog.WordWrap = False
        '
        'FrmMain
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(800, 450)
        Me.Controls.Add(Me.TxtLog)
        Me.Controls.Add(Me.TxtUrl)
        Me.Controls.Add(Me.RecordCounter)
        Me.Controls.Add(Me.LblFileName)
        Me.Controls.Add(Me.LblDateTime)
        Me.Controls.Add(Me.GroupBox1)
        Me.Name = "FrmMain"
        Me.Text = "Create ROH from WarGraves Project"
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        CType(Me.RecordCounter, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents LblDateTime As Label
    Friend WithEvents LblFileName As Label
    Friend WithEvents RecordCounter As NumericUpDown
    Friend WithEvents BtnFetchData As Button
    Friend WithEvents TxtUrl As TextBox
    Friend WithEvents TxtLog As TextBox
    Friend WithEvents Label2 As Label
    Friend WithEvents Label1 As Label
    Friend WithEvents BtnExtractData As Button
    Friend WithEvents CntStartRecord As MaskedTextBox
    Friend WithEvents CntEndRecord As MaskedTextBox
End Class
