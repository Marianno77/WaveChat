#!/bin/bash

cat <<EOF > .env
APP_ENV=production
VITE_PUSHER_APP_KEY=${VITE_PUSHER_APP_KEY}
VITE_PUSHER_APP_CLUSTER=${VITE_PUSHER_APP_CLUSTER}
EOF

npm install
npm run build