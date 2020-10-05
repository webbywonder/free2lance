<?php 
$fields = array(


                        'money_format' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'default' => 1,
                                                ),
                        'money_currency_position' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'default' => 1,
                                                ),
                        'pdf_font' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => 'NotoSans',
                                                ),
                        'pdf_path' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '10',
                                                 'unsigned' => TRUE,
                                                 'default' => 1,
                                                ),
                        'registration' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '10',
                                                 'unsigned' => TRUE,
                                                 'default' => 0,
                                                ),
                        
                  
);

$columns = Setting::table()->columns;
  if(!array_key_exists("money_format", $columns)){ 
$this->dbforge->add_column('core', $fields);
}

$fields = array(
                        'second_tax' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '0',
                                                ),
                        'estimate_reference' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
);

      $columns = Invoice::table()->columns;
  if(!array_key_exists("second_tax", $columns)){ 
$this->dbforge->add_column('invoices', $fields);
}
$fields = array(
                        'second_tax' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '0',
                                                )

);


$columns = Subscription::table()->columns;
  if(!array_key_exists("second_tax", $columns)){ 
$this->dbforge->add_column('subscriptions', $fields);
}

$fields = array(
                        'description' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )

);


$columns = Item::table()->columns;
  if(!array_key_exists("description", $columns)){ 
$this->dbforge->add_column('items', $fields);
}

$fields = array(
                        'hashed_password' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
);


$columns = Client::table()->columns;
if(!array_key_exists("hashed_password", $columns)){ 
	$this->dbforge->add_column('clients', $fields);
}

$fields = array(
                        'password' => array(
                                                         'name' => 'password_old',
                                                         'type' => 'VARCHAR',
                                                         'constraint' => '255',
		                                                 'null' => TRUE,
                                                 		 'default' => ''
                                                ),
);
if(!array_key_exists("password_old", $columns)){ 
	$this->dbforge->modify_column('clients', $fields);

	$clients = Client::all();
		foreach ($clients as $client) {
			$pass = $client->password_old; 
			$client->password = $client->set_password($pass);
			$client->save();		
		}
}



$invoices = Invoice::find('all', array('conditions' => array('estimate != ?', 1)));
$settings = Setting::first();
		foreach ($invoices as $invoice) {
		
		$items = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$invoice->id)));

		//calculate sum
		$i = 0; $sum = 0;
		foreach ($items as $value){
			$sum = $sum+$invoice->invoice_has_items[$i]->amount*$invoice->invoice_has_items[$i]->value; $i++;
		}
		if(substr($invoice->discount, -1) == "%"){ 
			$discount = sprintf("%01.2f", round(($sum/100)*substr($invoice->discount, 0, -1), 2)); 
		}
		else{
			$discount = $invoice->discount;
		}
		$sum = $sum-$discount;

		if($invoice->tax != ""){
			$tax_value = $invoice->tax;
		}else{
			$tax_value = $settings->tax;
		}

		$tax = sprintf("%01.2f", round(($sum/100)*$tax_value, 2));
		$sum = sprintf("%01.2f", round($sum+$tax, 2));

		$invoice->sum = $sum;
		$invoice->save();

		}