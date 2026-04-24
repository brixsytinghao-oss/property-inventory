Public Class UserSession
    ' Shared properties (accessible from anywhere in the app)
    Public Shared Property Username As String = ""
    Public Shared Property FullName As String = ""
    Public Shared Property UserRole As String = "User"
    Public Shared Property UserID As String = ""

    ' Method to clear session
    Public Shared Sub ClearSession()
        Username = ""
        FullName = ""
        UserRole = "User"
        UserID = ""
    End Sub

    ' Method to check if user is logged in
    Public Shared Function IsLoggedIn() As Boolean
        Return Not String.IsNullOrEmpty(Username)
    End Function

    ' Method to check if user is admin
    Public Shared Function IsAdmin() As Boolean
        Return UserRole.ToLower() = "admin"
    End Function
End Class