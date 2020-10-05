<div id="row">

    <?php include 'settings_menu.php'; ?>

    <div class="col-md-9 col-lg-10">
        <div class="box-shadow">
            <div class="table-head">
                <?= $this->lang->line('application_notifications'); ?>
            </div>

            <div class="table-div">
                <?php
                $attributes = ['class' => '', 'id' => 'notifications'];
                echo form_open_multipart($form_action, $attributes);
                ?>

                <br />

                <div class="form-group">
                    <label><?= $this->lang->line('application_mail_on_overdue'); ?></label>
                    <input type="checkbox" class="checkbox" name="sendmail_on_overdue" data-labelauty="<?= $this->lang->line('application_mail_on_overdue'); ?>" value="1" <?php if ($settings->sendmail_on_overdue) echo 'checked'; ?>>
                </div>

                <div class="form-group">
                    <label><?= $this->lang->line('application_mail_on_overduexperiod'); ?></label>
                    <input type="text" class="form-control" name="sendmail_on_overduexperiod" data-labelauty="<?= $this->lang->line('application_mail_on_overduexperiod'); ?>" value="<?= $settings->sendmail_on_overduexperiod; ?>">
                </div>


                <div class="form-group no-border">
                    <input type="submit" class="btn btn-primary" value="<?= $this->lang->line('application_save'); ?>"><br />
                </div>

                <?php echo form_close(); ?>

            </div>
        </div>