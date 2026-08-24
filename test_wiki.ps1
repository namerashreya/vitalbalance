$url = "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Ginger_Tea_with_lemon_and_honey.jpg/800px-Ginger_Tea_with_lemon_and_honey.jpg"
$out = "c:\xampp\htdocs\vitalbalance_db\assets\images\remedies\ginger_test.jpg"
try {
    Invoke-WebRequest -Uri $url -OutFile $out -UserAgent "Mozilla/5.0"
    Write-Host "Success"
} catch {
    Write-Host "Error: $_"
}
