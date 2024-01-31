
# Test Stripe + Slack

This project uses PHP along with the Stripe and Slack APIs to handle payment events and send notifications to a Slack channel.

## Configuration

### Requirements
- PHP 7.0 or higher
- Composer
- Stripe Account
- Slack Account
- [Ngrok](https://ngrok.com/) installed on your computer.
- Local development server running (e.g., PHP built-in server).

### Installation

1. Clone the repository:


    git clone https://github.com/rociomonaco/api-php.git

2. Install dependencies using Composer:

    
    composer install
    
3. Copy the `.env.example` file to `.env` and configure your Stripe and Slack credentials.

## Usage

### Webhook Handling

This project uses Stripe webhooks to handle payment events. Make sure to set up webhooks in your Stripe account and provide the webhook URL in your Stripe dashboard.

Configure Webhooks in Stripe:
Set up webhooks in your Stripe account and provide the generated webhook URL in your Stripe dashboard.
Webhook URL: http://your-ngrok-subdomain.ngrok.io/webhook/stripe
ngrok http 8080

Install Stripe CLi 
$stripePath = Join-Path $PWD "src\cli\stripe_1.19.2_windows_x86_64"
$env:Path += ";$stripePath"

stripe login 
stripe trigger payment_intent.canceled

Webhook URL: http://your-domain.com/webhook/stripe

#### Join us to Alerts channel!
Stay in the loop by joining our Slack channel. Click the link below to connect and receive real-time alerts:
https://join.slack.com/t/nuevoespaciod-kud8678/shared_invite/zt-2blyljb9y-HTBvcbf6dDvYXqUPwVWMrg