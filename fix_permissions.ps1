$path = "d:\Desktop-projects\nickmark-ecommerce\bootstrap\cache"
$acl = Get-Acl $path
$rule = New-Object System.Security.AccessControl.FileSystemAccessRule("Gabriel", "FullControl", "ContainerInherit, ObjectInherit", "None", "Allow")
$acl.SetAccessRule($rule)
Set-Acl $path $acl
Write-Host "Permissions set successfully"
