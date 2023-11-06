<?php

# Application's Name
define('APP_NAME', 'Manage Collections Webflow App in PHP');

# Your application's root URL.
define('APP_URL', 'https://c33d-2406-b400-d5-737e-8c1f-b07-99f1-504.ngrok-free.app/projects/webflow_applications/manage_collections_app/');

# Your application's Client ID.
define('CLIENT_ID', 'b88841e59e121c91362a995b83f20924c030ea7f64d17e88eedd6a4eb42162cb');

# Your application's Client Secret.
define('CLIENT_SECRET', 'f392f5555955b22ff8fbdbc9aa75e80a24b24b77d2f0a09b12f92e265b815079');

# Define the Scopes that your app needs here.
define('SCOPES', 'assets:read assets:write authorized_user:read cms:read cms:write custom_code:read custom_code:write forms:read forms:write pages:read pages:write sites:read sites:write');

# End-point to start the process of Authroization.
define('AUTHORIZATION_URL', 'https://webflow.pixlapi.com/plp/cmsadmin/app/authorize.php');

# Default Response Type.
define('RESPONSE_TYPE', 'code');

# Default Grant Type.
define('GRANT_TYPE', 'authorization_code');
