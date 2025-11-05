#!/bin/bash

# Script để start Laravel và Ngrok cùng lúc
# Usage: ./start-ngrok.sh

echo "🚀 Starting Laravel server on port 8050..."
php artisan serve --port=8050 &
LARAVEL_PID=$!

echo "⏳ Waiting for Laravel to start..."
sleep 3

echo "🌐 Starting Ngrok tunnel..."
echo "📋 Ngrok URL will be displayed below:"
echo "⚠️  Don't forget to update APP_URL in .env file with the ngrok URL!"
echo ""

# Start ngrok
ngrok http 8050

# Cleanup khi dừng script (Ctrl+C)
trap "kill $LARAVEL_PID 2>/dev/null; exit" INT TERM

