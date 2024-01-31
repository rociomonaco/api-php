<?php
namespace src\Webhooks;

use src\Services\StripeService;

class StripeWebhookHandler
{
    private $stripeService;
    private $endpointSecret;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->endpointSecret = $_ENV['ENPOINT_SECRET_STRIPE'];
    }

    public function handleWebhook()
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $this->endpointSecret);
            $this->handleEvent($event);
        } catch (\UnexpectedValueException $e) {
            #Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            #Invalid signature
            http_response_code(400);
            exit();
        }
        #OK
        http_response_code(200);
    }

    private function handleEvent($event)
    {
        switch ($event->type) {
            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
                echo "Received .succeeded event. PaymentIntent ID: " . $paymentIntent->id;
                $this->stripeService->handleStripeEvent($event->type, $paymentIntent);
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
    }
}