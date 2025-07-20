# 🤖 InstaAnalyzerBot – Telegram Instagram Account Analyzer

**InstaAnalyzerBot** is a simple Telegram bot built in **PHP** that allows users to analyze public **Instagram** accounts by sending a username. It fetches data from **RapidAPI** and provides insights such as followers count, engagement rate, and statistics on the latest posts.

---

## 💡 Features

- `/start` command with interactive keyboard
- `/help` command with usage instructions
- Button: `📊 Analyze Account` to guide users
- Automatically detects and handles valid usernames
- Provides:
  - ✅ Username
  - ✅ Followers count
  - ✅ Last 3 posts with likes/comments
  - ✅ Engagement Rate (%)
- Error handling for:
  - Invalid usernames
  - Private or non-existent accounts
  - API connection issues

---

## 🚀 How to Use

1. Start the bot with `/start`
2. Click the `📊 Analyze Account` button or send a username directly (without `@`)
3. Get a detailed Instagram profile analysis

---

## 📸 Example Output

👤 Username: @cristiano
📊 Followers: 630,000,000
📈 Engagement Rate: 0.72%

📝 Latest Posts:
━━━━━━━━━━━━━━━━━━
📅 2025-07-19
❤️ Likes: 2,300,000
💬 Comments: 45,000

📅 2025-07-15
❤️ Likes: 2,100,000
💬 Comments: 39,000

📅 2025-07-10
❤️ Likes: 2,200,000
💬 Comments: 41,000

━━━━━━━━━━━━━━━━━━
📌 Analysis based on the latest 3 posts

---

## 🛠️ Tech Stack

| Layer        | Technology                          |
|--------------|-------------------------------------|
| Bot Engine   | Telegram Bot API                    |
| Language     | PHP 8+                              |
| Data Source  | [RapidAPI - instagram28](https://rapidapi.com/Glavier/api/instagram28) |
| Libraries    | `irazasyed/telegram-bot-sdk`, `vlucas/phpdotenv` |
| Hosting      | Any PHP-compatible server (e.g. cPanel, VPS, Laravel Forge) |

---

## 🔐 Environment Variables (.env)

Create a `.env` file in the project root with:

TELEGRAM_BOT_TOKEN=your_telegram_bot_token
RAPIDAPI_KEY=your_rapidapi_key

---

## 📦 Installation

1. Upload the project files to your server or hosting
2. Run:
   ```bash
   composer install
3. Set your Telegram webhook:
   ```bash
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://your-domain.com/
   ```
4. You're done! 🎉
