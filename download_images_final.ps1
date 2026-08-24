$images = @{
    "honey_lemon.jpg" = "https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "ginger.jpg" = "https://images.unsplash.com/photo-1627916607164-7b702418579d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "turmeric_milk.jpg" = "https://images.unsplash.com/photo-1540933569681-426b384632c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "salt_water.jpg" = "https://images.unsplash.com/photo-1520038410233-7141dd882f5d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "aloe.jpg" = "https://images.unsplash.com/photo-1628178652309-8d77d1303102?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "peppermint.jpg" = "https://images.unsplash.com/photo-1625026937666-ac2368c81881?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "steam.jpg" = "https://images.unsplash.com/photo-1596483786184-f25b2713f05c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "clove_oil.jpg" = "https://images.unsplash.com/photo-1549480017-d76466a4b7e8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "acv.jpg" = "https://images.unsplash.com/photo-1577158756857-41a4697397b9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "chamomile.jpg" = "https://images.unsplash.com/photo-1546738962-430983d9435b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "garlic.jpg" = "https://images.unsplash.com/photo-1634591784949-a3d823467f2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    "cucumber.jpg" = "https://images.unsplash.com/photo-1565551949168-963b5167bb30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
}

$dir = "c:\xampp\htdocs\vitalbalance_db\assets\images\remedies\"
if (!(Test-Path -Path $dir)) { 
    New-Item -ItemType Directory -Path $dir -Force | Out-Null
}

$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"

foreach ($key in $images.Keys) {
    $filePath = $dir + $key
    try {
        Write-Host "Downloading $key..."
        Invoke-WebRequest -Uri $images[$key] -OutFile $filePath -UserAgent $userAgent
        Write-Host "Success: $key"
    } catch {
        Write-Host "Error downloading $key : $_"
        # Optional: Generate a fallback generic image if download fails?
    }
}
