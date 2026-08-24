$urls = @(
    "https://images.pexels.com/photos/1030990/pexels-photo-1030990.jpeg?auto=compress&cs=tinysrgb&w=800",
    "https://images.pexels.com/photos/1630134/pexels-photo-1630134.jpeg?auto=compress&cs=tinysrgb&w=800",
    "https://images.pexels.com/photos/6620950/pexels-photo-6620950.jpeg?auto=compress&cs=tinysrgb&w=800"
)

$out = "c:\xampp\htdocs\vitalbalance_db\assets\images\remedies\acv.jpg"

foreach ($url in $urls) {
    try {
        Write-Host "Trying $url..."
        Invoke-WebRequest -Uri $url -OutFile $out -UserAgent "Mozilla/5.0"
        Write-Host "Success ACV!"
        break
    } catch {
        Write-Host "Failed: $_"
    }
}
