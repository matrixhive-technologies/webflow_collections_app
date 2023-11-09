<?php

# Application's Name
define('APP_NAME', 'Manage Collections Webflow App in PHP');

# Your application's root URL.
// define('APP_URL', 'https://0d0e-2406-b400-d11-9862-8895-a8a4-f5a7-d2d2.ngrok-free.app/plp/cmsadmin/app/');

define('APP_URL', 'https://0d0e-2406-b400-d11-9862-8895-a8a4-f5a7-d2d2.ngrok-free.app/projects/webflow_applications/webflow_collections_ui/dist/app/');

define('HOME_PAGE_URL', 'https://0d0e-2406-b400-d11-9862-8895-a8a4-f5a7-d2d2.ngrok-free.app/plp/cmsadmin/');

# Your application's Client ID.
define('CLIENT_ID', 'e6b54abd7bc636af7ccf60a0e10e52db35339d052cb90519b74eb40c87cedd50');

# Your application's Client Secret.
define('CLIENT_SECRET', 'a59c3b80f172447569e69aa4cb7463fb92ce593b6303237bf0ab8e7ef2429d49');

# Define the Scopes that your app needs here.
define('SCOPES', 'assets:read assets:write authorized_user:read cms:read cms:write custom_code:read custom_code:write forms:read forms:write pages:read pages:write sites:read sites:write ecommerce:read ecommerce:write');

# End-point to start the process of Authroization.
define('AUTHORIZATION_URL', 'https://webflow.com/oauth/authorize');

# Default Response Type.
define('RESPONSE_TYPE', 'code');

# Default Grant Type.
define('GRANT_TYPE', 'authorization_code');
