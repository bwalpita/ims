<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    // VALIDATE STRIPE PAYMENT
    public function stripe_payment($user_id = "", $session_id = "", $is_instructor_payout = false) {
        if(!$is_instructor_payout) {
            $stripe_keys = get_settings('stripe_keys');
            $values = json_decode($stripe_keys);
            if ($values[0]->testmode == 'on') {
                $public_key = $values[0]->public_key;
                $secret_key = $values[0]->secret_key;
            } else {
                $public_key = $values[0]->public_live_key;
                $secret_key = $values[0]->secret_live_key;
            }
        }else{
            $instructor_data = $this->db->get_where('users', array('id' => $user_id))->row_array();
            $stripe_keys = json_decode($instructor_data['stripe_keys'], true);
            $public_key = $stripe_keys[0]['public_live_key'];
            $secret_key = $stripe_keys[0]['secret_live_key'];
        }

        // Stripe API configuration
        define('STRIPE_API_KEY', $secret_key);
        define('STRIPE_PUBLISHABLE_KEY', $public_key);

        $status_msg = '';
        $transaction_id = '';
        $paid_amount = '';
        $paid_currency = '';
        $payment_status = '';

        // Check whether stripe checkout session is not empty
        if($session_id != ""){
            //$session_id = $_GET['session_id'];

            // Include Stripe PHP library
            require_once APPPATH.'libraries/Stripe/init.php';

            // Set API key
            \Stripe\Stripe::setApiKey(STRIPE_API_KEY);

            // Fetch the Checkout Session to display the JSON result on the success page
            try {
                $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
            }catch(Exception $e) {
                $api_error = $e->getMessage();
            }

            if(empty($api_error) && $checkout_session){
                // Retrieve the details of a PaymentIntent
                try {
                    $intent = \Stripe\PaymentIntent::retrieve($checkout_session->payment_intent);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $api_error = $e->getMessage();
                }

                // Retrieves the details of customer
                try {
                    // Create the PaymentIntent
                    $customer = \Stripe\Customer::retrieve($checkout_session->customer);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $api_error = $e->getMessage();
                }

                if(empty($api_error) && $intent){
                    // Check whether the charge is successful
                    if($intent->status == 'succeeded'){
                        // Customer details
                        $name = $customer->name;
                        $email = $customer->email;

                        // Transaction details
                        $transaction_id = $intent->id;
                        $paid_amount = ($intent->amount/100);
                        $paid_currency = $intent->currency;
                        $payment_status = $intent->status;

                        // If the order is successful
                        if($payment_status == 'succeeded'){
                            $status_msg = get_phrase("Your_Payment_has_been_Successful");
                        }else{
                            $status_msg = get_phrase("Your_Payment_has_failed");
                        }
                    }else{
                        $status_msg = get_phrase("Transaction_has_been_failed");;
                    }
                }else{
                    $status_msg = get_phrase("Unable_to_fetch_the_transaction_details"). ' ' .$api_error;
                }

                $status_msg = 'success';
            }else{
                $status_msg = get_phrase("Transaction_has_been_failed").' '.$api_error;
            }
        }else{
            $status_msg = get_phrase("Invalid_Request");
        }

        $response['status_msg'] = $status_msg;
        $response['transaction_id'] = $transaction_id;
        $response['paid_amount'] = $paid_amount;
        $response['paid_currency'] = $paid_currency;
        $response['payment_status'] = $payment_status;
        $response['stripe_session_id'] = $session_id;
        $response['payment_method'] = 'stripe';

        return $response;
    }

    // VALIDATE PAYPAL PAYMENT AFTER PAYING
    public function paypal_payment($paymentID = "", $paymentToken = "", $payerID = "", $paypalClientID = "", $paypalSecret = "") {
      $paypal_keys = get_settings('paypal');
      $paypal_data = json_decode($paypal_keys);

      $paypalEnv       = $paypal_data[0]->mode; // Or 'production'
      if ($paypal_data[0]->mode == 'sandbox') {
          $paypalURL       = 'https://api.sandbox.paypal.com/v1/';
      } else {
          $paypalURL       = 'https://api.paypal.com/v1/';
      }

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $paypalURL.'oauth2/token');
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERPWD, $paypalClientID.":".$paypalSecret);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
      $response = curl_exec($ch);
      curl_close($ch);

      if(empty($response)){
          return false;
      }else{
          $jsonData = json_decode($response);
          $curl = curl_init($paypalURL.'payments/payment/'.$paymentID);
          curl_setopt($curl, CURLOPT_POST, false);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_HEADER, false);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              'Authorization: Bearer ' . $jsonData->access_token,
              'Accept: application/json',
              'Content-Type: application/xml'
          ));
          $response = curl_exec($curl);
          curl_close($curl);

          // Transaction data
          $result = json_decode($response);

          // CHECK IF THE PAYMENT STATE IS APPROVED OR NOT
          if($result && $result->state == 'approved'){
              return true;
          }else{
              return false;
          }
      }
    }

    public function invoice($payment, $user, $course, $paymentSum)
    {
        $fullName = $user['first_name'] . " " . $user['last_name'];
        $dueAmount = ($course['price'] - $paymentSum->payment);
        return '
        <html>
	<head>
		<meta charset="utf-8" />
		<title>Infogate Invoice</title>
		<style>
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
				color: #555;
			}
			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}
			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}
			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}
			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}
			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}
			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}
			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}
			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}
			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}
			.invoice-box table tr.item.last td {
				border-bottom: none;
			}
			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}
			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}
				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}
			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
			}
			.invoice-box.rtl table {
				text-align: right;
			}
			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
		</style>
	</head>
	<body>
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
                                <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 93.81"><defs><style>.cls-1{font-size:77.15px;fill:#337ab7;font-family:Helvetica-Bold, Helvetica;font-weight:700;}</style></defs><title>logo</title><text class="cls-1" transform="translate(0 66.27)">Infogate</text></svg>
								</td>
								<td>
									Receipt #: '. $payment['id'] .'<br />
									Created: '. date("F d, Y") .'
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
									
									Infogate Institute<br />
            #72/2, Main Street, Kantale, Sri Lanka<br />
			info@infogate.lk | www.infogate.lk<br />
            +94 26 22 34 582 | 0777120190									
								</td>
								<td>
									Reg. No: '. $user['id'] .'<br />
									Student Name: '. $fullName .'	
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="heading">
					<td>Description</td>
					<td>Amount</td>
				</tr>
				<tr class="item">
					<td height="84">'. $course['title'] .'</td>
					<td>Total Fees: LKR'. $course['price'] .'.00</td>
			  </tr>
				<tr class="item">
					<td>Amount Paid</td>
					<td>LKR'. $payment['amount'] .'.00</td>
				</tr>
				<tr class="item last">
					<td>Due Amount</td>
					<td>LKR'. $dueAmount .'.00</td>
				</tr>
			</table>
		</div>
	</body>
</html>';
    }
}
