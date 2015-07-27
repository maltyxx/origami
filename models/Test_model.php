<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();

        // Dépendance
        $this->load->library('origami', array(
            'entity_autoload' => TRUE,
            'entity_path' => APPPATH.'third_party/origami/models/Entity',
            'binary_enable' => TRUE,
            'encryption_enable' => TRUE,
            'encryption_key' => bin2hex('Origami')
        ));
    }

    public function add()
    {
        \Origami\DB::get('test')->trans_start();

        $user = new \Entity\test\user();
        $user->firstname = 'John';
        $user->lastname = 'Do';
        $user->password = sha1('JohnDo');
        $user->birth = new DateTime('17-04-1984');
        $user->dateinsert = new DateTime();
        $user->dateupdate = new DateTime();
        $user->save();
        
        \Origami\DB::get('test')->trans_complete();
        
        return \Origami\DB::get('test')->trans_status();
    }

    public function add_user_address()
    {
        $user = \Entity\test\user::find_one();

        $address = new \Entity\test\address();
        $address->user_id = $user->id;
        $address->street = '1 Promenade des Anglais';

        return $address->save();
    }

     public function add_user_file()
    {
        $user = \Entity\test\user::find_one();

        $file = new \Entity\test\file();
        $file->user_id = $user->id;
        $file->type = 'png';
        $file->content = base64_encode(file_get_contents('https://www.google.fr/images/srpr/logo11w.png'));

        return $file->save();
    }

    public function get()
    {
        $user = \Entity\test\user::find_one();
        $user = new \Entity\test\user($user->id);
        
        return $user->toArray();
    }

    public function get_join()
    {
        $user = \Entity\test\user::find_one();

        $result = $user
            ->join(\Entity\test\file::entity())
            ->join(\Entity\test\address::entity())
            ->find_one();

        return $result->toArray();
    }

    public function get_user_address()
    {
        $user = \Entity\test\user::find_one();
        
        $address = $user->address()->find_one();

        return $address->toArray();
    }

    public function get_user_file()
    {
        $file = \Entity\test\file::find_one();
        
        return $file->toArray();
    }

    public function set()
    {
        $user = \Entity\test\user::find_one();
        $user->firstname = 'John';

        return $user->save();
    }

    public function del()
    {
        $user = \Entity\test\user::find_one();

        return $user->remove();
    }

}