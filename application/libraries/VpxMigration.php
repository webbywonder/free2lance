<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Vpx Migration Library
 *
 * Create a base file for migrations to start off with;
 *
 * @author Liaan vd Merwe <info@vpx.co.za>
 * @license Free to use and abuse
 * @version 0.4 Beta
 *
 */
class VpxMigration
{
    public $db_user;
    public $db_pass;
    public $db_host;
    public $db_name;
    public $email;
    public $tables = '*';
    public $newline = '\n';
    public $write_file = true;
    public $file_name = '';
    public $file_per_table = true;
    public $path = 'migrations';
    public $skip_tables = [];
    public $add_view = false;

    /*
     * defaults;
     */

    public function __construct($params = null)
    {
        // parent::__construct();
        // isset($this->ci) or $this->ci = get_instance();

        $this->db_user = 'fc-dev';
        $this->db_pass = 'fc';
        $this->db_host = 'mysql';
        $this->db_name = 'fc-dev';
        $this->path = APPPATH . $this->path;
        if ($params) {
            $this->init_config($params);
        }
    }

    /**
     * Init Config if there is any passed
     *
     *
     * @param type $params
     */
    public function init_config($params = [])
    { //apply config
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * Generate the file.
     *
     * @param string $tables
     * @return boolean|string
     */
    public function generate($tables = null)
    {
        if ($tables) {
            $this->tables = $tables;
        }

        $return = '';
        /* open file */
        if ($this->write_file) {
            if (!is_dir($this->path) or !is_really_writable($this->path)) {
                $msg = 'Unable to write migration file: ' . $this->path;
                log_message('error', $msg);
                echo $msg;
                return;
            }

            // if (!$this->file_per_table) {
            //     $file_path = $this->path . '/' . $this->file_name . '.sql';
            //     $file = fopen($file_path, 'w+');

            //     if (!$file) {
            //         $msg = 'no file';
            //         log_message('error', $msg);
            //         echo $msg;
            //         return false;
            //     }
            // }
        }

        // if default, then run all tables, otherwise just do the list provided
        if ($this->tables == '*') {
            //$query = $this->ci->db_master->query('SHOW full TABLES FROM ' . $this->ci->db_master->protect_identifiers($this->ci->db_master->database));
            $query = Setting::find_by_sql('SHOW full TABLES');

            $retval = [];

            if (count($query) > 0) {
                foreach ($query as $row) {
                    $tablename = 'tables_in_' . $this->db_name;

                    if (isset($row->$tablename)) {
                        /* check if table in skip arrays, if so, go next */
                        if (in_array($row->$tablename, $this->skip_tables)) {
                            continue;
                        }

                        /* check if views to be migrated */
                        if ($this->add_view) {
                            ## not implemented ##
                            //$retval[] = $row[$tablename];
                        } else {
                            /* skip views */
                            if (strtolower($row->table_type) == 'view') {
                                continue;
                            }
                            $retval[] = $row->$tablename;
                        }
                    }
                }
            }

            $this->tables = [];
            $this->tables = $retval;
        } else {
            $this->tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        ## if write file, check if we can
        if ($this->write_file) {
            /* make subdir */
            $path = $this->path . '/' . $this->file_name;

            if (!@is_dir($path)) {
                if (!@mkdir($path, DIR_WRITE_MODE, true)) {
                    return false;
                }

                @chmod($path, DIR_WRITE_MODE);
            }

            if (!is_dir($path) or !is_really_writable($path)) {
                $msg = 'Unable to write backup per table file: ' . $path;
                log_message('error', $msg);

                return;
            }

            // $file_path = $path . '/001_create_' . $table . '.php';
            // //$file_path = $path . '/001_create_base.php';
            // $file = fopen($file_path, 'w+');

            // if (!$file) {
            //     $msg = 'No File';
            //     log_message('error', $msg);
            //     echo $msg;

            //     return false;
            // }
        }

        //loop through tables
        $i = 0;
        foreach ($this->tables as $table) {
            $up = '';
            $down = '';
            //log_message('debug', print_r($table, true));

            // $q = $this->ci->db_master->query('describe ' . $this->ci->db_master->protect_identifiers($this->ci->db_master->database . '.' . $table));
            $q = Setting::find_by_sql('describe ' . $table);
            // No result means the table name was invalid
            if ($q === false) {
                continue;
            }

            $columns = $q;

            // $q = $this->ci->db_master->query(' SHOW TABLE STATUS WHERE Name = \'' . $table . '\'');
            $q = Setting::find_by_sql(' SHOW TABLE STATUS WHERE Name = \'' . $table . '\'');
            $engines = $q;

            $up .= "\n\t\t" . '## Create Table ' . $table . "\n";
            foreach ($columns as $column) {
                $up .= "\t\t" . '$this->dbforge->add_field("' . "`$column->field` $column->type " . ($column->null == 'NO' ? 'NOT NULL' : 'NULL') .
                        (
                        #  if its timestamp column, don't '' around default value .... crap way, but should work for now
                        $column->default ? ' DEFAULT ' . ($column->type == 'timestamp' ? $column->default : '\'' . $column->default . '\'') : ''
                        )
                        . " $column->extra\");" . "\n";

                if ($column->key == 'PRI') {
                    $up .= "\t\t" . '$this->dbforge->add_key("' . $column->field . '",true);' . "\n";
                }
            }
            $up .= "\t\t" . '$this->dbforge->create_table("' . $table . '", TRUE);' . "\n";
            if (isset($engines->engine) and $engines->engine) {
                $up .= "\t\t" . '$this->db->query(\'ALTER TABLE  ' . $table . ' ENGINE = ' . $engines->engine . '\');';
            }

            $down .= "\t\t" . '### Drop table ' . $table . ' ##' . "\n";
            $down .= "\t\t" . '$this->dbforge->drop_table("' . $table . '", TRUE);' . "\n";

            ### generate the text ##
            $return = '<?php ';
            $return .= 'defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');' . "\n\n";
            $return .= 'class Migration_Create_' . $table . ' extends CI_Migration {' . "\n";
            $return .= "\n\t" . 'public function up() {' . "\n";

            $return .= $up;
            $return .= "\n\t" . ' }' . "\n";

            $return .= "\n\t" . 'public function down()';
            $return .= "\t" . '{' . "\n";
            $return .= $down . "\n";
            $return .= "\t" . '}' . "\n" . '}';

            ## write the file, or simply return if write_file false
            if ($this->write_file) {
                $i = $i + 1;
                $num = sprintf('%03d', $i);
                $file_path = $this->path . '/' . $num . '_create_' . $table . '.php';
                $file = fopen($file_path, 'w+');
                fwrite($file, $return);
                fclose($file);
                echo 'Create file migration with success!';
            }
        }
        die();
    }
}
