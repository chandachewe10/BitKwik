# Bit2Kwacha Mobile App

React Native mobile application for Bit2Kwacha Bitcoin exchange platform.

## Features

- **Buy Bitcoin**: Convert Kwacha (ZMW) to Bitcoin
- **Sell Bitcoin**: Convert Bitcoin to Kwacha via Lightning Network
- **Live Exchange Rates**: Real-time BTC/ZMW rates from OpenNode
- **Balance Checking**: Verify account balance before transactions
- **QR Code Generation**: Lightning invoice QR codes for payments

## Prerequisites

- Node.js (v16 or higher)
- npm or yarn
- Expo CLI (`npm install -g expo-cli`)
- iOS Simulator (for Mac) or Android Studio (for Android development)

## Installation

1. Install dependencies:
```bash
npm install
```

2. Configure API endpoint:
   - Open `src/config/api.js`
   - Update `API_BASE_URL` with your Laravel backend URL

3. Start the development server:
```bash
npm start
```

4. Run on device/simulator:
   - Press `i` for iOS simulator
   - Press `a` for Android emulator
   - Scan QR code with Expo Go app on your physical device

## Project Structure

```
MobileApp/
├── src/
│   ├── config/
│   │   ├── api.js          # API configuration
│   │   └── theme.js        # App theme and colors
│   ├── screens/
│   │   ├── BuyScreen.js    # Buy Bitcoin screen
│   │   └── SellScreen.js   # Sell Bitcoin screen
│   └── services/
│       └── exchangeService.js  # API service functions
├── App.js                  # Main app component
├── app.json               # Expo configuration
└── package.json           # Dependencies
```

## API Integration

The app connects to your Laravel backend using the following endpoints:

- `GET /api/exchange-rates` - Fetch live exchange rates
- `POST /api/check-balance` - Check account balance
- `POST /generate-invoice` - Generate Lightning invoice
- `POST /complete-subscription` - Complete Bitcoin purchase

## Building for Production

### iOS
```bash
expo build:ios
```

### Android
```bash
expo build:android
```

## Notes

- Make sure your Laravel backend CORS is configured to allow requests from the mobile app
- Update the API base URL in production builds
- Consider adding authentication if needed
- Test on both iOS and Android devices

## License

Same as main Bit2Kwacha project.

