<?php 
$attributes = ['class' => '', 'id' => 'user_form'];
echo form_open_multipart($form_action, $attributes);
?>

<div class="form-group">
        <label for="username"><?= $this->lang->line('application_name'); ?></label>
        <input id="username" class="form-control" type="text" value="<?= $user->firstname;?> <?= $user->lastname;?>" readonly/>
</div>

<div class="form-group">
        <label for="usertitle"><?= $this->lang->line('application_title'); ?></label>
        <input id="usertitle" class="form-control" type="text" value="<?= $user->title;?>" readonly/>
</div>

<div class="modal-footer">
    <a class="btn btn-primary" data-toggle="mainmodal" href="<?=base_url();?>messages/write?tu=<?=$user->id;?>"><?=$this->lang->line('application_message');?></a>
    <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>