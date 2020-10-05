<?php
$attributes = array('class' => '', 'id' => 'payment-form');
echo form_open($form_action, $attributes);
?>
<input type="hidden" name="id" value="<?= $invoices->id; ?>">
<input type="hidden" name="type" value="card">

<div class="form-group stripe-form-group">
    <label for="card-element">
        Credit or debit card
    </label>
    <div id="card-element">
        <!-- A Stripe Element will be inserted here. -->
    </div>

    <!-- Used to display form errors. -->
    <div id="card-errors" role="alert"></div>
</div>

<div class="form-group">
    <label for="value"><?= $this->lang->line('application_payment'); ?> *</label>
    <input id="value" id="payment_sum" type="text" name="sum" class="required form-control number money-format" value="<?= $sum; ?>" required />
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="submitBtn"><?= $this->lang->line('application_send'); ?></button>
    <a class="btn" data-dismiss="modal"><?= $this->lang->line('application_close'); ?></a>
</div>

<?php echo form_close(); ?>

<style>
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
    }

    .stripe-form-group {
        overflow: hidden;
    }

    .StripeElement--focus {
        background: #f0f0f0;
        box-shadow: 0 0px 0px 45px #f0f0f0;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

    #card-errors {
        color: #fa755a;
        padding-left: 5px;
    }
</style>

<script type="text/javascript">
    // Create a Stripe client.
    var stripe = Stripe('<?php echo $public_key; ?>');

    // Create an instance of Elements.
    var elements = stripe.elements();

    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#000',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '14px',
            '::placeholder': {
                color: '#a4a5a9'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element.
    var card = elements.create('card', {
        style: style
    });

    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission.
    var form = document.getElementById('payment-form');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createPaymentMethod(
            'card',
            card
        ).then(function(result) {
            if (result.error) {
                // Show error in payment form
            } else {

                // Send paymentMethod.id to server
                let formData = new FormData();
                formData.append('id', '<?= $invoices->id; ?>');
                formData.append('type', 'card');
                formData.append('sum', document.querySelector('input[name="sum"]').value);
                formData.append('fcs_csrf_token', '<?= $csrf ?>');
                formData.append('payment_method_id', result.paymentMethod.id);
                fetch('/<?= $form_action ?>', {
                    method: 'POST',

                    body: formData
                }).then(function(result) {
                    // Handle server response (see Step 3)
                    result.json().then(function(json) {
                        handleServerResponse(json);
                    })
                });
            }
        });
    });

    function handleServerResponse(response) {
        if (response.error) {
            // Show error from server on payment form
            alert(response.error);
        } else if (response.requires_action) {
            // Use Stripe.js to handle required card action
            handleAction(response);
        } else {
            // Show success message
            location.reload();
        }
    }

    function handleAction(response) {
        stripe.handleCardAction(
            response.payment_intent_client_secret
        ).then(function(result) {
            if (result.error) {
                // Show error in payment form
            } else {
                // The card action has been handled
                // The PaymentIntent can be confirmed again on the server
                let formData = new FormData();
                formData.append('id', '<?= $invoices->id; ?>');
                formData.append('type', 'card');
                formData.append('sum', <?= $sum; ?>);
                formData.append('fcs_csrf_token', '<?= $csrf ?>');
                formData.append('payment_intent_id', result.paymentIntent.id);
                fetch('/<?= $form_action ?>', {
                    method: 'POST',

                    body: formData
                }).then(function(confirmResult) {
                    return confirmResult.json();
                }).then(handleServerResponse);
            }
        });
    }

    // Submit the form with the token ID.
    // function stripeTokenHandler(token) {
    //     // Insert the token ID into the form so it gets submitted to the server
    //     var form = document.getElementById('payment-form');
    //     var hiddenInput = document.createElement('input');
    //     hiddenInput.setAttribute('type', 'hidden');
    //     hiddenInput.setAttribute('name', 'stripeToken');
    //     hiddenInput.setAttribute('value', token.id);
    //     form.appendChild(hiddenInput);

    //     // Submit the form
    //     form.submit();
    // }
</script>