$images = @{
    "salt_water.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Glass-of-water.jpg"
    "aloe.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Aloe_vera_gel.jpg"
    "peppermint.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Peppermint_tea.jpg"
    "acv.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Apple_Cider_Vinegar_(4108653248).jpg"
    "chamomile.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Glass_of_chamomile_tea.jpg"
    "garlic.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Garlic_Bulbs.jpg"
    "cucumber.jpg" = "https://commons.wikimedia.org/wiki/Special:FilePath/Cucumber_Slices.JPG"
    "steam.jpg" = "https://images.pexels.com/photos/3873134/pexels-photo-3873134.jpeg?auto=compress&cs=tinysrgb&w=800"
}

$dir = "c:\xampp\htdocs\vitalbalance_db\assets\images\remedies\"
if (!(Test-Path -Path $dir)) { 
    New-Item -ItemType Directory -Path $dir -Force | Out-Null
}

$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"

foreach ($key in $images.Keys) {
    $filePath = $dir + $key
    if (Test-Path $filePath) {
        Write-Host "$key already exists, skipping."
        continue
    }

    try {
        Write-Host "Downloading $key..."
        Invoke-WebRequest -Uri $images[$key] -OutFile $filePath -UserAgent $userAgent
        Write-Host "Success: $key"
    } catch {
        Write-Host "Error downloading $key : $_"
        # Create a text-based placeholder if download fails (SVG) to avoid 404 in browser
        $svgContent = @"
<svg width='800' height='600' xmlns='http://www.w3.org/2000/svg'>
    <rect width='100%' height='100%' fill='#e2e8f0'/>
    <text x='50%' y='50%' font-family='Arial' font-size='24' fill='#64748b' text-anchor='middle' dominant-baseline='middle'>$key</text>
</svg>
"@
        Set-Content -Path ($dir + $key -replace ".jpg", ".svg") -Value $svgContent
    }
}
