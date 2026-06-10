# Registration Troubleshooting Guide

## Issue: Registration Not Working

### Quick Fix

The registration has been updated with better error handling. Follow these steps:

1. **Clear Browser Cache**
   - Press `Ctrl + Shift + Delete`
   - Clear cached images and files
   - Or do a hard refresh: `Ctrl + F5`

2. **Open Browser Console**
   - Press `F12` to open Developer Tools
   - Go to "Console" tab
   - Try registering again
   - Look for error messages

3. **Check What You'll See**

   You should see console logs like:
   ```
   Registration form submitted
   Selected roles: ['farmer'] or ['farmer', 'buyer']
   Sending registration request...
   Response status: 200
   Response text: {"success":true,"redirect":"verify_phone.php",...}
   Parsed data: {success: true, redirect: "verify_phone.php", ...}
   Redirecting to: verify_phone.php
   ```

4. **Common Errors & Solutions**

   **Error: "Please select at least one role"**
   - Solution: Check at least one checkbox (Farmer or Buyer)

   **Error: "Username or Phone Number already taken"**
   - Solution: Use a different username or phone number

   **Error: "Server returned invalid response"**
   - Check console for actual response
   - Likely a PHP error - check Apache error logs

   **Error: "Network error: Failed to fetch"**
   - Make sure XAMPP Apache is running
   - Check the URL is correct: `http://localhost/naos/auth.php`

## Testing Registration

### Test Case 1: Single Role Registration

1. Go to: `http://localhost/naos/auth.php`
2. Click "Register here"
3. Fill in:
   - Username: `testfarmer1`
   - Password: `password123`
   - Phone: `0991234567`
   - **Check only "Farmer"**
   - Gender: Male
   - Location: Blantyre
   - Language: English
4. Click "Register"
5. ✅ Should redirect to phone verification

### Test Case 2: Dual Role Registration

1. Fill in:
   - Username: `testdual1`
   - Password: `password123`
   - Phone: `0991234568`
   - **Check BOTH "Farmer" AND "Buyer"**
   - Gender: Female
   - Location: Lilongwe
   - Language: English
2. Click "Register"
3. ✅ Should redirect to phone verification

## Verify Registration Worked

### Check Database

```sql
-- Check user created
SELECT id, username, role, phone_number FROM users 
WHERE username = 'testfarmer1';

-- If migrations were run, check user_roles
SELECT u.username, ur.role_type, ur.is_primary
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
WHERE u.username = 'testdual1';
```

## Important Notes

1. **Migrations Not Required**: Registration now works BEFORE running migrations
   - Pre-migration: Uses old single-role system
   - Post-migration: Uses new multi-role system

2. **WebSocket Error is Normal**: The `ws://127.0.0.1:5500//ws` error is from Live Server extension and can be ignored

3. **Phone Verification**: After registration, you'll be redirected to verify your phone number

## Still Not Working?

If registration still doesn't work after following above steps:

1. **Check Apache Error Log**
   - Location: `c:\xampp\apache\logs\error.log`
   - Look for recent PHP errors

2. **Check PHP Errors**
   - Open: `c:\xampp\htdocs\naos\auth\register_process.php`
   - Add at top: `error_reporting(E_ALL); ini_set('display_errors', 1);`

3. **Test API Directly**
   ```bash
   curl -X POST http://localhost/naos/auth/register_process.php \
     -H "Content-Type: application/json" \
     -d '{"username":"testapi","password":"test123","roles":["farmer"],"gender":"male","phone":"0991111111","location":"Test","lang":"en"}'
   ```

4. **Share Console Output**
   - Copy all console messages
   - Share with developer for debugging
