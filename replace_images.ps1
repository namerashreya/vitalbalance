$images = @{
    "honey_lemon.jpg" = "https://images.pexels.com/photos/1417945/pexels-photo-1417945.jpeg?cs=srgb&dl=pexels-quang-nguyen-vinh-222549-1417945.jpg&fm=jpg&w=800"
    "acv.jpg" = "https://upload.wikimedia.org/wikipedia/commons/f/f6/Apple_Cider_Vinegar_%284108653248%29.jpg"
    "clove_oil.jpg" = "https://images.pexels.com/photos/6621458/pexels-photo-6621458.jpeg?cs=srgb&dl=pexels-tara-winstead-6621458.jpg&fm=jpg&w=800"
    "steam.jpg" = "https://images.pexels.com/photos/3873134/pexels-photo-3873134.jpeg?cs=srgb&dl=pexels-gustavo-fring-3873134.jpg&fm=jpg&w=800"
}

$dir = "c:\xampp\htdocs\vitalbalance_db\assets\images\remedies\"

$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"

foreach ($key in $images.Keys) {
    $filePath = $dir + $key
    # Process replacement
    try {
        Write-Host "Downloading replacement for $key..."
        Invoke-WebRequest -Uri $images[$key] -OutFile $filePath -UserAgent $userAgent
        Write-Host "Successfully replaced: $key"
    } catch {
        Write-Host "Error downloading $key : $_"
    }
}
