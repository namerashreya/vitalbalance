$images = @{
    "ginger.jpg" = "https://images.unsplash.com/photo-1615485925763-8678628b0780?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "turmeric_milk.jpg" = "https://images.unsplash.com/photo-1615485400332-8b010b9f9136?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "salt_water.jpg" = "https://images.unsplash.com/photo-1560060143-1f630560d2bb?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "aloe.jpg" = "https://images.unsplash.com/photo-1596525983790-2e90c2538186?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "peppermint.jpg" = "https://images.unsplash.com/photo-1629230574889-ebba47161c56?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "steam.jpg" = "https://images.unsplash.com/photo-1555883006-0f5a0915a80f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "acv.jpg" = "https://images.unsplash.com/photo-1566807810030-3eaa60f3e670?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "chamomile.jpg" = "https://images.unsplash.com/photo-1599388102325-15a0c10fd4e4?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "garlic.jpg" = "https://images.unsplash.com/photo-1621251390317-063f2560868a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "lavender.jpg" = "https://images.unsplash.com/photo-1595855727026-628d0865d487?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "oatmeal.jpg" = "https://images.unsplash.com/photo-1586520108960-59f5647565b9?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "fennel.jpg" = "https://images.unsplash.com/photo-1615484477778-ca3b77940c25?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "epsom_salt.jpg" = "https://images.unsplash.com/photo-1627964435677-9db6984e8701?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "tea_tree.jpg" = "https://images.unsplash.com/photo-1618331835717-801e976710b2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "hibiscus.jpg" = "https://images.unsplash.com/photo-1596813200922-85098e6027a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "tulsi.jpg" = "https://images.unsplash.com/photo-1628088062854-d1870b4553da?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
    "coconut_oil.jpg" = "https://images.unsplash.com/photo-1580137576563-356c36c1e956?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
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
        # Fallback to Wikimedia or similar if needed, or placeholders
    }
}
