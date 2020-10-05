<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_loginsessions extends CI_Migration
{
    public function up()
    {
        ## Create Table loginsessions
        $this->dbforge->add_field('`id` int(11) NOT NULL auto_increment primary key');
        //$this->dbforge->add_key('id', true);
        $this->dbforge->add_field('`hash` varchar(128) NOT NULL ');
        $this->dbforge->add_field('`agent` int(11) UNSIGNED NOT NULL ');
        $this->dbforge->add_field('`expires` DATETIME NOT NULL ');
        $this->dbforge->create_table('loginsessions', true);
    }

    public function down()
    {
        ### Drop table users ##
        $this->dbforge->drop_table('loginsessions', true);
    }
}
