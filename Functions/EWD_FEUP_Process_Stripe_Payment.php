<?php 
function EWD_FEUP_Stripe_Process_Payment() {
	global $wpdb;
	global $ewd_feup_user_table_name;

	if(isset($_POST['action']) && $_POST['action'] == 'ewd_feup_stripe' && wp_verify_nonce($_POST['stripe_nonce'], 'stripe-nonce')) {
 		
 		$Payment_Frequency = get_option("EWD_FEUP_Payment_Frequency");
		$Payment_Types = get_option("EWD_FEUP_Payment_Types");
		$Membership_Cost = get_option("EWD_FEUP_Membership_Cost");
		$Levels_Payment_Array = get_option("EWD_FEUP_Levels_Payment_Array");
		$Discount_Codes_Array = get_option("EWD_FEUP_Discount_Codes_Array");
		$Pricing_Currency_Code = get_option("EWD_FEUP_Pricing_Currency_Code");
		$Thank_You_URL = get_option("EWD_FEUP_Thank_You_URL");

		$Stripe_Live_Secret = get_option("EWD_FEUP_Stripe_Live_Secret");
		$Stripe_Live_Publishable = get_option("EWD_FEUP_Stripe_Live_Publishable");
		$Stripe_Plan_ID = get_option("EWD_FEUP_Stripe_Plan_ID");

		$Payment_Date = date("Y-m-d H:i:s");
		
		$User_ID = $_POST['User_ID'];
		$Username = $wpdb->get_var($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID=%d", $User_ID));
		$Discount_Code_Used = $_POST['discount_code'];
		
		//if ($_POST['mc_gross'] == $_POST['payment_gross']) {
			if ($Payment_Frequency == "One_Time") {
				$Next_Time = time() + (60*60*24*366*100);
				$Next_Payment_Date = '2100-01-01';
			}
			elseif ($Payment_Frequency == "Yearly") {
				$Next_Time =time() + (60*60*24*366);
				$Next_Payment_Date = date("Y-m-d H:i:s", $Next_Time);
			}
			elseif ($Payment_Frequency == "Monthly") {
				$Next_Time =time() + (60*60*24*31);
				$Next_Payment_Date = date("Y-m-d H:i:s", $Next_Time);
			}

			if ($Payment_Types == "Levels") {
				$Level_ID = $_POST['level'];
				$Current_Level_ID = $_POST['current_level_id'];
				$wpdb->query($wpdb->prepare("UPDATE $ewd_feup_user_table_name SET Level_ID=%d WHERE User_ID=%d", $Level_ID, $User_ID));

				$Return_Levels = get_option("EWD_FEUP_Return_Levels");
				$New_Return['User_ID'] = $User_ID;
				$New_Return['Level_ID'] = $Current_Level_ID;
				$New_Return['Return_Time'] = $Next_Time;
				$Return_Levels[] = $New_Return;

				update_option("EWD_FEUP_Return_Levels", $Return_Levels);
			}
 
		// load the stripe libraries
		require_once(EWD_FEUP_CD_PLUGIN_PATH . 'stripe/init.php');
		
		// retrieve the token generated by stripe.js
		$token = $_POST['stripeToken'];

		if ($Pricing_Currency_Code == "JPY") {$Payment_Amount = $_POST['payment_amount'];}
		else {$Payment_Amount = $_POST['payment_amount'] * 100;}
 
		// attempt to charge the customer's card
		if(isset($_POST['recurring']) && $_POST['recurring'] == 'yes') { // process a recurring payment
 
			// recurring payment setup will go here
			try {	
				\Stripe\Stripe::setApiKey($Stripe_Live_Secret);
				$customer = Stripe_Customer::create(array(
						'card' => $token,
						'plan' => $Stripe_Plan_ID
					)
				);	
 				
 				$wpdb->get_results($wpdb->prepare("UPDATE $ewd_feup_user_table_name SET User_Membership_Fees_Paid='Yes', User_Account_Expiry=%s WHERE User_ID=%d", $Next_Payment_Date, $User_ID));
 				Add_EWD_FEUP_Payment($User_ID, $Username, $Payer_ID, $PayPal_Receipt_Number, $Payment_Date, $Next_Payment_Date, $Payment_Amount, $Discount_Code_Used);

				// redirect on successful recurring payment setup
				$redirect = add_query_arg('payment', 'paid', $Thank_You_URL);
 
			} catch (Exception $e) {
				// redirect on failure
				$redirect = add_query_arg('payment', 'failed', $_POST['redirect']);
			}
 
		} else {
			try {
				\Stripe\Stripe::setApiKey($Stripe_Live_Secret);
				$charge = \Stripe\Charge::create(array(
						'amount' => $Payment_Amount, 
						'currency' => strtolower($Pricing_Currency_Code),
						'card' => $token
					)
				);

				$PayPal_Receipt_Number = $charge->id;
				
				$wpdb->get_results($wpdb->prepare("UPDATE $ewd_feup_user_table_name SET User_Membership_Fees_Paid='Yes', User_Account_Expiry=%s WHERE User_ID=%d", $Next_Payment_Date, $User_ID));
				Add_EWD_FEUP_Payment($User_ID, $Username, $Payer_ID, $PayPal_Receipt_Number, $Payment_Date, $Next_Payment_Date, $Payment_Amount, $Discount_Code_Used);
	 
				// redirect on successful payment
				$redirect = add_query_arg('payment', 'paid', $Thank_You_URL);
	 
			} catch (Exception $e) {
				// redirect on failed payment
				$redirect = add_query_arg('payment', 'failed', $_POST['redirect']);
			}
		}
 
		// redirect back to our previous page with the added query variable
		wp_redirect($redirect); exit;
	}
}
add_action('init', 'EWD_FEUP_Stripe_Process_Payment');

?>