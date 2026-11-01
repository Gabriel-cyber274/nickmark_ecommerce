@echo off
set DIR="D:\Desktop-projects\nickmark-ecommerce\app\Filament\Resources\GeneralSettings"
takeown /F %DIR% /R /Y 2>nul
icacls %DIR% /grant %USERNAME%:F /T /Q 2>nul
rmdir /S /Q %DIR%
echo Done