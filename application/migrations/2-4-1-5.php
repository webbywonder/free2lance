<?php $invoices = Invoice::find('all', array('conditions' => array('estimate != ?', 0)));
            foreach ($invoices as $invoice) {
            	if($invoice->estimate_reference == 0 ){
                	  $invoice->estimate_reference = $invoice->reference;
                  	  $invoice->save();
            		}
            }

