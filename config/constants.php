<?php


$adminEmailsString = env('ADMIN_EMAILS', '');
$adminEmailsArray = explode(',', $adminEmailsString);
$adminEmailsArray = array_map('trim', $adminEmailsArray);

$paystackWhitelistedIp = env('PAYSTACK_WHITELISTED_IP', '');
$paystackWhitelistedIpsArray = explode(',', $paystackWhitelistedIp);
$paystackWhitelistedIpsArray = array_map('trim', $paystackWhitelistedIpsArray);

return [
    'ENVIRONMENT' => env('ENVIRONMENT', ''),
    'SECRET_KEY' => env('SECRET_KEY', ''),
    'REDIS_URL' => env('REDIS_URL', ''),
    'SENDER_EMAIL' => env('SENDER_EMAIL', ''),
    'SENDER_NAME' => env('SENDER_NAME', ''),
    'SENDGRID_API_KEY' => env('SENDGRID_API_KEY', ''),
    'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION', ''),
    'AWS_S3_BUCKET' => env('AWS_S3_BUCKET', ''),
    'AWS_ACCESS_KEY' => env('AWS_ACCESS_KEY', ''),
    'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY', ''),
    'EMAIL_VERIFICATION_TTL' => env('EMAIL_VERIFICATION_TTL', ''),
    'EMAIL_VERIFICATION_MAX_TRIALS' => env('EMAIL_VERIFICATION_MAX_TRIALS', ''),
    'PHONE_VERIFICATION_TTL' => env('PHONE_VERIFICATION_TTL', ''),
    'PHONE_VERIFICATION_MAX_TRIALS' => env('PHONE_VERIFICATION_MAX_TRIALS', ''),
    'TEST_OTP_STRING' => env('TEST_OTP_STRING', ''),
    'ACCESS_TOKEN_LIFETIME' => env('ACCESS_TOKEN_LIFETIME', ''),
    'REFRESH_TOKEN_LIFETIME' => env('REFRESH_TOKEN_LIFETIME', ''),
    'THROTTLE_RATE' => env('THROTTLE_RATE', ''),
    'THROTTLE_PERIOD' => env('THROTTLE_PERIOD', ''),
    'BASE_URL' => env('BASE_URL', ''),
    'PAYSTACK_SECRET_KEY' => env('PAYSTACK_SECRET_KEY', ''),
    'PAYSTACK_WHITELISTED_IP' => $paystackWhitelistedIpsArray,
    'ENABLED_IP_LOOKUP' => env('ENABLED_IP_LOOKUP', ''),
    'USE_S3' => env('USE_S3', ''),
    'GOOGLE_API_KEY' => env('GOOGLE_API_KEY', ''),
    'GOOGLE_SEARCH_LOCATION' => env('GOOGLE_SEARCH_LOCATION', ''),
    'GOOGLE_SEARCH_RADIUS' => env('GOOGLE_SEARCH_RADIUS', ''),
    'ONE_SIGNAL_KEY' => env('ONE_SIGNAL_KEY', ''),
    'ONE_SIGNAL_APP_ID' => env('ONE_SIGNAL_APP_ID', ''),
    'ONE_SIGNAL_SMS_FROM' => env('ONE_SIGNAL_SMS_FROM', ''),
    'TERMII_API_KEY' => env('TERMII_API_KEY', ''),
    'TERMII_SECRET_KEY' => env('TERMII_SECRET_KEY', ''),
    'TERMII_SMS_FROM' => env('TERMII_SMS_FROM', ''),
    'ALLOW_SERVER_SENT_EVENTS' => env('ALLOW_SERVER_SENT_EVENTS', ''),
    'ENCRYPTION_KEY' => env('ENCRYPTION_KEY', ''),
    'FELE_CHARGE' => env('FELE_CHARGE', ''),
    'ADMIN_EMAILS' => $adminEmailsArray,
];
