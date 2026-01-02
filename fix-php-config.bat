@echo off
echo ========================================
echo PHP Configuration Fixer
echo ========================================
echo.

set PHP_INI=C:\Users\FA\.config\herd\bin\php84\php.ini
set TEMP_DIR=D:\MyProjects\scarf-ecommerce-app\scarf-api\storage\app\temp_uploads

echo Checking PHP configuration...
echo.

if not exist "%PHP_INI%" (
    echo ERROR: PHP ini file not found at: %PHP_INI%
    echo Please check the path and try again.
    pause
    exit /b 1
)

echo PHP ini file found: %PHP_INI%
echo.

echo Creating temp directory if it doesn't exist...
if not exist "%TEMP_DIR%" (
    mkdir "%TEMP_DIR%"
    echo Temp directory created: %TEMP_DIR%
) else (
    echo Temp directory already exists: %TEMP_DIR%
)
echo.

echo Opening php.ini in notepad...
echo.
echo Please add or update these lines in the php.ini file:
echo.
echo upload_tmp_dir = "%TEMP_DIR%"
echo upload_max_filesize = 10M
echo post_max_size = 12M
echo.
echo After saving, restart your PHP server (Laravel Herd).
echo.
pause

notepad "%PHP_INI%"

echo.
echo ========================================
echo Configuration file opened.
echo.
echo After editing and saving:
echo 1. Close this window
echo 2. Restart Laravel Herd
echo 3. Try uploading an image again
echo ========================================
pause

