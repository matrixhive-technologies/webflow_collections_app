<?php

define('ENV', 'asd_local');

if (ENV == 'asd_local') {

    define('APP_URL', 'https://c8d6-2406-b400-d11-9862-265b-cfc3-5e88-de89.ngrok-free.app/projects/webflow_applications/webflow_collections_ui/dist/app/');
    
    define('HOME_PAGE_URL', 'https://c8d6-2406-b400-d11-9862-265b-cfc3-5e88-de89.ngrok-free.app/plp/cmsadmin/');
    
    define('CLIENT_ID', 'e6b54abd7bc636af7ccf60a0e10e52db35339d052cb90519b74eb40c87cedd50');
    
    define('CLIENT_SECRET', 'a59c3b80f172447569e69aa4cb7463fb92ce593b6303237bf0ab8e7ef2429d49');

    define('UPLOAD_PATH', 'https://c8d6-2406-b400-d11-9862-265b-cfc3-5e88-de89.ngrok-free.app/projects/webflow_collections_app/app/');
    // define('UPLOAD_PATH', 'https://3180-2402-a00-400-b707-f549-8b67-20eb-619a.ngrok-free.app/webflow_collections_app/app/');

    // define('UPLOAD_PATH', 'https://8511-2406-b400-d11-9862-36cb-f62e-2f4-d24.ngrok-free.app/plp/cmsadmin/app/');
}

if (ENV == 'rp_local') {

    define('APP_URL', 'http://localhost:5173/andrew/webflow_collections_app/app');
    define('HOME_PAGE_URL', 'http://localhost:5173/andrew/webflow_collections_app/');
    define('CLIENT_ID', '213dcd14e53bc8079da3b4863948bf715716dd051328198684922c013309ae69');
    define('CLIENT_SECRET', '34599d9aacd93a853e4e45f8d33ecf02ff47c6e395e645515a9ec43b26fc8a64');
    define('UPLOAD_PATH', 'https://webflow.pixlapi.com/plp/cmsadmin/app/');

}

if (ENV == 'prod') {
    define('APP_URL', '/app/');
    define('HOME_PAGE_URL', '/plp/cmsadmin/');
    # add the prod client id and secret
    define('CLIENT_ID', 'e6b54abd7bc636af7ccf60a0e10e52db35339d052cb90519b74eb40c87cedd50');
    define('CLIENT_SECRET', 'a59c3b80f172447569e69aa4cb7463fb92ce593b6303237bf0ab8e7ef2429d49');

    // define('UPLOAD_PATH', 'https://8511-2406-b400-d11-9862-36cb-f62e-2f4-d24.ngrok-free.app/projects/webflow_collections_app/app/');

    define('UPLOAD_PATH', 'https://webflow.pixlapi.com/plp/cmsadmin/app/');
}


# Common Config
# Application's Name
define('APP_NAME', 'Manage Collections Webflow App in PHP');
# Define the Scopes that your app needs here.
define('SCOPES', 'assets:read assets:write authorized_user:read cms:read cms:write custom_code:read custom_code:write forms:read forms:write pages:read pages:write sites:read sites:write ecommerce:read ecommerce:write');
# End-point to start the process of Authroization.
define('AUTHORIZATION_URL', 'https://webflow.com/oauth/authorize');
# Default Response Type.
define('RESPONSE_TYPE', 'code');

# Default Grant Type.
define('GRANT_TYPE', 'authorization_code');
