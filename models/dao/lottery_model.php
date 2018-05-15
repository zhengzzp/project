<?php
class Lottery_model extends MY_Model
{
    var $table = 'lottery';
    var $primary = 'id';

    function __construct()
    {
        parent::__construct();
    }
    
    public function ajax_insert($config)
    {
        $default = array(
        'name' => '',
        'phone' => '',
        'address' => '',
        'prize_grade' => '',
        'lottery_id' => '');
        $data = $this->extend($default,$config);
        if (!$this->name) {
            throw new Err('姓名不能为空');
        }
        if (!$this->phone) {
            throw new Err('手机号码不能为空');
        }
        if (!preg_match("/^1[345678]\d{9}$/",$this->phone)) {
            throw new Err('手机号码格式不正确');
        }
        $data['ctime'] = time();
        $data['state'] = 'valid';
        $this->db->insert('lottery_record',$data);
    }
}