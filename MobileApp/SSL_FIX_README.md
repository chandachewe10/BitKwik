# Fixing SSL Certificate Error on Android

## Problem
Android is rejecting ngrok's SSL certificate with error:
```
java.security.cert.CertPathValidatorException: Trust anchor for certification path not found.
```

## Solution

### Option 1: Install expo-build-properties and Rebuild (Recommended)

1. **Install the plugin:**
   ```bash
   cd MobileApp
   npm install expo-build-properties --save-dev
   ```

2. **Clear cache and rebuild:**
   ```bash
   npx expo prebuild --clean
   npx expo run:android
   ```

   Or if using Expo Go:
   ```bash
   npx expo start --clear
   ```

### Option 2: Use HTTP instead of HTTPS (Development Only)

If ngrok supports HTTP, you can temporarily change the API URL in `src/config/api.js`:
```javascript
const API_BASE_URL = 'http://5458ca2b9366.ngrok-free.app'; // Use HTTP instead
```

**Note:** This is only for development. Never use HTTP in production.

### Option 3: Install ngrok Certificate on Device

1. Download ngrok's root certificate from: https://ngrok.com/download
2. Install it on your Android device/emulator
3. Trust the certificate in Android settings

### Option 4: Use ngrok with Reserved Domain (Paid)

ngrok paid plans allow you to use a reserved domain with a proper SSL certificate that Android will trust.

## Current Configuration

The app is already configured with:
- `expo-build-properties` plugin in `app.json`
- `usesCleartextTraffic: true` for Android
- Network security config to allow user certificates

After installing the plugin and rebuilding, the SSL certificate should be trusted.

