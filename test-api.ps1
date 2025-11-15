# Abandoned Arcade - API Test Script
# This script helps you quickly test the API endpoints

Write-Host "=== Abandoned Arcade API Tester ===" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"

# Test 1: Check if server is running
Write-Host "1. Testing server connection..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/games" -Method Get -ErrorAction Stop
    Write-Host "   ✓ Server is running!" -ForegroundColor Green
    Write-Host "   Games found: $($response.data.Count)" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Server is not running or not accessible" -ForegroundColor Red
    Write-Host "   Make sure to run: php artisan serve" -ForegroundColor Yellow
    exit
}

Write-Host ""

# Test 2: Register a test user
Write-Host "2. Registering test user..." -ForegroundColor Yellow
$registerBody = @{
    name = "Test User"
    email = "test@example.com"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-RestMethod -Uri "$baseUrl/register" -Method Post -Body $registerBody -ContentType "application/json"
    Write-Host "   ✓ User registered successfully!" -ForegroundColor Green
    Write-Host "   User ID: $($registerResponse.user.id)" -ForegroundColor Gray
    Write-Host "   Email: $($registerResponse.user.email)" -ForegroundColor Gray
    $token = $registerResponse.token
    Write-Host "   Token received: $($token.Substring(0, 20))..." -ForegroundColor Gray
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    if ($statusCode -eq 422) {
        Write-Host "   ⚠ User already exists, trying to login..." -ForegroundColor Yellow
        
        # Try to login instead
        $loginBody = @{
            email = "test@example.com"
            password = "password123"
        } | ConvertTo-Json
        
        try {
            $loginResponse = Invoke-RestMethod -Uri "$baseUrl/login" -Method Post -Body $loginBody -ContentType "application/json"
            $token = $loginResponse.token
            Write-Host "   ✓ Login successful!" -ForegroundColor Green
            Write-Host "   Token received: $($token.Substring(0, 20))..." -ForegroundColor Gray
        } catch {
            Write-Host "   ✗ Login failed: $($_.Exception.Message)" -ForegroundColor Red
            exit
        }
    } else {
        Write-Host "   ✗ Registration failed: $($_.Exception.Message)" -ForegroundColor Red
        exit
    }
}

Write-Host ""

# Test 3: Get authenticated user
Write-Host "3. Testing authenticated endpoint..." -ForegroundColor Yellow
$headers = @{
    Authorization = "Bearer $token"
}

try {
    $userResponse = Invoke-RestMethod -Uri "$baseUrl/user" -Method Get -Headers $headers
    Write-Host "   ✓ Authentication works!" -ForegroundColor Green
    Write-Host "   Authenticated as: $($userResponse.name) ($($userResponse.email))" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Authentication failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== All Tests Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Your API token (save this for testing):" -ForegroundColor Yellow
Write-Host $token -ForegroundColor White
Write-Host ""
Write-Host "Use this token in your API requests:" -ForegroundColor Yellow
Write-Host "Authorization: Bearer $token" -ForegroundColor White
Write-Host ""

