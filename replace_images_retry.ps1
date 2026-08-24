$images = @{
    "acv.jpg" = "https://images.pexels.com/photos/5946961/pexels-photo-5946961.jpeg?auto=compress&cs=tinysrgb&w=800"
    "clove_oil.jpg" = "https://images.pexels.com/photos/672051/pexels-photo-672051.jpeg?auto=compress&cs=tinysrgb&w=800"
}

$dir = "c:\xampp\htdocs\vitalbalance_db\assets\images\remedies\"
$userAgent = "Mozilla/5.0"

foreach ($key in $images.Keys) {
    try {
        Write-Host "Retry replacement for $key..."
        Invoke-WebRequest -Uri $images[$key] -OutFile ($dir + $key) -UserAgent $userAgent
        Write-Host "Success: $key"
    } catch {
        Write-Host "Error $key : $_"
    }
}
