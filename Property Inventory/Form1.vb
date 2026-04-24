Imports System.Windows.Forms

Public Class Form1
    Private loginAttempts As Integer = 0
    Private Const MAX_LOGIN_ATTEMPTS As Integer = 3

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Me.Text = "Property Inventory System - Login"
        Me.FormBorderStyle = FormBorderStyle.FixedSingle
        Me.MaximizeBox = False
        Me.StartPosition = FormStartPosition.CenterScreen

        TextBox2.PasswordChar = "*"c
        TextBox2.UseSystemPasswordChar = True

        TextBox1.Clear()
        TextBox2.Clear()
        TextBox1.Focus()
    End Sub

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        If String.IsNullOrWhiteSpace(TextBox1.Text) Then
            MessageBox.Show("Please enter username", "Validation Error", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            TextBox1.Focus()
            Return
        End If

        If String.IsNullOrWhiteSpace(TextBox2.Text) Then
            MessageBox.Show("Please enter password", "Validation Error", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            TextBox2.Focus()
            Return
        End If

        If loginAttempts >= MAX_LOGIN_ATTEMPTS Then
            MessageBox.Show("Too many failed login attempts. Please restart the application.", "Account Locked", MessageBoxButtons.OK, MessageBoxIcon.Error)
            Application.Exit()
            Return
        End If

        Button1.Enabled = False
        Button1.Text = "Authenticating..."
        Application.DoEvents()

        Dim authResult As Boolean = AuthenticateUser(TextBox1.Text.Trim(), TextBox2.Text)

        Button1.Enabled = True
        Button1.Text = "SIGN IN"

        If authResult Then
            loginAttempts = 0
            LogActivity(TextBox1.Text.Trim(), "Login Successful")

            Dim dashboard As New Form2()
            dashboard.Show()
            Me.Hide()
        Else
            loginAttempts = loginAttempts + 1
            Dim remainingAttempts As Integer = MAX_LOGIN_ATTEMPTS - loginAttempts

            LogActivity(TextBox1.Text.Trim(), "Login Failed - Invalid credentials")

            Dim message As String = "Invalid username or password."
            If remainingAttempts > 0 Then
                message = message & vbCrLf & "You have " & remainingAttempts.ToString() & " attempt(s) remaining."
            Else
                message = message & vbCrLf & "Application will now close."
            End If

            MessageBox.Show(message, "Login Failed", MessageBoxButtons.OK, MessageBoxIcon.Error)

            TextBox2.Clear()
            TextBox2.Focus()

            If remainingAttempts = 0 Then
                Application.Exit()
            End If
        End If
    End Sub

    Private Function AuthenticateUser(username As String, password As String) As Boolean
        If username = "admin" AndAlso password = "admin123" Then
            UserSession.Username = "admin"
            UserSession.FullName = "System Administrator"
            UserSession.UserRole = "Admin"
            Return True
        End If

        If username = "user" AndAlso password = "user123" Then
            UserSession.Username = "user"
            UserSession.FullName = "Standard User"
            UserSession.UserRole = "User"
            Return True
        End If

        Return False
    End Function

    ' Fixed LogActivity using simple file append
    Private Sub LogActivity(username As String, action As String)
        Try
            Dim logPath As String = Application.StartupPath & "\audit_log.txt"
            Dim logEntry As String = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss") & " | " & username & " | " & action

            ' Append to file
            My.Computer.FileSystem.WriteAllText(logPath, logEntry & vbCrLf, True)
        Catch ex As Exception
            ' Silently fail
        End Try
    End Sub

    Private Sub TextBox2_KeyPress(sender As Object, e As KeyPressEventArgs) Handles TextBox2.KeyPress
        If e.KeyChar = Convert.ToChar(Keys.Enter) Then
            Button1.PerformClick()
        End If
    End Sub
End Class