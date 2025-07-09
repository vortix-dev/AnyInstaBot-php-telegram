<?php
require 'vendor/autoload.php';

use Telegram\Bot\Api;
use Dotenv\Dotenv;
use Telegram\Bot\Keyboard\Keyboard;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$telegram = new Api($_ENV['TELEGRAM_BOT_TOKEN']);
$update = $telegram->getWebhookUpdate();

if ($update->getMessage()) {
    $text = trim($update->getMessage()->getText());
    $chatId = $update->getMessage()->getChat()->getId();

    // /start
    if ($text === "/start") {
        $keyboard = Keyboard::make([
            'keyboard' => [['📊 تحليل حساب']],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "👋 أهلاً بك في بوت تحليل حسابات Instagram.\n\n📥 فقط أرسل اسم المستخدم (بدون @)، وسنقوم بتحليل الحساب تلقائيًا.\n\nاكتب مثلاً:\n`cristiano` أو `zendaya`",
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard
        ]);
        exit;
    }

    // /help
    if ($text === "/help") {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "ℹ️ *طريقة الاستخدام:*\n\nفقط أرسل اسم مستخدم Instagram (مثال: `zuck`) وسنقوم بتحليل الحساب تلقائيًا.\n\nلا تحتاج لكتابة أي أوامر.",
            'parse_mode' => 'Markdown'
        ]);
        exit;
    }

    // زر وهمي - إظهار تعليمات
    if ($text === "📊 تحليل حساب") {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "✏️ أرسل الآن اسم المستخدم (بدون @)\n\nمثال:\n`zuck` أو `cristiano`",
            'parse_mode' => 'Markdown'
        ]);
        exit;
    }

    // تحليل تلقائي إذا أرسل المستخدم نصًا عاديًا
    if (!str_starts_with($text, "/")) {
        $username = preg_replace('/[^a-zA-Z0-9._]/', '', $text); // تأكيد أنه اسم صالح

        if (!$username) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ اسم المستخدم غير صالح. الرجاء إدخال اسم صحيح بدون رموز خاصة."
            ]);
            exit;
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "🔍 جاري تحليل الحساب @$username ..."
        ]);

        $result = analyzeInstagram($username);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $result
        ]);
        exit;
    }
}

function analyzeInstagram($username)
{
    $url = "https://instagram28.p.rapidapi.com/user_info?username=$username";
    $headers = [
        "X-RapidAPI-Host: instagram28.p.rapidapi.com",
        "X-RapidAPI-Key: " . $_ENV['RAPIDAPI_KEY']
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return "⚠️ فشل في الاتصال بـ Instagram.";

    $data = json_decode($response, true);
    if (!isset($data['data']['user'])) return "❌ المستخدم غير موجود أو الحساب خاص.";

    $user = $data['data']['user'];
    $followersCount = $user['edge_followed_by']['count'];
    $followers = number_format($followersCount);
    $posts = array_slice($user['edge_owner_to_timeline_media']['edges'], 0, 3);

    $output = "👤 Username: @{$user['username']}\n";
    $output .= "📊 Followers: $followers\n";
    $output .= "📈 Engagement Rate: ⏳\n\n";
    $output .= "📝 Latest Posts:\n━━━━━━━━━━━━━━━━━━\n";

    $totalLikes = 0;
    $totalComments = 0;

    foreach ($posts as $post) {
        $likes = $post['node']['edge_liked_by']['count'] ?? 0;
        $comments = $post['node']['edge_media_to_comment']['count'] ?? 0;
        $timestamp = date("Y-m-d", $post['node']['taken_at_timestamp']);

        $totalLikes += $likes;
        $totalComments += $comments;

        $output .= "📅 $timestamp\n❤️ Likes: $likes\n💬 Comments: $comments\n\n";
    }

    $engagement = $followersCount > 0 ? round((($totalLikes + $totalComments) / ($followersCount * count($posts))) * 100, 2) : 0;
    $output = str_replace("⏳", "$engagement%", $output);
    $output .= "━━━━━━━━━━━━━━━━━━\n📌 تحليل تم بناءً على آخر 3 منشورات";

    return $output;
}
