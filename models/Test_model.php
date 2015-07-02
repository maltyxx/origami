<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    
        // Dépendance
        $this->load->library('origami');
    }
    
    public function add()
    {
        $user = new \Entity\test\user();
        $user->firstname = 'John';
        $user->lastname = 'Do';
        $user->password = sha1('JohnDo');
        
        return $user->save();
    }
    
    public function add_user_address()
    {
        $user_entity = new \Entity\test\user();
        $user = $user_entity->find_one();
         
        $address = new \Entity\test\address();
        $address->user_id = $user->id;
        $address->street = '1 Promenade des Anglais';
        
        return $address->save();
    }
    
     public function add_user_file()
    {
        $user_entity = new \Entity\test\user();
        $user = $user_entity->find_one();
         
        $file = new \Entity\test\file();
        $file->user_id = $user->id;
        $file->type = 'png';
        $file->content = base64_encode(file_get_contents('https://www.google.fr/images/srpr/logo11w.png'));
        
        return $file->save();
    }
        
    public function get()
    {
        $user = new \Entity\test\user();
        return $user->find_one();
    }
    
    public function get_user_address()
    {
        $user_entity = new \Entity\test\user();
        
        $user = $user_entity->find_one();
        
        return $user->address()->find_one();
    }
    
    public function get_user_file()
    {
        $file_entity = new \Entity\test\file();
        
        $file = $file_entity->find_one();
        
        return $file;
    }
    
    public function set()
    {
        $user_entity = new \Entity\test\user();
        
        $user = $user_entity->find_one();
        $user->firstname = 'John';
        
        return $user->save();
    }

    public function del()
    {
        $user_entity = new \Entity\test\user();
        
        $user = $user_entity->find_one();
        
        return $user->remove();
    }
    
}