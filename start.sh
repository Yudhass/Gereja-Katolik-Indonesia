#!/bin/bash
echo "Starting PHP Development Server..."
echo "Server running at http://localhost:8000"
echo "Press Ctrl+C to stop the server"
php -S localhost:8000 -t public
