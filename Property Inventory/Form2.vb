Imports System.Windows.Forms
Imports System.Drawing.Printing

Public Class Form2
    Private currentUser As String = ""
    Private currentUserRole As String = ""
    Private currentUserFullName As String = ""

    Private Sub Form2_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Me.WindowState = FormWindowState.Maximized
        Me.Text = "Property Inventory System - Dashboard"

        LoadUserInfo()
        DisplayWelcomeMessage()
        LoadDashboardMetrics()
        LoadRecentAssets()
        StyleSidebarButtons()
        SetupDataGridView()
    End Sub

    Private Sub LoadUserInfo()
        currentUser = UserSession.Username
        currentUserRole = UserSession.UserRole
        currentUserFullName = UserSession.FullName

        If String.IsNullOrEmpty(currentUser) Then currentUser = "User"
        If String.IsNullOrEmpty(currentUserRole) Then currentUserRole = "User"
        If String.IsNullOrEmpty(currentUserFullName) Then currentUserFullName = currentUser
    End Sub

    Public Sub SetCurrentUser(username As String)
        currentUser = username
        LoadUserInfo()
    End Sub

    Private Sub DisplayWelcomeMessage()
        Dim timeOfDay As String = GetTimeOfDayGreeting()
        Label2.Text = timeOfDay & ", " & currentUserFullName & "!"
        Label3.Text = "Role: " & currentUserRole & " | " & DateTime.Now.ToString("dddd, MMMM dd, yyyy") & " | " & DateTime.Now.ToString("h:mm tt")

        If currentUserRole.ToLower() <> "admin" Then
            Button4.Visible = False
            Button5.Visible = False
        End If
    End Sub

    Private Function GetTimeOfDayGreeting() As String
        Dim hour As Integer = DateTime.Now.Hour
        If hour < 12 Then
            Return "Good Morning"
        ElseIf hour < 17 Then
            Return "Good Afternoon"
        Else
            Return "Good Evening"
        End If
    End Function

    Private Sub StyleSidebarButtons()
        Dim buttons As Button() = {Button1, Button2, Button3, Button4, Button5, Button6}

        For Each btn In buttons
            btn.FlatStyle = FlatStyle.Flat
            btn.FlatAppearance.BorderSize = 0
            btn.FlatAppearance.MouseOverBackColor = Color.FromArgb(52, 73, 94)
            btn.Cursor = Cursors.Hand
        Next

        Button8.BackColor = Color.FromArgb(52, 152, 219)
        Button8.FlatStyle = FlatStyle.Flat
        Button8.FlatAppearance.BorderSize = 0

        Button7.BackColor = Color.FromArgb(46, 204, 113)
        Button7.FlatStyle = FlatStyle.Flat
        Button7.FlatAppearance.BorderSize = 0
    End Sub

    Private Sub SetupDataGridView()
        DataGridView1.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill
        DataGridView1.SelectionMode = DataGridViewSelectionMode.FullRowSelect
        DataGridView1.ReadOnly = True
        DataGridView1.AllowUserToAddRows = False
        DataGridView1.AllowUserToDeleteRows = False
        DataGridView1.RowHeadersVisible = False
        DataGridView1.BackgroundColor = Color.White

        DataGridView1.ColumnHeadersDefaultCellStyle.BackColor = Color.FromArgb(52, 152, 219)
        DataGridView1.ColumnHeadersDefaultCellStyle.ForeColor = Color.White
        DataGridView1.ColumnHeadersDefaultCellStyle.Font = New Font("Segoe UI", 10, FontStyle.Bold)
        DataGridView1.ColumnHeadersHeight = 40

        DataGridView1.RowsDefaultCellStyle.Font = New Font("Segoe UI", 9)
        DataGridView1.AlternatingRowsDefaultCellStyle.BackColor = Color.FromArgb(248, 249, 250)
    End Sub

    Private Sub LoadDashboardMetrics()
        Label4.Text = "TOTAL ASSETS" & vbCrLf & "1,248"
        Label5.Text = "AVAILABLE" & vbCrLf & "534"
        Label6.Text = "IN USE" & vbCrLf & "612"
        Label7.Text = "IN REPAIR" & vbCrLf & "102"

        Dim metricLabels As Label() = {Label4, Label5, Label6, Label7}
        For Each lbl In metricLabels
            lbl.TextAlign = ContentAlignment.MiddleCenter
            lbl.Dock = DockStyle.Fill
            lbl.Font = New Font("Segoe UI", 11, FontStyle.Bold)
            lbl.BackColor = Color.White
        Next
    End Sub

    Private Sub LoadRecentAssets()
        Dim dt As New DataTable()

        dt.Columns.Add("Asset ID", GetType(String))
        dt.Columns.Add("Asset Name", GetType(String))
        dt.Columns.Add("Category", GetType(String))
        dt.Columns.Add("Status", GetType(String))
        dt.Columns.Add("Location", GetType(String))
        dt.Columns.Add("Last Updated", GetType(String))

        dt.Rows.Add("AST-001", "Dell XPS Laptop", "Electronics", "In Use", "Floor 3 - IT Dept", DateTime.Now.AddDays(-1).ToShortDateString())
        dt.Rows.Add("AST-002", "Epson Projector", "AV Equipment", "Available", "Conference Room A", DateTime.Now.AddDays(-2).ToShortDateString())
        dt.Rows.Add("AST-003", "Herman Miller Chair", "Furniture", "In Repair", "Maintenance Bay", DateTime.Now.AddDays(-3).ToShortDateString())
        dt.Rows.Add("AST-004", "iPhone 14", "Mobile Devices", "Available", "IT Storage", DateTime.Now.AddDays(-1).ToShortDateString())
        dt.Rows.Add("AST-005", "Samsung 27 Inch Monitor", "Electronics", "In Use", "Floor 2 - Sales", DateTime.Now.ToShortDateString())
        dt.Rows.Add("AST-006", "HP LaserJet Printer", "Electronics", "Available", "Floor 1 - Admin", DateTime.Now.AddDays(-4).ToShortDateString())
        dt.Rows.Add("AST-007", "Conference Camera", "AV Equipment", "In Use", "Conference Room B", DateTime.Now.AddDays(-2).ToShortDateString())
        dt.Rows.Add("AST-008", "Standing Desk", "Furniture", "Available", "Floor 2 - Open Area", DateTime.Now.AddDays(-5).ToShortDateString())

        DataGridView1.DataSource = dt
        AddHandler DataGridView1.CellFormatting, AddressOf DataGridView1_CellFormatting
    End Sub

    Private Sub DataGridView1_CellFormatting(sender As Object, e As DataGridViewCellFormattingEventArgs)
        If e.RowIndex >= 0 AndAlso DataGridView1.Columns.Contains("Status") AndAlso e.ColumnIndex = DataGridView1.Columns("Status").Index Then
            Dim status As String = ""
            If e.Value IsNot Nothing Then
                status = e.Value.ToString()
            End If

            If Not String.IsNullOrEmpty(status) Then
                Dim statusLower As String = status.ToLower()
                If statusLower = "available" Then
                    e.CellStyle.BackColor = Color.FromArgb(212, 237, 218)
                    e.CellStyle.ForeColor = Color.FromArgb(40, 167, 69)
                    e.CellStyle.Font = New Font(DataGridView1.Font, FontStyle.Bold)
                ElseIf statusLower = "in use" Then
                    e.CellStyle.BackColor = Color.FromArgb(204, 229, 255)
                    e.CellStyle.ForeColor = Color.FromArgb(0, 123, 255)
                    e.CellStyle.Font = New Font(DataGridView1.Font, FontStyle.Bold)
                ElseIf statusLower = "in repair" Then
                    e.CellStyle.BackColor = Color.FromArgb(248, 215, 218)
                    e.CellStyle.ForeColor = Color.FromArgb(220, 53, 69)
                    e.CellStyle.Font = New Font(DataGridView1.Font, FontStyle.Bold)
                End If
            End If
        End If
    End Sub

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        LoadDashboardMetrics()
        LoadRecentAssets()
        Label2.Text = "Dashboard - " & GetTimeOfDayGreeting()
        Label3.Text = "Real-time overview of company assets and valuations"
        HighlightActiveButton(Button1)
    End Sub

    Private Sub Button2_Click(sender As Object, e As EventArgs) Handles Button2.Click
        Label2.Text = "Asset Inventory Management"
        Label3.Text = "Add, edit, delete, and manage all company assets"
        MessageBox.Show("Inventory Management Module - Coming Soon", "Information", MessageBoxButtons.OK, MessageBoxIcon.Information)
        HighlightActiveButton(Button2)
    End Sub

    Private Sub Button3_Click(sender As Object, e As EventArgs) Handles Button3.Click
        Label2.Text = "Reports and Analytics"
        Label3.Text = "Generate comprehensive reports and export data"
        ShowReportOptions()
        HighlightActiveButton(Button3)
    End Sub

    Private Sub Button4_Click(sender As Object, e As EventArgs) Handles Button4.Click
        If currentUserRole.ToLower() = "admin" Then
            Label2.Text = "User Access Management"
            Label3.Text = "Manage system users, roles, and permissions"
            MessageBox.Show("User Management Module - Coming Soon", "Admin Access", MessageBoxButtons.OK, MessageBoxIcon.Information)
        Else
            MessageBox.Show("Access Denied. Admin privileges required.", "Unauthorized", MessageBoxButtons.OK, MessageBoxIcon.Warning)
        End If
        HighlightActiveButton(Button4)
    End Sub

    Private Sub Button5_Click(sender As Object, e As EventArgs) Handles Button5.Click
        If currentUserRole.ToLower() = "admin" Then
            Label2.Text = "System Audit Logs"
            Label3.Text = "View all system activities and user actions"
            ShowAuditLogs()
        Else
            MessageBox.Show("Access Denied. Admin privileges required.", "Unauthorized", MessageBoxButtons.OK, MessageBoxIcon.Warning)
        End If
        HighlightActiveButton(Button5)
    End Sub

    Private Sub Button6_Click(sender As Object, e As EventArgs) Handles Button6.Click
        Dim result As DialogResult = MessageBox.Show("Are you sure you want to logout?", "Confirm Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question)

        If result = DialogResult.Yes Then
            LogActivity("User Logout")
            UserSession.ClearSession()

            Dim login As New Form1()
            login.Show()
            Me.Close()
        End If
    End Sub

    Private Sub Button7_Click(sender As Object, e As EventArgs) Handles Button7.Click
        PrintReport()
    End Sub

    Private Sub Button8_Click(sender As Object, e As EventArgs) Handles Button8.Click
        MessageBox.Show("Asset Management Console - Full CRUD operations available here", "Manage Assets", MessageBoxButtons.OK, MessageBoxIcon.Information)
    End Sub

    Private Sub HighlightActiveButton(activeButton As Button)
        Dim buttons As Button() = {Button1, Button2, Button3, Button4, Button5, Button6}

        For Each btn In buttons
            If btn Is activeButton Then
                btn.BackColor = Color.FromArgb(52, 152, 219)
                btn.ForeColor = Color.White
            Else
                btn.BackColor = Color.FromArgb(29, 41, 81)
                btn.ForeColor = Color.White
            End If
        Next
    End Sub

    Private Sub ShowReportOptions()
        Dim reportForm As New Form()
        reportForm.Text = "Generate Report"
        reportForm.Size = New Size(400, 300)
        reportForm.StartPosition = FormStartPosition.CenterParent
        reportForm.FormBorderStyle = FormBorderStyle.FixedDialog

        Dim lbl As New Label()
        lbl.Text = "Select Report Type:"
        lbl.Location = New Point(20, 20)
        lbl.Size = New Size(150, 25)

        Dim cmb As New ComboBox()
        cmb.Location = New Point(20, 50)
        cmb.Size = New Size(340, 25)
        cmb.DropDownStyle = ComboBoxStyle.DropDownList
        cmb.Items.AddRange(New String() {"Asset Summary Report", "Asset Status Report", "Category-wise Report", "Depreciation Report", "Warranty Expiry Report"})
        cmb.SelectedIndex = 0

        Dim btnGenerate As New Button()
        btnGenerate.Text = "Generate"
        btnGenerate.Location = New Point(100, 100)
        btnGenerate.Size = New Size(100, 35)
        btnGenerate.BackColor = Color.FromArgb(52, 152, 219)
        btnGenerate.ForeColor = Color.White
        btnGenerate.FlatStyle = FlatStyle.Flat

        Dim btnCancel As New Button()
        btnCancel.Text = "Cancel"
        btnCancel.Location = New Point(220, 100)
        btnCancel.Size = New Size(100, 35)
        btnCancel.FlatStyle = FlatStyle.Flat

        AddHandler btnGenerate.Click, Sub()
                                          MessageBox.Show("Generating " & cmb.SelectedItem.ToString() & "... Feature coming soon.", "Report", MessageBoxButtons.OK, MessageBoxIcon.Information)
                                          reportForm.Close()
                                      End Sub

        AddHandler btnCancel.Click, Sub() reportForm.Close()

        reportForm.Controls.Add(lbl)
        reportForm.Controls.Add(cmb)
        reportForm.Controls.Add(btnGenerate)
        reportForm.Controls.Add(btnCancel)

        reportForm.ShowDialog()
    End Sub

    Private Sub PrintReport()
        Using printDialog As New PrintDialog()
            Dim printDocument As New PrintDocument()
            AddHandler printDocument.PrintPage, AddressOf PrintDocument_PrintPage
            printDialog.Document = printDocument

            If printDialog.ShowDialog() = DialogResult.OK Then
                Try
                    printDocument.Print()
                    MessageBox.Show("Report sent to printer successfully.", "Print", MessageBoxButtons.OK, MessageBoxIcon.Information)
                    LogActivity("Printed dashboard report")
                Catch ex As Exception
                    MessageBox.Show("Print error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
                End Try
            End If
        End Using
    End Sub

    Private Sub PrintDocument_PrintPage(sender As Object, e As PrintPageEventArgs)
        Dim font As New Font("Arial", 10)
        Dim titleFont As New Font("Arial", 18, FontStyle.Bold)
        Dim headerFont As New Font("Arial", 12, FontStyle.Bold)
        Dim yPos As Single = e.MarginBounds.Top
        Dim leftMargin As Single = e.MarginBounds.Left
        Dim lineHeight As Single = font.GetHeight(e.Graphics)

        e.Graphics.DrawString("Property Inventory System", titleFont, Brushes.Navy, leftMargin, yPos)
        yPos = yPos + (lineHeight * 2)

        e.Graphics.DrawString("Report Generated: " & DateTime.Now.ToString("MMMM dd, yyyy h:mm tt"), font, Brushes.Black, leftMargin, yPos)
        yPos = yPos + lineHeight

        e.Graphics.DrawString("Generated By: " & currentUserFullName, font, Brushes.Black, leftMargin, yPos)
        yPos = yPos + (lineHeight * 2)

        e.Graphics.DrawString("ASSET SUMMARY", headerFont, Brushes.Black, leftMargin, yPos)
        yPos = yPos + lineHeight
        e.Graphics.DrawLine(Pens.Black, leftMargin, yPos, e.MarginBounds.Right, yPos)
        yPos = yPos + 5

        Dim total As String = Label4.Text.Split(vbCrLf)(1)
        Dim available As String = Label5.Text.Split(vbCrLf)(1)
        Dim inUse As String = Label6.Text.Split(vbCrLf)(1)
        Dim inRepair As String = Label7.Text.Split(vbCrLf)(1)

        e.Graphics.DrawString("Total Assets: " & total, font, Brushes.Black, leftMargin, yPos)
        yPos = yPos + lineHeight
        e.Graphics.DrawString("Available: " & available, font, Brushes.Green, leftMargin, yPos)
        yPos = yPos + lineHeight
        e.Graphics.DrawString("In Use: " & inUse, font, Brushes.Blue, leftMargin, yPos)
        yPos = yPos + lineHeight
        e.Graphics.DrawString("In Repair: " & inRepair, font, Brushes.Red, leftMargin, yPos)
        yPos = yPos + (lineHeight * 2)

        e.Graphics.DrawString("*** End of Report ***", font, Brushes.Gray, leftMargin, yPos)
    End Sub

    Private Sub ShowAuditLogs()
        Try
            Dim logPath As String = Application.StartupPath & "\audit_log.txt"

            If My.Computer.FileSystem.FileExists(logPath) Then
                Dim logs As String = My.Computer.FileSystem.ReadAllText(logPath)

                Dim logForm As New Form()
                logForm.Text = "System Audit Logs"
                logForm.Size = New Size(800, 500)
                logForm.StartPosition = FormStartPosition.CenterParent

                Dim txtLogs As New RichTextBox()
                txtLogs.Dock = DockStyle.Fill
                txtLogs.Font = New Font("Consolas", 9)
                txtLogs.Text = logs
                txtLogs.ReadOnly = True

                Dim btnClose As New Button()
                btnClose.Text = "Close"
                btnClose.Dock = DockStyle.Bottom
                btnClose.Height = 35
                btnClose.BackColor = Color.FromArgb(52, 152, 219)
                btnClose.ForeColor = Color.White
                btnClose.FlatStyle = FlatStyle.Flat
                AddHandler btnClose.Click, Sub() logForm.Close()

                logForm.Controls.Add(txtLogs)
                logForm.Controls.Add(btnClose)
                logForm.ShowDialog()
            Else
                MessageBox.Show("No audit logs found.", "System Logs", MessageBoxButtons.OK, MessageBoxIcon.Information)
            End If
        Catch ex As Exception
            MessageBox.Show("Error reading logs: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    ' Fixed LogActivity using My.Computer.FileSystem (no StreamWriter required)
    Private Sub LogActivity(action As String)
        Try
            Dim safeUser As String = ""
            Dim safeRole As String = ""

            If String.IsNullOrEmpty(currentUser) Then
                safeUser = "Unknown"
            Else
                safeUser = currentUser
            End If

            If String.IsNullOrEmpty(currentUserRole) Then
                safeRole = "Unknown"
            Else
                safeRole = currentUserRole
            End If

            Dim logPath As String = Application.StartupPath & "\audit_log.txt"
            Dim logEntry As String = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss") & " | " & safeUser & " (" & safeRole & ") | " & action

            My.Computer.FileSystem.WriteAllText(logPath, logEntry & vbCrLf, True)
        Catch ex As Exception
            ' Silently fail
        End Try
    End Sub

    Private Sub DataGridView1_CellDoubleClick(sender As Object, e As DataGridViewCellEventArgs) Handles DataGridView1.CellDoubleClick
        If e.RowIndex >= 0 Then
            Dim assetId As String = ""
            Dim assetName As String = ""

            If DataGridView1.Rows(e.RowIndex).Cells(0).Value IsNot Nothing Then
                assetId = DataGridView1.Rows(e.RowIndex).Cells(0).Value.ToString()
            End If
            If DataGridView1.Rows(e.RowIndex).Cells(1).Value IsNot Nothing Then
                assetName = DataGridView1.Rows(e.RowIndex).Cells(1).Value.ToString()
            End If

            MessageBox.Show("Asset Details" & vbCrLf & "ID: " & assetId & vbCrLf & "Name: " & assetName & vbCrLf & vbCrLf & "Detailed view coming soon.", "Asset Information", MessageBoxButtons.OK, MessageBoxIcon.Information)
        End If
    End Sub

    Private Sub PictureBox1_Click(sender As Object, e As EventArgs) Handles PictureBox1.Click
        Button1.PerformClick()
    End Sub
End Class