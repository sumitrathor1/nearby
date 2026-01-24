<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

$respond = static function (int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload);
    exit;
};

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user']['id'])) {
        $respond(401, ['success' => false, 'message' => 'Login required']);
    }

    $userId = (int) $_SESSION['user']['id'];
    
    // Rate limiting for chatbot messages
    if (!checkRateLimit('chatbot_' . $userId, 30, 300)) { // 30 messages per 5 minutes
        $respond(429, ['success' => false, 'message' => 'Too many messages. Please wait before sending another message.']);
    }

    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = [];
    }

    // Validate and sanitize message
    $messageValidation = validateText($payload['message'] ?? '', 1, 1000);
    if (!$messageValidation['valid']) {
        $respond(422, ['success' => false, 'message' => $messageValidation['error']]);
    }
    $message = $messageValidation['text'];

    // Additional security: Check for potential prompt injection
    $suspiciousPatterns = [
        '/ignore\s+previous\s+instructions/i',
        '/system\s*:/i',
        '/assistant\s*:/i',
        '/\[INST\]/i',
        '/\<\|system\|\>/i',
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $message)) {
            $respond(422, ['success' => false, 'message' => 'Message contains invalid content']);
        }
    }

    $conn = nearby_db_connect();

    $historySql = 'SELECT sender, message FROM chatbot_messages WHERE user_id = ? ORDER BY created_at DESC LIMIT 9';
    $historyStmt = mysqli_prepare($conn, $historySql);
    if ($historyStmt === false) {
        $respond(500, ['success' => false, 'message' => 'Failed to prepare chat history lookup']);
    }

    mysqli_stmt_bind_param($historyStmt, 'i', $userId);
    mysqli_stmt_execute($historyStmt);
    $historyResult = mysqli_stmt_get_result($historyStmt);

    $history = [];
    while ($row = mysqli_fetch_assoc($historyResult)) {
        $history[] = [
            'sender' => $row['sender'],
            'message' => sanitizeInput($row['message'] ?? ''),
        ];
    }
    mysqli_stmt_close($historyStmt);

    $history = array_reverse($history);
    $contents = [];
    foreach ($history as $item) {
        $role = $item['sender'] === 'user' ? 'user' : 'model';
        $contents[] = [
            'role' => $role,
            'parts' => [['text' => $item['message']]],
        ];
    }

    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => $message]],
    ];

    $apiKey = GEMINI_API_KEY;
    if (!$apiKey) {
        $respond(500, ['success' => false, 'message' => 'Gemini API key is not configured']);
    }

    $requestPayload = json_encode([
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 300,
        ],
    ]);

    $preferredModels = array_values(array_unique(array_filter([
        GEMINI_MODEL,
        'models/gemini-2.0-flash-exp',
        'models/gemini-1.5-flash',
        'models/gemini-1.0-pro',
        'models/gemini-pro',
    ])));

    $resolveModel = static function () use ($apiKey, $preferredModels) {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $endpoint = 'https://generativelanguage.googleapis.com/v1/models?key=' . urlencode($apiKey) . '&pageSize=100';
        $curl = curl_init($endpoint);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
        ]);
        $body = curl_exec($curl);
        $error = curl_error($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $supported = [];
        if ($body !== false && !$error && $httpStatus < 400) {
            $decoded = json_decode($body, true);
            if (!empty($decoded['models'])) {
                foreach ($decoded['models'] as $model) {
                    if (!empty($model['name']) && !empty($model['supportedGenerationMethods']) && in_array('generateContent', $model['supportedGenerationMethods'], true)) {
                        $supported[] = $model['name'];
                    }
                }
            }
        } else {
            error_log('[Gemini] ListModels fallback activated: ' . ($error ?: 'status ' . $httpStatus));
        }

        foreach ($preferredModels as $candidate) {
            if (in_array($candidate, $supported, true)) {
                return $cached = $candidate;
            }
        }

        if (!empty($supported[0])) {
            return $cached = $supported[0];
        }

        return $cached = ($preferredModels[0] ?? 'models/gemini-2.0-flash-exp');
    };

    $modelName = $resolveModel();
    $encodedModel = implode('/', array_map('rawurlencode', explode('/', $modelName)));
    $apiUrl = 'https://generativelanguage.googleapis.com/v1/' . $encodedModel . ':generateContent?key=' . urlencode($apiKey);
    $curl = curl_init($apiUrl);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $requestPayload,
    ]);

    $responseBody = curl_exec($curl);
    $curlError = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($responseBody === false) {
        error_log('[Gemini] cURL failed: ' . ($curlError ?: 'unknown error'));
        $respond(502, ['success' => false, 'message' => 'Sorry, I\'m having trouble connecting to my brain. Please try again later.']);
    }

    $responseData = json_decode($responseBody, true);
    if ($responseData === null) {
        error_log('[Gemini] Invalid JSON response: ' . $responseBody);
        $respond(502, ['success' => false, 'message' => 'Sorry, I\'m having trouble processing the response. Please try again later.']);
    }

    if ($httpCode >= 400) {
        $errorMessage = $responseData['error']['message'] ?? 'Gemini API failed';
        error_log('[Gemini] HTTP ' . $httpCode . ': ' . $errorMessage);
        $respond(502, ['success' => false, 'message' => 'Sorry, I\'m having trouble responding right now. Please try again later.']);
    }

    $botReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $botReply = sanitizeInput(trim($botReply));
    if ($botReply === '') {
        $respond(502, ['success' => false, 'message' => 'Sorry, I couldn\'t generate a response. Please try again.']);
    }

    // Additional security: Validate bot response doesn't contain sensitive info
    if (preg_match('/password|token|secret|key|api/i', $botReply)) {
        error_log('[NearBy Security] Potentially sensitive content in bot response: ' . substr($botReply, 0, 100));
        $botReply = 'I apologize, but I cannot provide that information. Please contact support for assistance.';
    }

    $insertSql = 'INSERT INTO chatbot_messages (user_id, sender, message) VALUES (?, ?, ?)';
    $insertStmt = mysqli_prepare($conn, $insertSql);
    if ($insertStmt === false) {
        $respond(500, ['success' => false, 'message' => 'Failed to prepare chat insert']);
    }

    mysqli_begin_transaction($conn);

    try {
        $sender = 'user';
        mysqli_stmt_bind_param($insertStmt, 'iss', $userId, $sender, $message);
        if (!mysqli_stmt_execute($insertStmt)) {
            throw new RuntimeException('Failed to store user message');
        }

        $sender = 'bot';
        mysqli_stmt_bind_param($insertStmt, 'iss', $userId, $sender, $botReply);
        if (!mysqli_stmt_execute($insertStmt)) {
            throw new RuntimeException('Failed to store bot message');
        }

        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        mysqli_stmt_close($insertStmt);
        $respond(500, ['success' => false, 'message' => 'Failed to store chat messages']);
    }

    mysqli_stmt_close($insertStmt);

    $respond(200, [
        'success' => true,
        'reply' => $botReply,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
} catch (Throwable $exception) {
    $respond(500, ['success' => false, 'message' => 'Unexpected server error']);
}
