<?php

namespace src\Services;

use Exception;

class StripeService
{
    private $secretKey;
    private $slackService;


    public function __construct(SlackService  $slackService)
    {
        $this->secretKey = $_ENV['SECRET_KEY_STRIPE'];
        $this->slackService = $slackService;
        \Stripe\Stripe::setApiKey($this->secretKey);
    }

    public function handleStripeEvent($eventType, $data)
    {
        switch ($eventType) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($data);
                break;
                #case 'issuing.dispute': #not supported by cli
                case 'payment_intent.canceled':
                    $this->handlePaymentCanceled($data, $eventType);
                    break;
            default:
                echo "Unexpected event type:  $eventType";
                break;
        }
    }

    private function handlePaymentCanceled($data, $eventType){
        $prepareMsg = $this->prepareDataToSendMsg($data, $eventType); # formatted data to Slack
        $idTransaction = $data['id'];
        
        try{
            $data['payment_method'] = [
                    'type' => 'card',
                    'brand' => 'visa',
                    'checks' => [
                        'address_line1_check' => null,
                        'address_postal_code_check' => null,
                        'cvc_check' => 'unchecked',
                    ]
                ];
            echo $data['payment_method'];
            if($data['payment_method']['type'] === 'card' && $data['payment_method']['brand'] === 'visa'){
                //TODO:
                /*
                $customerId = $this->getValue($data, 'customer');

                #method api stripe
                $associatedOrders = $this->getAssociatedOrders($customerId);

                #si tuviese mas tiempo lo guardaría en la base de datos
                $_SESSION['associated_orders_canceled'] = $associatedOrders;
                */
            }
            //$paymentIntent = $this->retrievePaymentIntent($idTransaction); #comento para poder probar un caso que sí venga con el payment_method
            
        }catch(Exception $e){
            #I would implements a log system 
            echo 'Error to retrieve el Payment Intent: ' . $e->getMessage();
        }

        $this->slackService->sendNotification($prepareMsg);
    }
    
    private function prepareDataToSendMsg($data,$eventType)
    {
        $message = null;
        if ($eventType === 'payment_intent.canceled') {
            $paymentIntentId = $this->getValue($data, 'id'); 
            $amount = isset($data['amount']) ? number_format($data['amount'], 2, ",", ".") : null;
            $currency = $this->getValue($data, 'currency');
            $customer = $data['customer'] ? $this->getValue($data['customer'], 'name') : ' request by CLI doesnt have a customer';

            // slack structure msg
            $message = [
                'text' => 'New Payment Intent Canceled:', #no usé el webhook disputas porque no podia ejecutarlo mediante CLI
                'attachments' => [
                    [
                        'pretext' => 'Details:',
                        'color' => '#FF0000', 
                        'fields' => [
                            ['title' => 'Event type', 'value' => $eventType, 'short' => true],
                            ['title' => 'Payment Intent ID', 'value' => $paymentIntentId, 'short' => true],
                            ['title' => 'Amount', 'value' => strtoupper($currency) . ' $' . $amount, 'short' => true],
                            ['title' => 'Customer', 'value' => $customer, 'short' => true],
                        ],
                        'footer' => 'Stripe Notifications',
                        'footer_icon' => 'https://cdn.freebiesupply.com/images/large/2x/stripe-logo-white-on-blue-gradient.png',
                        'ts' => time(),
                    ],
                ],
            ];
        }else{
            $message = [
                'text' => 'New Event Unkwon:',
                'attachments' => [
                    [
                        'fields' => [
                            ['title' => ' ID', 'value' => $this->getValue($data, 'id'), 'short' => true],
                            ['title' => 'Event Type', 'value' => $eventType, 'short' => true],
                        ],
                    ],
                ],
            ];
        }

        return $message;

    }

    private function handlePaymentSucceeded($paymentIntent)
    {
        //TODO: nueva lógica
        echo "payment_intent " . json_encode($paymentIntent);
    }

    #validate value of array 
    private function getValue($array, $key)
    {
        return isset($array[$key]) ? $array[$key] : null;
    }

    #need to get the entire info of the payment Intent like card (to know about the brand) - this info is not in de webhook 
    private function retrievePaymentIntent($paymentIntentId){
        try {
            
            $response = \Stripe\PaymentIntent::retrieve($paymentIntentId,  ['expand' => ['payment_method']]);
            #simulo para el punto de validar si es VISA 
            $response['data']['payment_method'] = [
                                                    'card' => [
                                                        'brand' => 'visa',
                                                        'checks' => [
                                                            'address_line1_check' => null,
                                                            'address_postal_code_check' => null,
                                                            'cvc_check' => 'unchecked',
                                                        ],
                                                    ],
                                                ];

            return $response;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo 'Error to retrieve el Payment Intent: ' . $e->getMessage();
        }
    }
}
