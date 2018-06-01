<?php

class Lottery_model extends MY_Model
{

    var $table = 'lottery';

    var $primary = 'id';

    function __construct()
    {
        parent::__construct();
    }

    public function ajax_insert_record($config)
    {
        $default = array(
            'name' => '',
            'phone' => '',
            'address' => '',
            'prize_grade' => '',
            'lottery_id' => ''
        );
        $data = $this->extend($default, $config);
        if (! $this->name) {
            throw new Err('姓名不能为空');
        }
        if (! $this->phone) {
            throw new Err('手机号码不能为空');
        }
        if (! preg_match("/^1[345678]\d{9}$/", $this->phone)) {
            throw new Err('手机号码格式不正确');
        }
        $data['ctime'] = time();
        $data['state'] = 'valid';
        $this->db->insert('lottery_record', $data);
        if ($this->db->affected_rows() > 0) {
            $this->log('添加中奖者信息成功');
            $this->redis->zIncrBy('lottery_'. $this->lottery_id . ':count_submit',1,date('Y-m-d'));
        }
    }

    public function insert_do($config)
    {
        $default = array(
            'name' => '',
            'stime' => '',
            'etime' => ''
        );
        $data = $this->extend($default, $config);
        if (! $this->name) {
            throw new Err('转盘名称不能为空');
        }
        $data['ctime'] = time();
        $data['state'] = 'valid';
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            $this->set_message('添加转盘成功');
            $this->log('添加转盘信息成功');
        }
    }

    public function update_do($config)
    {
        $default = array(
            'name' => '',
            'stime' => '',
            'etime' => ''
        );
        $data = $this->extend($default, $config);
        $this->id = (int) $config['id'];
        if (! $this->id) {
            throw new Err('转盘信息不是有效的');
        }
        if (! $this->name) {
            throw new Err('转盘标题不能为空');
        }
        $this->db->where('id', $this->id);
        $this->db->update($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            $this->set_message('修改转盘信息成功');
            $this->log('修改转盘信息成功' . $this->id);
        } else {
            $this->set_message('转盘信息未修改');
        }
    }

    public function set_prize_do($id, $config, $insert)
    {
        $this->id = (int) $id;
        $data_list = array();
        $ret = array();
        $default = array(
            'prize_grade' => '',
            'name' => '',
            'probability' => '',
            'information' => ''
        );
        $data = $this->extend($default, $config);
        foreach ($data as $key => $val) {
            for ($i = 0; $i <= 5; $i ++) {
                if ($data[$key][$i] == '' && $key != 'id') {
                    throw new Err('lottery_' . $key . '_invalid');
                }
                if (in_array($data['prize_grade'][$i], $ret)) {
                    throw new Err('lottery_prize_grade_same_invalid');
                }
                $ret[] = $data['prize_grade'][$i];
                $data_list[$i][$key] = $data[$key][$i];
                $data_list[$i]['state'] = 'valid';
                if ($insert == 'insert') {
                    $data_list[$i]['ctime'] = time();
                    $data_list[$i]['lottery_id'] = $this->id;
                }
                if ($insert == 'update') {
                    $data_list[$i]['id'] = $config['id'][$i];
                    $data_list[$i]['mtime'] = time();
                }
            }
        }
        if ($insert == 'insert') {
            $this->db->insert_batch('lottery_prize', $data_list);
            if ($this->db->affected_rows() > 0) {
                $this->log('新增转盘奖项lottery_id:' . $this->id);
                $this->set_message('lottery_prize_insert_success');
            }
        }
        if ($insert == 'update') {
            $this->db->update_batch('lottery_prize', $data_list, 'id');
            if ($this->db->affected_rows() > 0) {
                $this->log('修改转盘奖项lottery_id:' . $this->id);
                $this->set_message('lottery_prize_update_success');
            }
        }
    }

    public function ajax_delete($id)
    {
        if ($id == null) {
            throw new Err('删除失败');
        }
        $this->id = (int) $id;
        $this->db->where('id', $this->id);
        $this->db->set('state', 'invalid');
        $this->db->update($this->table);
        if ($this->db->affected_rows() > 0) {
            $this->set_message('删除转盘成功');
            $this->log('删除转盘信息.$this->id');
        } else {
            $this->set_message('转盘已删除');
        }
        $this->db->where('lottery_id', $this->id);
        $this->db->set('state', 'invalid');
        $this->db->update('lottery_prize');
        if ($this->db->affected_rows() > 0) {
            $this->log('删除转盘奖项.$this->id');
        }
    }
}