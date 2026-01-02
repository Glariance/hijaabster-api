# Fix PHP Upload Temp Directory Issue

## Problem
PHP is trying to use `C:\Windows` as the temp directory, which is not writable. This causes file uploads to fail.

## Solution

### Option 1: Edit php.ini (Recommended)

1. Find your `php.ini` file:
   - Run: `php --ini` in terminal
   - Or check: `http://localhost:8000/upload-fix-guide.php`

2. Open `php.ini` in a text editor (as Administrator)

3. Find and update these lines:
   ```ini
   upload_tmp_dir = "D:\MyProjects\scarf-ecommerce-app\scarf-api\storage\app\temp_uploads"
   upload_max_filesize = 10M
   post_max_size = 12M
   ```

4. **Restart your PHP server** (Laravel dev server, XAMPP, WAMP, etc.)

### Option 2: Create php.ini in project root

Create a file named `php.ini` in your project root (`D:\MyProjects\scarf-ecommerce-app\scarf-api\`) with:
```ini
upload_tmp_dir = "D:\MyProjects\scarf-ecommerce-app\scarf-api\storage\app\temp_uploads"
upload_max_filesize = 10M
post_max_size = 12M
```

Then restart your server.

### Option 3: Set Environment Variable (Windows)

1. Open System Properties → Environment Variables
2. Add new System Variable:
   - Name: `TMP`
   - Value: `D:\MyProjects\scarf-ecommerce-app\scarf-api\storage\app\temp_uploads`
3. Restart your server

## Verify Fix

1. Visit: `http://localhost:8000/upload-fix-guide.php`
2. Check that temp directory is writable
3. Try uploading an image in the CMS

## Important Notes

- The temp directory must exist and be writable
- You MUST restart your PHP server after changing php.ini
- If using XAMPP/WAMP, make sure you're editing the correct php.ini (web server's php.ini, not CLI's)

