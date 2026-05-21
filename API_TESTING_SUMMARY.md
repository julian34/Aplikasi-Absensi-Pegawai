# API Testing Summary - CORS Issue Deep Dive

## Initial Request

User requested "coba cek API" (test the API). This evolved into comprehensive CORS troubleshooting as login flow was blocked by browser CORS policy errors.

## What Works ✅

1. **Backend API fully functional** - All endpoints respond with HTTP 200 when tested via curl:
   - GET `/api/sanctum/csrf-cookie` - Returns CSRF token, sets session cookie
   - POST `/api/login` - Accepts email/NIP + password, authenticates user, establishes session
   - GET `/api/user` - Returns authenticated user with employment data
   - POST `/api/logout` - Destroys session

2. **Database authentication** - User lookup and password validation work correctly:
   - Supports dual login: email (Andimultimedia@papua.go.id) or NIP (19900101001)
   - Test password: papua1324
   - User found and authenticated successfully

3. **Frontend form** - Vue 3 LoginPage component:
   - Form renders without errors
   - Input validation works (login button enables when both fields filled)
   - Axios configured correctly with withCredentials: true for session cookies

4. **All Docker services healthy**:
   - finpoint-backend: Laravel 12 on port 8000
   - finpoint-db: MySQL 8.0 on port 3306
   - finpoint-web: Vue 3 frontend on port 5174
   - finpoint-pma: PHPMyAdmin on port 8082

## The Blocker ❌

### Problem: Duplicate CORS Headers

Browser error when frontend tries to make requests:

```
The 'Access-Control-Allow-Origin' header contains multiple values 'http://localhost:5174, *',
but only one is allowed.
```

### Root Cause

API responses contain TWO values for `Access-Control-Allow-Origin`:

- `Access-Control-Allow-Origin: http://localhost:5174` (correct)
- `Access-Control-Allow-Origin: *` (unwanted wildcard)

When a request has `credentials: include` (withCredentials: true), browsers REQUIRE exactly ONE specific origin, NOT a wildcard.

### Source: Unknown

Despite extensive investigation, the source of the wildcard header could not be identified:

- **Not from application code** - No header() calls in routes, controllers, or application middleware
- **Not from Sanctum middleware** - Package source code searched, no CORS handling found
- **Not from user middleware** - Tested both EarlyCorsinit (prepend) and FinalCorsCleanup (append) middlewares with multiple removal approaches - wildcard persists
- **Likely from**: Laravel framework internals, Symfony HttpFoundation, PHP configuration, or php artisan serve development server

## Solutions Attempted (13 different approaches)

1. ✗ EarlyCorsinit middleware (prepend to middleware stack)
2. ✗ LateCors middleware (append to middleware stack)
3. ✗ Native PHP header() and header_remove() functions
4. ✗ Symfony Response->headers->set() methods
5. ✗ Combining multiple approaches simultaneously
6. ✗ Testing with only one middleware active (FinalCorsCleanup alone still shows both headers)
7. ✗ Searching for CORS handling in vendor code
8. ✗ Creating ResponseMacroServiceProvider with event listeners
9. ✗ Using Symfony HeaderBag's remove() method with getAllHeaders() checking
10. ✗ Native header() with replace=true flag
11. ✗ Response object and native PHP methods combined
12. ✗ Middleware ordering permutations
13. ✗ Installing fruitcake/laravel-cors package (blocked by SSL certificate errors)

## Recommended Solutions

### Option 1: Use JWT-Based Authentication (Recommended)

- Switch from session-based to token-based auth
- Tokens sent in Authorization header, not cookies
- Wildcard CORS header is acceptable without credentials mode
- **Trade-off**: More code changes, but cleaner architecture for APIs

### Option 2: Reverse Proxy Solution

- Use Nginx or Caddy in Docker to intercept and filter CORS headers
- Remove wildcard header at proxy level before reaching client
- **Trade-off**: Additional infrastructure, needs Docker Compose update

### Option 3: Production Server

- Replace `php artisan serve` with production server (Nginx/Apache) in Docker
- May not have the same CORS header behavior
- **Trade-off**: More Docker configuration, but more realistic environment

### Option 4: Offline CORS Package Installation

- Build Laravel CORS package dependency tree locally
- Add vendor directory to Docker image (offline installation)
- May have best compatibility with session-based auth
- **Trade-off**: Manual build process, larger Docker image

### Option 5: Browser API Alternative

- Switch from Axios to fetch API with different CORS handling
- **Trade-off**: Unlikely to help, browser CORS rules are consistent

## Technical Details

### Current Architecture

- **Backend**: Laravel 12.2.4 + Sanctum 4.3.2 (session-based authentication)
- **Frontend**: Vue 3.4.0 + Axios 1.6.0 (configured with withCredentials: true)
- **Database**: MySQL 8.0 with session and cache tables
- **Environment**: Docker Compose with 4 services

### CORS Requirements for Session-Based Auth

Session cookies + credentials mode requires:

```
Access-Control-Allow-Origin: http://localhost:5174 (specific origin only)
Access-Control-Allow-Credentials: true
Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-TOKEN
```

❌ **NOT allowed**: `Access-Control-Allow-Origin: *` when credentials are used

### Git Commits

- `f1300cd`: "fix: implement CORS headers with specific origin - wildcard conflict remains"
- `67ec0a4`: "fix: add FinalCorsCleanup middleware - wildcard CORS header persists despite multiple approaches"

## Next Steps

1. Investigate further if time permits using Option 2 or 4
2. Document this CORS issue for team reference
3. Consider switching to JWT-based auth for cleaner API design
4. Set up production Nginx environment instead of php artisan serve

## Verification Steps When Issue Is Resolved

Once CORS headers are fixed, test login flow:

```
1. Browser: http://localhost:5174/login
2. Fill form: Andimultimedia@papua.go.id / papua1324
3. Click Login
4. Should redirect to /dashboard with user data displayed
```

---

**Status**: BLOCKED at CSRF token fetch due to CORS error. Backend API is fully functional.
