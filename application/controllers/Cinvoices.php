<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cInvoices extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if ($this->client) {
			foreach ($this->view_data['menu'] as $key => $value) {
				if ($value->link == "cinvoices") {
					$access = TRUE;
				}
			}
			if (!$access) {
				redirect('login');
			}
		} elseif ($this->user) {
			redirect('invoices');
		} else {

			redirect('login');
		}
		$this->view_data['submenu'] = array(
			$this->lang->line('application_all_invoices') => 'cinvoices',
		);
	}
	function index()
	{
		$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('company_id=? AND estimate != ? AND issue_date<=?', $this->client->company->id, 1, date('Y-m-d', time()))));
		$this->content_view = 'invoices/client_views/all';
	}

	function view($id = FALSE)
	{
		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'invoices',
		);
		$this->view_data['invoice'] = Invoice::find($id);
		$data["core_settings"] = Setting::first();
		$invoice = $this->view_data['invoice'];
		$this->view_data['items'] = $invoice->invoice_has_items;

		//calculate sum
		$i = 0;
		$sum = 0;
		foreach ($this->view_data['items'] as $value) {
			$sum = $sum + $invoice->invoice_has_items[$i]->amount * $invoice->invoice_has_items[$i]->value;
			$i++;
		}
		if (substr($invoice->discount, -1) == "%") {
			$discount = sprintf("%01.2f", round(($sum / 100) * substr($invoice->discount, 0, -1), 2));
		} else {
			$discount = $invoice->discount;
		}
		$sum = $sum - $discount;

		if ($invoice->tax != "") {
			$tax_value = $invoice->tax;
		} else {
			$tax_value = $data["core_settings"]->tax;
		}

		if ($invoice->second_tax != "") {
			$second_tax_value = $invoice->second_tax;
		} else {
			$second_tax_value = $data["core_settings"]->second_tax;
		}

		$tax = sprintf("%01.2f", round(($sum / 100) * $tax_value, 2));
		$second_tax = sprintf("%01.2f", round(($sum / 100) * $second_tax_value, 2));

		$sum = sprintf("%01.2f", round($sum + $tax + $second_tax, 2));

		$payment = 0;
		$i = 0;
		$payments = $invoice->invoice_has_payments;
		if (isset($payments)) {
			foreach ($payments as $value) {
				$payment = sprintf("%01.2f", round($payment + $payments[$i]->amount, 2));
				$i++;
			}
			$invoice->paid = $payment;
			$invoice->outstanding = sprintf("%01.2f", round($sum - $payment, 2));
		}

		$invoice->sum = $sum;
		$invoice->save();


		if ($this->view_data['invoice']->company_id != $this->client->company->id) {
			redirect('cinvoices');
		}
		$this->content_view = 'invoices/client_views/view';
	}
	function download($id = FALSE)
	{
		$this->load->helper(array('dompdf', 'file'));
		$this->load->library('parser');
		$data["invoice"] = Invoice::find($id);
		$data['items'] = InvoiceHasItem::find('all', array('conditions' => array('invoice_id=?', $id)));
		if ($data['invoice']->company_id != $this->client->company->id) {
			redirect('cinvoices');
		}
		$data["core_settings"] = Setting::first();
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date . ' 00:00:00'));
		$parse_data = array(
			'due_date' => $due_date,
			'invoice_id' => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
		);
		$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $this->lang->line('application_invoice') . '_' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference;
		pdf_create($html, $filename, TRUE);
	}
	function banktransfer($id = FALSE, $sum = FALSE)
	{

		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_bank_transfer');

		$data["core_settings"] = Setting::first();
		$this->view_data['invoice'] = Invoice::find($id);
		$this->content_view = 'invoices/client_views/_banktransfer';
	}
	function twocheckout($id = FALSE, $sum = FALSE)
	{
		$data["core_settings"] = Setting::first();
		$this->load->helper('notification');

		if ($_POST) {
			$invoice = Invoice::find_by_id($_POST['id']);
			$invoice_reference = $data["core_settings"]->invoice_prefix . $invoice->reference;
			$this->load->file(APPPATH . 'helpers/2checkout/Twocheckout.php', true);
			$token = $_POST["token"];
			Twocheckout::privateKey($data["core_settings"]->twocheckout_private_key);
			Twocheckout::sellerId($data["core_settings"]->twocheckout_seller_id);
			//Twocheckout::sandbox(true);  #Uncomment to use Sandbox

			//Get currency
			$currency = $invoice->currency;
			$currency_codes = getCurrencyCodesForTwocheckout();
			if (!array_key_exists($currency, $currency_codes)) {
				$currency = $data["core_settings"]->twocheckout_currency;
			}
			$total = floatval(str_replace(",", "", $_POST['sum']));

			try {
				$charge = Twocheckout_Charge::auth(array(
					"sellerId" => $data['core_settings']->twocheckout_seller_id,
					"merchantOrderId" => $invoice_reference,
					"token"      => $_POST['token'],
					"currency"   => $currency,
					'total' => $total,
					"billingAddr" => array(
						"name" => $invoice->company->name,
						"addrLine1" => $invoice->company->address,
						"city" => $invoice->company->city,
						"state" => $invoice->company->province,
						"zipCode" => $invoice->company->zipcode,
						"country" => $invoice->company->country,
						"email" => $invoice->company->client->email,
						"phoneNumber" => $invoice->company->phone
					)
				));

				if ($charge['response']['responseCode'] == 'APPROVED') {

					$attr = array();
					$paid_date = date('Y-m-d', time());
					$payment_reference = $invoice->reference . '00' . InvoiceHasPayment::count(array('conditions' => 'invoice_id = ' . $invoice->id)) + 1;
					$attributes = array('invoice_id' => $invoice->id, 'reference' => $payment_reference, 'amount' => $_POST['sum'], 'date' => $paid_date, 'type' => 'credit_card', 'notes' => '');
					$invoiceHasPayment = InvoiceHasPayment::create($attributes);

					if ($_POST['sum'] >= $invoice->outstanding) {
						$invoice->update_attributes(array('paid_date' => $paid_date, 'status' => 'Paid'));
					} else {
						$invoice->update_attributes(array('status' => 'PartiallyPaid'));
					}

					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_payment_complete'));
					log_message('error', '2Checkout: Payment of ' . $_POST['sum'] . ' for invoice ' . $invoice_reference . ' received!');
					//send receipt to client
					receipt_notification($this->client->id, FALSE, $invoiceHasPayment->id);
					//send email to admin
					send_notification($data["core_settings"]->email, $this->lang->line('application_notification_payment_processed_subject'), $this->lang->line('application_notification_payment_processed') . ' #' . $data["core_settings"]->invoice_prefix . $invoiceHasPayment->invoice->reference);
				}
			} catch (Twocheckout_Error $e) {
				$this->session->set_flashdata('message', 'error: Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction.');
				log_message('error', '2Checkout: Payment of invoice ' . $invoice_reference . ' failed - ' . $e->getMessage());
			}
			redirect('cinvoices/view/' . $_POST['id']);
		} else {
			$this->view_data['invoices'] = Invoice::find_by_id($id);

			$this->view_data['publishable_key'] = $data["core_settings"]->twocheckout_publishable_key;
			$this->view_data['seller_id'] = $data["core_settings"]->twocheckout_seller_id;

			$this->view_data['sum'] = $sum;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
			$this->view_data['form_action'] = 'cinvoices/twocheckout';
			$this->content_view = 'invoices/_2checkout';
		}
	}
	function stripepay($id = FALSE, $sum = FALSE, $type = "card")
	{
		$data['core_settings'] = Setting::first();

		$stripe_keys = [
			'secret_key' => $data['core_settings']->stripe_p_key,
			'publishable_key' => $data['core_settings']->stripe_key
		];

		if ($_POST) {
			unset($_POST['send']);

			$invoice = Invoice::find($_POST['id']);
			// Stores errors:

			// Set the order amount somehow:
			$sum_exp = explode('.', $_POST['sum']);
			$amount = $sum_exp[0] * 100; //in cents
			$amount = isset($sum_exp[1]) ? $amount + $sum_exp[1] : $amount; // add cents

			//Get currency

			$currency = $invoice->currency;
			$currency_codes = getCurrencyCodes();
			if (!array_key_exists($currency, $currency_codes)) {
				$currency = $data['core_settings']->stripe_currency;
			}

			$intent = null;
			try {
				// Set API key for stripe:
				\Stripe\Stripe::setApiKey($stripe_keys['secret_key']);
				if (isset($_POST['payment_method_id'])) {
					# Create the PaymentIntent
					$intent = \Stripe\PaymentIntent::create([
						'payment_method' => $_POST['payment_method_id'],
						'amount' => $amount,
						'currency' => $currency,
						'confirmation_method' => 'manual',
						'confirm' => true,
						'receipt_email' => $invoice->company->client->email,
						'description' => $data['core_settings']->invoice_prefix . $invoice->reference,
					]);
				}
				if (isset($_POST['payment_intent_id'])) {
					$intent = \Stripe\PaymentIntent::retrieve(
						$_POST['payment_intent_id']
					);
					$intent->confirm();
				}

				# Note that if your API version is before 2019-02-11, 'requires_action'
				# appears as 'requires_source_action'.
				if (
					$intent->status == 'requires_action' &&
					$intent->next_action->type == 'use_stripe_sdk'
				) {
					# Tell the client to handle the action
					echo json_encode([
						'requires_action' => true,
						'payment_intent_client_secret' => $intent->client_secret
					]);
					die();
				} else if ($intent->status == 'succeeded') {
					# The payment didn’t need any additional actions and completed!
					# Handle post-payment fulfillment
					$attr = [];
					$paid_date = date('Y-m-d', time());
					$payment_reference = $invoice->reference . '00' . InvoiceHasPayment::count(['conditions' => 'invoice_id = ' . $invoice->id]) + 1;
					$attributes = ['invoice_id' => $invoice->id, 'reference' => $payment_reference, 'amount' => $_POST['sum'], 'date' => $paid_date, 'type' => 'credit_card', 'notes' => ''];
					$invoiceHasPayment = InvoiceHasPayment::create($attributes);

					if ($_POST['sum'] >= $invoice->outstanding) {
						$invoice->update_attributes(['paid_date' => $paid_date, 'status' => 'Paid']);
					} else {
						$invoice->update_attributes(['status' => 'PartiallyPaid']);
					}

					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_payment_complete'));
					log_message('error', 'Stripe: Payment for Invoice #' . $invoice->reference . ' successfully made');

					echo json_encode([
						"success" => true
					]);
					die();
				} else {
					# Invalid status
					http_response_code(500);
					echo json_encode(['error' => 'Invalid PaymentIntent status']);
					die();
				}
			} catch (\Stripe\Error\Base $e) {
				# Display error on client
				echo json_encode([
					'error' => $e->getMessage()
				]);
				die();
			}

			//redirect('invoices/view/' . $_POST['id']);
		} else {
			$this->view_data['invoices'] = Invoice::find_by_id($id);

			$this->view_data['public_key'] = $data['core_settings']->stripe_key;
			$this->view_data['sum'] = $sum;
			$this->theme_view = 'modal';
			$this->view_data['csrf'] = $this->security->get_csrf_hash();

			switch ($type) {
				case 'ideal':
					$this->view_data['form_action'] = 'cinvoices/idealpay';
					$this->view_data['title'] = $this->lang->line('application_pay_with_ideal');
					$this->content_view = 'invoices/_stripe_ideal';
					break;
				default:
					$this->view_data['form_action'] = 'cinvoices/stripepay';
					$this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
					$this->content_view = 'invoices/_stripe';
					break;
			}
		}
	}
	function success($id = FALSE)
	{
		$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_payment_success'));
		redirect('cinvoices/view/' . $id);
	}

	function idealpay($id = FALSE, $sum = FALSE)
	{
		$core_settings = Setting::first();
		// Set API key for stripe:
		\Stripe\Stripe::setApiKey($core_settings->stripe_p_key);
		// Get Stripe source from url source id 
		$source = \Stripe\Source::retrieve($_GET['source']);
		// Find invoice and get currecny
		$invoice = Invoice::find_by_id($id);
		$currency = $invoice->currency;

		$sum_exp = explode('.', $sum);
		$amount = $sum_exp[0] * 100 + $sum_exp[1]; // in cents

		switch ($source->status) {
			case 'chargeable':
				$create = \Stripe\Charge::create(array(
					'amount' => $amount,
					'currency' => $currency,
					"source" => $_GET['source'],
				));
				var_dump($create->status);
				if ($create->status == "succeeded") {

					$attr = array();
					$paid_date = date('Y-m-d', time());
					$payment_reference = $invoice->reference . '00' . InvoiceHasPayment::count(array('conditions' => 'invoice_id = ' . $invoice->id)) + 1;
					$attributes = array('invoice_id' => $invoice->id, 'reference' => $payment_reference, 'amount' => $sum, 'date' => $paid_date, 'type' => 'iDEAL', 'notes' => '');
					$invoiceHasPayment = InvoiceHasPayment::create($attributes);

					if ($sum >= $invoice->outstanding) {
						$invoice->update_attributes(array('paid_date' => $paid_date, 'status' => 'Paid'));
					} else {
						$invoice->update_attributes(array('status' => 'PartiallyPaid'));
					}

					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_payment_complete'));
					log_message('error', 'Stripe: Payment for Invoice #' . $core_settings->invoice_prefix . $invoice->reference . ' successfully made with iDEAL.');
				} else {
					$this->session->set_flashdata('message', 'error: Payment could not be processed!');
					log_message('error', 'iDEAL Payment was canceled!');
				}

				break;
			case 'canceled':
				$this->session->set_flashdata('message', 'error: Payment could not be processed!');
				log_message('error', 'iDEAL Payment was canceled!');
				break;
			case 'consumed':
				$this->session->set_flashdata('message', 'error: Payment already completed!');
				log_message('error', 'iDEAL Payment was called again but already compleated!');
				break;
			case 'failed':
				$this->session->set_flashdata('message', 'error: Payment failed!');
				log_message('error', 'iDEAL Payment failed during process!');
				break;
		}

		redirect("cinvoices/view/" . $id);
	}

	function authorizenet($id = FALSE)
	{

		if ($_POST) {
			// Authorize.net lib
			$data["core_settings"] = Setting::first();
			$this->load->library('authorize_net');
			$invoice = Invoice::find_by_id($_POST['invoice_id']);
			log_message('error', 'Authorize.net: Payment process started for invoice: #' . $data["core_settings"]->invoice_prefix . $invoice->reference);

			$amount = $_POST["sum"];

			$auth_net = array(
				'x_card_num'			=> str_replace(' ', '', $_POST['x_card_num']),
				'x_exp_date'			=> $_POST['x_card_month'] . '/' . $_POST['x_card_year'],
				'x_card_code'			=> $_POST['x_card_code'],
				'x_description'			=> $this->lang->line('application_invoice') . ' #' . $data["core_settings"]->invoice_prefix . $invoice->reference,
				'x_amount'				=> $amount,
				'x_first_name'			=> $invoice->company->client->firstname,
				'x_last_name'			=> $invoice->company->client->lastname,
				'x_address'				=> $invoice->company->address,
				'x_city'				=> $invoice->company->city,
				//'x_state'				=> 'KY',
				'x_zip'					=> $invoice->company->zipcode,
				//'x_country'			=> 'US',
				'x_phone'				=> $invoice->company->phone,
				'x_email'				=> $invoice->company->client->email,
				'x_customer_ip'			=> $this->input->ip_address(),
			);
			$this->authorize_net->setData($auth_net);
			// Try to AUTH_CAPTURE
			if ($this->authorize_net->authorizeAndCapture()) {

				$this->session->set_flashdata('message', 'success: ' . $this->lang->line('messages_payment_complete'));

				log_message('error', 'Authorize.net: Transaction ID: ' . $this->authorize_net->getTransactionId());
				log_message('error', 'Authorize.net: Approval Code: ' . $this->authorize_net->getApprovalCode());
				log_message('error', 'Authorize.net: Payment completed.');
				$invoice->status = "Paid";
				$invoice->paid_date = date('Y-m-d', time());

				$invoice->save();
				$attributes = array('invoice_id' => $invoice->id, 'reference' => $this->authorize_net->getTransactionId(), 'amount' => $amount, 'date' => date('Y-m-d', time()), 'type' => 'credit_card', 'notes' => $this->authorize_net->getApprovalCode());
				$invoiceHasPayment = InvoiceHasPayment::create($attributes);
				//send receipt to client
				receipt_notification($this->client->id, FALSE, $invoiceHasPayment->id);
				//send email to admin
				send_notification($data["core_settings"]->email, $this->lang->line('application_notification_payment_processed_subject'), $this->lang->line('application_notification_payment_processed') . ' #' . $data["core_settings"]->invoice_prefix . $invoiceHasPayment->invoice->reference);
				redirect('cinvoices/view/' . $invoice->id);
			} else {

				log_message('error', 'Authorize.net: Payment failed.');
				log_message('error', 'Authorize.net: ' . $this->authorize_net->getError());



				$this->view_data['return_link'] = "invoices/view/" . $invoice->id;

				$this->view_data['message'] = $this->authorize_net->getError();
				//$this->authorize_net->debug();


				$this->content_view = 'error/error';
			}
		} else {

			$this->view_data['invoices'] = Invoice::find_by_id($id);
			$this->view_data["settings"] = Setting::first();
			$this->view_data["sum"] = sprintf("%01.2f", $this->view_data['invoices']->outstanding);

			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
			$this->view_data['form_action'] = 'cinvoices/authorizenet';
			$this->content_view = 'invoices/_authorizenet';
		}
	}
}
