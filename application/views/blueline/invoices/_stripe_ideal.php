<?php
$attributes = array('class' => '', 'id' => 'payment-form');
echo form_open($form_action, $attributes);
?>
<input type="hidden" name="id" value="<?= $invoices->id; ?>">
<input type="hidden" class="redirect" value="<?= base_url() ?><?= (is_object($this->client)) ? "c" : ""; ?>invoices/idealpay/<?= $invoices->id; ?>">
<input type="hidden" name="type" value="ideal">

<div id="source-updates">
</div>


<div class="form-group stripe-form-group">
    <label for="name">
        <?= $this->lang->line('application_fullname'); ?> *
    </label>
    <input id="name" name="name" class="required form-control" placeholder="Jenny Rosen" required>
</div>

<div class="form-row form-group">
    <label for="ideal-bank-element">
        iDEAL Bank
    </label>
    <div id="ideal-bank-element">
        <!-- A Stripe Element will be inserted here. -->
    </div>
</div>

<div class="form-group">
    <label for="value"><?= $this->lang->line('application_payment'); ?> *</label>
    <input id="value" type="text" name="sum" class="required form-control decimal sum" value="<?= $sum; ?>" required />
</div>


<!-- Used to display form errors. -->
<div id="error-message" role="alert"></div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="submitBtn"><?= $this->lang->line('application_send'); ?></button>
    <a class="btn" data-dismiss="modal"><?= $this->lang->line('application_close'); ?></a>
</div>


<?php echo form_close(); ?>



<script type="text/javascript">
    var stripe = Stripe('<?php echo $public_key; ?>');

    // Create an instance of Elements.
    var elements = stripe.elements();

    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            padding: '10px 12px',
            color: '#000',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '14px',
            '::placeholder': {
                color: '#a4a5a9'
            },
        },
        invalid: {
            color: '#fa755a',
        }
    };

    // Create an instance of the idealBank Element.
    var idealBank = elements.create('idealBank', {
        style: style
    });

    // Add an instance of the idealBank Element into the `ideal-bank-element` <div>.
    idealBank.mount('#ideal-bank-element');

    var errorMessage = document.getElementById('error-message');

    // Handle form submission.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        // showLoading();
        let amountsplit = document.querySelector('input[name="sum"]').value.split('.')
        amount = parseInt(amountsplit[0]) * 100
        amount = amountsplit[1] ? amount + parseInt(amountsplit[1]) : amount

        let currency = '<?= $invoices->currency == '€' || $invoices->currency == 'EUR' ? 'eur' : 'usd' ?>'
        var sourceData = {
            type: 'ideal',
            amount: amount,
            currency: currency,
            owner: {
                name: document.querySelector('input[name="name"]').value,
            },
            // Specify the URL to which the customer should be redirected
            // after paying.
            redirect: {
                return_url: '<?= base_url() ?><?= (is_object($this->client)) ? "c" : ""; ?>invoices/idealpay/<?= $invoices->id; ?>/' + amount,
            },
        };

        // Call `stripe.createSource` with the idealBank Element and additional options.
        stripe.createSource(idealBank, sourceData).then(function(result) {
            if (result.error) {
                // Inform the customer that there was an error.
                errorMessage.textContent = result.error.message;
                errorMessage.classList.add('visible');
                stopLoading();
            } else {
                // Redirect the customer to the authorization URL.
                errorMessage.classList.remove('visible');
                stripeSourceHandler(result.source);
            }
        });

        function stripeSourceHandler(source) {
            // Redirect the customer to the authorization URL.
            document.location.href = source.redirect.url;
        }
    });
</script>


<style>
    input,
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
    }

    .stripe-form-group {
        overflow: hidden;
    }


    input:focus,
    .StripeElement--focus {}

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    #error-message {
        color: #fa755a;
        font-weight: bold;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>