<?php
// ==================================================
// ⚡ RAHAT • LAB GENERATOR (DEMO BOT)
// ==================================================

// ===================== CONFIG =====================
$botToken = "8087138122:AAEhFKkEytW0dS9IUSP4qseY6wewwNV9lmc"; // <-- এখানে আপনার বট টোকেন দিন
$api = "https://api.telegram.org/bot$botToken/";

// ===================== RECEIVE UPDATE =====================
$update = json_decode(file_get_contents("php://input"), true);
if (!isset($update["message"])) exit;

$chatId = $update["message"]["chat"]["id"];
$text   = trim($update["message"]["text"]);
$msgId  = $update["message"]["message_id"];

// ===================== COMMAND: /gen =====================
if (strpos($text, "/gen") === 0) {

    $pattern = trim(substr($text, 5));

    if (empty($pattern) || !preg_match('/^[0-9xX]{4,6}$/', $pattern)) {
        sendMessage($chatId,
            "⚠️ <b>Invalid Pattern</b>\n\nUse like:\n<code>/gen 4532</code>",
            $msgId
        );
        exit;
    }

    // Typing animation
    file_get_contents($api."sendChatAction?chat_id=$chatId&action=typing");
    usleep(900000);

    // Loading screen
    $loadingId = sendMessage($chatId,
        "⚡ <b>Rahat Engine Initializing...</b>\n⏳ Loading modules...",
        $msgId
    );

    usleep(1300000);

    // Generate demo cards
    $cards = "";
    for ($i = 1; $i <= 5; $i++) {
        $cards .= buildDemoCard($pattern, $i);
    }

    // Final output
    $finalText = 
"╔══════════════════════╗
║  ⚡ <b>RAHAT • LAB GEN</b>  ║
╚══════════════════════╝

🔍 <b>Pattern:</b> <code>$pattern</code>
🧪 <b>Mode:</b> Visual Demo Engine

━━━━━━━━━━━━━━━━━━━━━━━
👤 <b>Name:</b> Rahat
💳 <b>Type:</b> Demo / Lab
━━━━━━━━━━━━━━━━━━━━━━━

$cards
⚠️ <b>NON-REAL DATA</b>
🎮 Just for UI & Fun
🧠 Crafted by Rahat Engine ©";

    editMessage($chatId, $loadingId, $finalText);
}

// ===================== FUNCTIONS =====================

function sendMessage($chatId, $text, $reply = null) {
    global $api;
    $url = $api."sendMessage?chat_id=$chatId&parse_mode=HTML&text=".urlencode($text);
    if ($reply) $url .= "&reply_to_message_id=$reply";
    $res = json_decode(file_get_contents($url), true);
    return $res["result"]["message_id"] ?? null;
}

function editMessage($chatId, $messageId, $text) {
    global $api;
    file_get_contents(
        $api."editMessageText?chat_id=$chatId&message_id=$messageId&parse_mode=HTML&text=".urlencode($text)
    );
}

function buildDemoCard($pattern, $index) {

    $pool = str_split("ABCDEFGHJKLMNPQRSTUVWXYZ23456789");
    $body = "";
    for ($i = 0; $i < 8; $i++) {
        $body .= $pool[array_rand($pool)];
    }

    $mm  = str_pad(rand(1, 12), 2, "0", STR_PAD_LEFT);
    $yy  = rand(28, 36);
    $cvv = rand(100, 999);

    return
"┏━ <b>CARD #".str_pad($index, 2, '0', STR_PAD_LEFT)."</b> ━━━━━━━━━━━
┃ <code>$pattern•".substr($body,0,4)."•".substr($body,4,4)."</code>
┃ EXP ▸ $mm / $yy
┃ CVV ▸ $cvv
┗━━━━━━━━━━━━━━━━━━━━━━

";
}
