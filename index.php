<?php

use src\Services\StripeService;
use src\Services\SlackService;
use src\Webhooks\StripeWebhookHandler;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Services/StripeService.php';
require_once __DIR__ . '/src/Services/SlackService.php';
require_once __DIR__ . '/src/Webhooks/StripeWebhookHandler.php';

// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// load enviroment data
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$requestMethod = $_SERVER['REQUEST_METHOD'];

// TODO: manejar mejor la parte de las rutas para los endpoints
if($requestMethod === 'GET'){

    echo "<h2>Welcome, Join us to communicate with Stripe&Slack</h2>";
}else{
    $slackWebhookUrl = $_ENV['URL_WEBHOOK_SLACK'];
    $slackService = new SlackService($slackWebhookUrl);
    # init
    $stripeService = new StripeService($slackService);
    $stripeWebhookHandler = new StripeWebhookHandler($stripeService);

    #webhook
    try {
        $stripeWebhookHandler->handleWebhook();
    } catch (\Exception $e) {
        echo 'Error: handleWebhook ' . $e->getMessage();
    }
}

