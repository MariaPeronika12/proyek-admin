
<?php

require_once __DIR__ . '/supabase.php';

// ========================================
// KONEKSI UTAMA KE SUPABASE
// ========================================

function supabase_request(
    $endpoint,
    $method = 'GET',
    $data = null,
    $accessToken = null
): array {

    if (!function_exists('curl_init')) {
        return [
            'status' => 0,
            'data' => null,
            'error' => 'Ekstensi PHP cURL belum aktif.'
        ];
    }

    if (
        !defined('SUPABASE_URL') ||
        !defined('SUPABASE_PUBLISHABLE_KEY') ||
        empty(SUPABASE_URL) ||
        empty(SUPABASE_PUBLISHABLE_KEY)
    ) {
        return [
            'status' => 0,
            'data' => null,
            'error' => 'Konfigurasi Supabase URL atau Publishable Key belum benar.'
        ];
    }

    $url = rtrim(SUPABASE_URL, '/') . $endpoint;

    $headers = [
        'apikey: ' . SUPABASE_PUBLISHABLE_KEY,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    if (!empty($accessToken)) {
        $headers[] = 'Authorization: Bearer ' . $accessToken;
    }

    $ch = curl_init();

    if ($ch === false) {
        return [
            'status' => 0,
            'data' => null,
            'error' => 'Gagal membuat koneksi cURL.'
        ];
    }

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
    ];

    if ($data !== null) {
        $jsonData = json_encode($data);

        if ($jsonData === false) {
            curl_close($ch);

            return [
                'status' => 0,
                'data' => null,
                'error' => 'Gagal mengubah data menjadi JSON.'
            ];
        }

        $options[CURLOPT_POSTFIELDS] = $jsonData;
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);

    $httpCode = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    // DETAIL ERROR KONEKSI
    if ($response === false) {
        $error = curl_error($ch);
        $errorNumber = curl_errno($ch);

        curl_close($ch);

        return [
            'status' => 0,
            'data' => null,
            'error' => 'cURL Error ' . $errorNumber . ': ' . $error
        ];
    }

    curl_close($ch);

    $decoded = json_decode($response, true);

    return [
        'status' => $httpCode,
        'data' => $decoded,
        'error' => null
    ];
}


// ========================================
// LOGIN EMAIL DAN PASSWORD
// ========================================

function supabase_login($email, $password): array
{
    return supabase_request(
        '/auth/v1/token?grant_type=password',
        'POST',
        [
            'email' => strtolower(trim($email)),
            'password' => $password
        ]
    );
}


// ========================================
// REGISTER AKUN SUPABASE
// ========================================

function supabase_signup(
    $nama,
    $email,
    $password
): array {

    return supabase_request(
        '/auth/v1/signup',
        'POST',
        [
            'email' => strtolower(trim($email)),
            'password' => $password,
            'data' => [
                'full_name' => $nama
            ]
        ]
    );
}


// ========================================
// MENGAMBIL DATA USER
// ========================================

function supabase_get_user($accessToken): array
{
    return supabase_request(
        '/auth/v1/user',
        'GET',
        null,
        $accessToken
    );
}


// ========================================
// REFRESH ACCESS TOKEN
// ========================================

function supabase_refresh_token($refreshToken): array
{
    return supabase_request(
        '/auth/v1/token?grant_type=refresh_token',
        'POST',
        [
            'refresh_token' => $refreshToken
        ]
    );
}


// ========================================
// CEK ANGGOTA BERDASARKAN EMAIL
// ========================================

function supabase_find_regis_by_email(
    string $email
): array {

    $email = strtolower(trim($email));

    $endpoint =
        '/rest/v1/regis?select=id,user_id,nama,email,role' .
        '&email=eq.' . rawurlencode($email);

    return supabase_request(
        $endpoint,
        'GET'
    );
}


// ========================================
// MENYIMPAN DATA ANGGOTA
// ========================================

function supabase_insert_regis(
    string $userId,
    string $nama,
    string $email,
    string $role = 'anggota'
): array {

    return supabase_request(
        '/rest/v1/regis',
        'POST',
        [
            'user_id' => $userId,
            'nama' => $nama,
            'email' => strtolower(trim($email)),
            'role' => $role
        ]
    );
}


// ========================================
// GOOGLE OAUTH - URL LOGIN
// ========================================

function supabase_google_authorize_url(
    string $redirectTo
): string {

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $verifier = rtrim(
        strtr(base64_encode(random_bytes(64)), '+/', '-_'),
        '='
    );

    $challenge = rtrim(
        strtr(
            base64_encode(
                hash('sha256', $verifier, true)
            ),
            '+/',
            '-_'
        ),
        '='
    );

    $state = bin2hex(random_bytes(32));

    $_SESSION['supabase_pkce_verifier'] = $verifier;
    $_SESSION['supabase_oauth_state'] = $state;

    $params = [
        'provider' => 'google',
        'redirect_to' => $redirectTo,
        'code_challenge' => $challenge,
        'code_challenge_method' => 's256',
        'state' => $state,
        'apikey' => SUPABASE_PUBLISHABLE_KEY
    ];

    return rtrim(SUPABASE_URL, '/') .
        '/auth/v1/authorize?' .
        http_build_query($params);
}


// ========================================
// GOOGLE OAUTH - EXCHANGE AUTH CODE
// ========================================

function supabase_google_exchange_code(
    string $authCode,
    string $verifier
): array {

    return supabase_request(
        '/auth/v1/token?grant_type=pkce',
        'POST',
        [
            'auth_code' => $authCode,
            'code_verifier' => $verifier
        ]
    );
}


// ========================================
// FORGOT PASSWORD - KIRIM EMAIL RECOVERY
// ========================================

function supabase_forgot_password(
    string $email,
    string $redirectTo
): array {

    return supabase_request(
        '/auth/v1/recover',
        'POST',
        [
            'email' => strtolower(trim($email)),
            'redirect_to' => $redirectTo
        ]
    );
}


// ========================================
// KIRIM OTP RECOVERY PASSWORD
// ========================================

function supabase_send_recovery_otp(
    string $email,
    string $redirectTo = ''
): array {

    return supabase_forgot_password(
        $email,
        $redirectTo
    );
}


// ========================================
// VERIFIKASI OTP RECOVERY
// ========================================

function supabase_verify_recovery_otp(
    string $email,
    string $otp
): array {

    return supabase_request(
        '/auth/v1/verify',
        'POST',
        [
            'email' => strtolower(trim($email)),
            'token' => trim($otp),
            'type' => 'recovery'
        ]
    );
}


// ========================================
// KIRIM OTP EMAIL BIASA
// ========================================

function supabase_send_otp(
    string $email
): array {

    return supabase_request(
        '/auth/v1/otp',
        'POST',
        [
            'email' => strtolower(trim($email)),
            'create_user' => false
        ]
    );
}


// ========================================
// UPDATE PASSWORD PENGGUNA
// ========================================

function supabase_update_password(
    string $accessToken,
    string $password
): array {

    return supabase_request(
        '/auth/v1/user',
        'PUT',
        [
            'password' => $password
        ],
        $accessToken
    );
}
