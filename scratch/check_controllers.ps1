$phpExe = 'C:\xampp\php\php.exe'
$dir = 'c:\xampp\htdocs\integ\Controller'
$files = Get-ChildItem -Path $dir -Recurse -Filter '*.php'
$errors = @()
foreach ($f in $files) {
    $result = & $phpExe -l $f.FullName 2>&1
    if ($result -notmatch 'No syntax errors') {
        $errors += "ERROR: $($f.Name) -> $result"
    }
}
if ($errors.Count -eq 0) {
    Write-Output "All Controller PHP files OK - No syntax errors."
} else {
    $errors | ForEach-Object { Write-Output $_ }
}
