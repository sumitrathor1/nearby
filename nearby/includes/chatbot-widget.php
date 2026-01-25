<?php
if (!isset($currentUser)) {
    $currentUser = $_SESSION['user'] ?? null;
}
$isLoggedIn = !empty($currentUser);
?>
<div class="chatbot-entry" data-chatbot data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">
    <button type="button" class="chatbot-toggle" data-chatbot-toggle aria-label="Open chat assistant">
        <i class="bi bi-robot"></i>
    </button>
    <div class="chatbot-panel" data-chatbot-panel hidden>
        <div class="chatbot-header">
            <div>
                <span class="chatbot-title">NearBy Assistant</span>
                <span class="chatbot-subtitle">Powered by Gemini</span>
            </div>
            <button type="button" class="chatbot-close" data-chatbot-close aria-label="Close chat assistant">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="chatbot-messages" data-chatbot-messages>
            <div class="chatbot-empty" data-chatbot-empty>
                <i class="bi bi-chat-dots"></i>
                <p class="mb-0">Ask anything about rooms, services, or neighbourhood tips.</p>
            </div>
        </div>
        <form class="chatbot-form" data-chatbot-form autocomplete="off">
            <input type="text" class="chatbot-input" data-chatbot-input placeholder="Type your message..." <?= $isLoggedIn ? '' : 'disabled' ?>>
            <button type="submit" class="chatbot-send" data-chatbot-send <?= $isLoggedIn ? '' : 'disabled' ?>>
                <i class="bi bi-send" aria-hidden="true"></i>
            </button>
        </form>
        <?php if (!$isLoggedIn): ?>
            <div class="chatbot-locked" data-chatbot-locked>
                <p class="mb-3">Please login to use the chat assistant.</p>
                <a class="btn btn-primary btn-sm" href="login.php">Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>
