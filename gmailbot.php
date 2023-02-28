<?php
// تنظیمات جیمیل
$email = "example@gmail.com"; // آدرس ایمیل
$password = "password"; // رمز عبور

// تنظیمات تلگرام
$telegramToken = "TOKEN"; // توکن ربات تلگرام
$chatID = "@GROUPNAME"; // آی‌دی گروه تلگرامی

// دسترسی به ایمیل‌ها
$inbox = imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $email, $password) or die("دسترسی به ایمیل با مشکل مواجه شد.");
$emails = imap_search($inbox, "ALL");

// بررسی وجود ایمیل
if (!$emails) {
    echo("هیچ ایمیلی پیدا نشد.");
} else {
    // تنظیمات پیام تلگرام
    $message = "این ایمیل‌های دریافتی شما هستند: \n\n";
    foreach ($emails as $email_number) {
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $message .= "موضوع: " . $overview[0]->subject . "\n";
        $message .= "فرستنده: " . $overview[0]->from . "\n";
        $message .= "تاریخ: " . $overview[0]->date . "\n\n";
    }

    // ارسال پیام به تلگرام
    $telegramUrl = "https://api.telegram.org/bot" . $telegramToken . "/sendMessage?chat_id=" . $chatID . "&text=" . urlencode($message);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, []);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
    $result = curl_exec($ch);
    curl_close($ch);

    // بررسی نتیجه ارسال پیام
    if (strpos($result, '"ok":false') === false) {
        echo("پیام با موفقیت به تلگرام ارسال شد.");
    } else {
        echo("مشکلی در ارسال پیام");
    }
}
