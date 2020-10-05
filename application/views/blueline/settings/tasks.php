<div id="row">

	<?php include 'settings_menu.php'; ?>

	<div class="col-md-9 col-lg-10">
		<div class="box-shadow">
		<div class="table-head">
			<?=$this->lang->line('application_tasks');?>
		</div>
		<div class="table-div">
			<?php 
        $attributes = ['class' => '', 'id' => 'paypal'];
        echo form_open_multipart($form_action, $attributes);
        ?>
			<br>
			<div class="form-group">
				<label>
					<?=$this->lang->line('application_send_email_when_task_completed');?>
				</label>
				<input name="sendmail_on_taskcomplete" type="checkbox" class="checkbox" style="width:100%;" data-labelauty="<?=$this->lang->line('application_send_email_when_task_completed');?>"
				 value="1" <?php if ($settings->sendmail_on_taskcomplete) {
            ?> checked="checked"
				<?php
        } ?>>

			</div>

			<div class="form-group">
				<label>
					<?=$this->lang->line('application_send_email_when_task_assigned');?>
				</label>
				<input name="sendmail_on_taskassign" type="checkbox" class="checkbox" style="width:100%;" data-labelauty="<?=$this->lang->line('application_send_email_when_task_assigned');?>"
				 value="1" <?php if ($settings->sendmail_on_taskcomplete) {
            ?> checked="checked"
				<?php
        } ?>>

			</div>

				<div class="form-group no-border">

					<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>" />
				</div>

				<?php echo form_close(); ?>

		</div>
	</div>
	</div>