
<?php

require_once __DIR__ . '/supabase.php';

function supabase_request($endpoint, $method = 'GET', $data = null, $accessToken = null)
{
    $url = rtrim(SUPABASE_URL, '/') . $endpoint;

    $headers = [
        'apikey: ' . SUPABASE_PUBLISHABLE_KEY,
        'Content-Type: application/json'
    ];

    if ($accessToken) {
        $headers[] = 'Authorization: Bearer ' . $accessToken;
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20
    ]);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status' => 0,
            'data' => null,
            'error' => $error
        ];
    }

    curl_close($ch);

    return [
        'status' => $httpCode,
        'data' => json_decode($response, true),
        'error' => null
    ];
}

// ========================================
// LOGIN MENGGUNAKAN EMAIL DAN PASSWORD
// ========================================

function supabase_login($email, $password)
{
    return supabase_request(
        '/auth/v1/token?grant_type=password',
        'POST',
        [
            'email' => $email,
            'password' => $password
        ]
    );
}

// ========================================
// DAFTAR AKUN BARU KE SUPABASE AUTH
// ========================================

function supabase_signup($nama, $email, $password)
{
    return supabase_request(
        '/auth/v1/signup',
        'POST',
        [
            'email' => $email,
            'password' => $password,
            'data' => [
                'full_name' => $nama
            ]
        ]
    );
}

// ========================================
// MENGAMBIL INFORMASI PENGGUNA BERDASARKAN JWT
// ========================================

function supabase_get_user($accessToken)
{
    return supabase_request(
        '/auth/v1/user',
        'GET',
        null,
        $accessToken
    );
}

// ========================================
// MEMPERBARUI ACCESS TOKEN
// ========================================

function supabase_refresh_token($refreshToken)
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
// GOOGLE OAUTH - MEMBUAT URL LOGIN
// ========================================

function supabase_google_authorize_url(string $redirectTo): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Membuat PKCE verifier
    $verifier = rtrim(
        strtr(base64_encode(random_bytes(64)), '+/', '-_'),
        '='
    );

    // Membuat PKCE challenge
    $challenge = rtrim(
        strtr(
            base64_encode(hash('sha256', $verifier, true)),
            '+/',
            '-_'
        ),
        '='
    );

    // Mencegah CSRF dengan state
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
// GOOGLE OAUTH - MENUKAR AUTH CODE
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
