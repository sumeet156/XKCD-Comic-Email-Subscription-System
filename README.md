# XKCD Comic Email Subscription System 📬

A PHP-based email subscription system that allows users to receive a **random XKCD comic daily** via email. It features email verification, secure unsubscription, and automatic daily comic delivery using CRON jobs.

---

## 🚀 Features

✅ User email verification with OTP  
✅ Secure unsubscription via email confirmation  
✅ XKCD comic delivery daily via cron  
✅ Emails sent using **Gmail SMTP** and **PHPMailer**  
✅ Lightweight: no database required (uses `.txt` files)  
✅ Deployable via Render (Web Service) + Cron-job.org (Free CRON)

---

## 🖼️ How It Works

1. User submits their email address on the website.
2. They receive a 6-digit verification code via email.
3. After verifying, their email is stored and subscribed.
4. A CRON job (triggered daily) sends them a **random XKCD comic**.
5. Emails contain an unsubscribe link with verification.

---

## 🔧 Technologies Used

- PHP 8.3
- PHPMailer
- Gmail SMTP (App Password)
- CRON (via [cron-job.org](https://cron-job.org))
- Render.com (for web hosting)
- HTML/CSS (for forms & UI)

---

## 📂 Folder Structure

├── index.php # Main subscription form
├── unsubscribe.php # Unsubscribe via verification
├── cron.php # Script to send comics (runs daily)
├── functions.php # All core functions
├── config.php # SMTP and logging config
├── registered_emails.txt # Stores verified email addresses
├── codes/ # Stores OTPs temporarily
├── setup.php # Prepares folders/files
├── PHPMailer/ # Mailer library files
├── Dockerfile # For Render deployment


---

## ⚙️ Setup Instructions

### 📦 Local Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/sumeet156/XKCD-Comic-Email-Subscription-System.git

Run locally: php -S localhost:8000

📜 License
This project is built for educational and showcase purposes. No commercial license is granted for bulk mailing or business usage of XKCD comics.

🌟 Demo 
🔗 Live site: https://xkcd-comic-app.onrender.com/
   Demo Video: https://www.youtube.com/watch?v=XEgP8fLrwsI



