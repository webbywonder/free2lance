<div id="row">

    <?php include 'settings_menu.php'; ?>

    <div class="col-md-9 col-lg-10">
        <div class="box-shadow">
            <div class="table-head">
                <?= $this->lang->line('application_languages'); ?>
            </div>

            <div class="table-div">
                <?php
                $attributes = ['class' => '', 'id' => 'languages'];
                echo form_open_multipart($form_action, $attributes);
                ?>

                <br />

                <div class="form-group no-border">
                    <input type="hidden" value="<?= $lang['__name__']; ?>" name="language">
                    <label><?= $this->lang->line('application_language'); ?>: <?= $lang['__name__'] ?></label>
                </div>

                <?php
                /* Switch language to english so labels are going to be shown in english */
                $this->lang->load('application', 'english');
                foreach ($lang as $langname => $langline) {
                    if ($langname != '__name__') {
                        ?>
                <div class="form-group">
                    <label><?= $this->lang->line($langname); ?></label>
                    <input type="text" name="<?= $langname; ?>" value="<?= $langline; ?>" class="form-control" />
                </div>
                <?php
                    }
                }
                ?>


                <div class="form-group no-border">
                    <input type="submit" class="btn btn-primary" value="<?= $this->lang->line('application_save'); ?>"><br />
                </div>

                <?php echo form_close(); ?>

            </div>
        </div>