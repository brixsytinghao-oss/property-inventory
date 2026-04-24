Public Class Form1
    ' Database connection string - update with your SQL Server details
    Private ReadOnly connectionString As String = "Data Source=localhost;Initial Catalog=PropertyInventory;Integrated Security=True"

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        ' Validate input
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

        ' Authenticate user
        If AuthenticateUser(TextBox1.Text.Trim(), TextBox2.Text) Then
            ' Log successful login
            LogActivity(TextBox1.Text.Trim(), "Login Successful")

            ' Open main dashboard
            Dim dashboard As New Form2()
            dashboard.Show()
            Me.Hide()
        Else
            LogActivity(TextBox1.Text.Trim(), "Login Failed - Invalid credentials")
            MessageBox.Show("Invalid username or password", "Login Failed", MessageBoxButtons.OK, MessageBoxIcon.Error)
            TextBox2.Clear()
            TextBox2.Focus()
        End If
    End Sub

    Private Function AuthenticateUser(username As String, password As String) As Boolean
        ' For demo purposes - replace with actual database authentication
        ' In production, use hashed passwords and parameterized queries

        ' Demo credentials (remove in production)
        If username = "admin" AndAlso password = "admin123" Then
            Return True
        End If

        ' Database authentication example (uncomment and configure)
        ' Try
        '     Using conn As New SqlConnection(connectionString)
        '         conn.Open()
        '         Dim query As String = "SELECT COUNT(1) FROM Users WHERE Username=@username AND Password=HASHBYTES('SHA2_256', @password)"
        '         Using cmd As New SqlCommand(query, conn)
        '             cmd.Parameters.AddWithValue("@username", username)
        '             cmd.Parameters.AddWithValue("@password", password)
        '             Dim count As Integer = Convert.ToInt32(cmd.ExecuteScalar())
        '             Return count = 1
        '         End Using
        '     End Using
        ' Catch ex As Exception
        '     MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        '     Return False
        ' End Try

        Return False
    End Function

    Private Sub LogActivity(username As String, action As String)
        ' Log user activity to database or file
        Try
            Using writer As New System.IO.StreamWriter("audit_log.txt", True)
                writer.WriteLine($"{DateTime.Now:yyyy-MM-dd HH:mm:ss} | {username} | {action}")
            End Using
        Catch ex As Exception
            ' Silently fail for logging
        End Try
    End Sub

    Private Sub TextBox2_KeyPress(sender As Object, e As KeyPressEventArgs) Handles TextBox2.KeyPress
        ' Allow Enter key to trigger login
        If e.KeyChar = Convert.ToChar(Keys.Enter) Then
            Button1.PerformClick()
        End If
    End Sub

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        ' Set password character masking
        TextBox2.PasswordChar = "●"c
        TextBox2.UseSystemPasswordChar = True
    End Sub
End Class