
<?php

require_once __DIR__ . '/../env.php';

// ========================================
// VERIFIKASI OTP UNTUK RESET PASSWORD
// ========================================

function supabase_verify_otp(
    string $email,
    string $token
): array {

    return supabase_request(
        '/auth/v1/verify',
        'POST',
        [
            'email' => $email,
            'token' => $token,
            'type' => 'recovery'
        ]
    );
}
