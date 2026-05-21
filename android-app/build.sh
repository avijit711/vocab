#!/bin/bash
set -e

echo "==> Building VocabMaster Android App"
echo ""

# 1. Generate production CSS
echo "==> 1/4 Building Tailwind CSS..."
npx tailwindcss -i www/css/input.css -o www/css/app.css --minify

# 2. Copy web assets to Android
echo "==> 2/4 Syncing web assets..."
npx cap sync

# 3. Build Android APK (debug)
echo "==> 3/4 Building debug APK..."
cd android
./gradlew assembleDebug

echo ""
echo "==> Build complete! APK location:"
echo "    android/app/build/outputs/apk/debug/app-debug.apk"
echo ""
echo "==> To build a release APK/AAB (Play Store):"
echo "    1. Generate a keystore:"
echo "       keytool -genkey -v -keystore vocabmaster.keystore -alias vocabmaster -keyalg RSA -keysize 2048 -validity 10000"
echo "    2. Set env vars:"
echo "       export KEYSTORE_PASSWORD=your_password"
echo "       export KEY_PASSWORD=your_password"
echo "    3. Run release build:"
echo "       cd android && ./gradlew assembleRelease"
echo "       # Output: android/app/build/outputs/apk/release/app-release.apk"
echo ""
echo "    Or for Android App Bundle (Play Store):"
echo "       cd android && ./gradlew bundleRelease"
echo "       # Output: android/app/build/outputs/bundle/release/app-release.aab"
