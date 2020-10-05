<?php

class Loginsession extends ActiveRecord\Model {
    public static function genHashForAgent($useragent)
    {
        if ($useragent) {
            $entry = Loginsession::find('first', array('conditions', array('agent = ' . $useragent->id)));
            if ($entry) {
                $entry->hash = md5($useragent->id . $useragent->email . date('Y-m-d H:i:s'));
                $entry->expires = time() + 60 * 60 * 24;
                $entry->save();
            } else {
                $data = [
                    'agent' => $useragent->id,
                    'hash' => md5($useragent->id . $useragent->email . date('Y-m-d H:i:s')),
                    'expires' => time() + 60 * 60 * 24
                ];

                $entry = Loginsession::create($data);
            }

            return $entry->hash;
        }

        return false;
    }
}
