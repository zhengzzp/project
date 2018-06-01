<?php

class Lottery_imodel extends MY_Model
{

    var $table = 'lottery';

    var $primary = 'id';

    function __construct()
    {
        parent::__construct();
    }

    public function lists_lottery($id)
    {
        if (! $id) {
            throw new Err('转盘信息不是有效的');
        }
        $this->id = (int) $id;
        $this->db->where('id', $this->id);
        $query = $this->db->get($this->table);
        $data = $query->_fetch_object();
        return $data;
    }

    public function lists($limit)
    {
        $this->db->select('id,name,stime,etime,ctime');
        $this->db->where('state', 'valid');
        if ($limit == 'count') {
            return $this->db->count_all_results($this->table);
        }
        $this->db->limit($limit);
        $this->db->order_by('id');
        $query = $this->db->get($this->table);
        $ret = array();
        while ($row = $query->_fetch_object()) {
            $row->isopen = $this->isopen($row->stime, $row->etime);
            $row = $this->translate($row, 'action');
            $ret[] = $row;
        }
        return $ret;
    }

    public function view_update($id)
    {
        if ($id == null) {
            throw new Err('转盘信息不是有效的');
        }
        $this->id = (int) $id;
        $this->db->where('id', $this->id);
        $query = $this->db->get($this->table);
        $data = $query->_fetch_object();
        return $data;
    }

    public function lists_record($config, $limit)
    {
        if (!$config['id']) {
            throw new Err('获取信息失败');
        }
        $this->lottery_id = (int) $config['id'];
        $default = array(
            'keyword' => '',
            'stime' => '',
            'etime' => ''
        );
        $data = $this->extend($default,$config);
        $this->db->select('lottery_record.prize_grade,lottery_record.name,lottery_record.phone,lottery_record.address,lottery_record.id,lottery_prize.name as prize_name');
        $this->db->join('lottery_prize', 'lottery_record.prize_grade = lottery_prize.prize_grade', 'left');
        $this->db->where('lottery_record.lottery_id', $this->lottery_id);
        $this->db->where('lottery_record.state', 'valid');
        $this->db->where('lottery_prize.lottery_id', $this->lottery_id);
        if ($this->keyword) {
            $this->db->where("(ed_lottery_prize.prize_grade like '%" . $this->keyword ."%' or ed_lottery_record.name like '%" . $this->keyword ."%' or ed_lottery_prize.name like '%" . $this->keyword . "%' or ed_lottery_record.phone like '%" . $this->keyword . "%')");
        }
        if ($this->stime) {
            $this->db->where('ed_lottery_record.ctime >=',strtotime($this->stime));
        }
        if ($this->etime) {
            $this->db->where('ed_lottery_record.ctime <=',strtotime($this->etime));
        }
        if ($limit == 'count') {
            return $this->db->count_all_results('lottery_record');
        }
        $this->db->limit($limit);
        $this->db->order_by('lottery_record.id');
        $query = $this->db->get('lottery_record');
        $ret = array();
        while ($row = $query->_fetch_object()) {
            $ret[] = $row;
        }
        return $ret;
    }
    
    public function activity_data($config,$limit)
    {
        $lottery_id = (int)$config['id'];
        $default = array(
            'stime' => '',
            'etime' => ''
        );
        $data = $this->extend($default,$config);
        $ret = array();
        $res = array();
        $a = $this->redis->zRange('lottery_'. $lottery_id .':count_lottery','0','-1');
        foreach($a as $key => $val){
            if($val != 'all'){
                if ($this->stime) {
                    if(strtotime($this->stime) <= strtotime($val)){
                        $ret[] = $val;
                    }
                }else{
                    $ret[] = $val;
                }
                if ($this->etime) {
                    if(strtotime($this->etime) >= strtotime($val)){
                        if(in_array($val,$ret)){
                            $res[] = $val;
                        }
                    }
                }else{
                    $res = $ret;
                }
            }
        }
        $total = new stdClass();
        $ret = array();
        $get = array();
        $row = array();
        if($limit == 'count'){
            return count($res);
        }
        foreach($res as $k => $v){
            $row[$k] = new stdClass();
            $row[$k]->lottery_date = $res[$k];
            $row[$k]->count_all = $this->redis->zScore('lottery_' . $lottery_id . ':count_lottery',$res[$k]);
            $row[$k]->count_prize_1 = $this->redis->zScore('lottery_' . $lottery_id . ':count_prize_1',$res[$k]);
            $row[$k]->count_prize_2 = $this->redis->zScore('lottery_' . $lottery_id . ':count_prize_2',$res[$k]);
            $row[$k]->count_prize_3 = $this->redis->zScore('lottery_' . $lottery_id . ':count_prize_3',$res[$k]);
            $row[$k]->count_prize_4 = $this->redis->zScore('lottery_' . $lottery_id . ':count_prize_4',$res[$k]);
            $row[$k]->count_prize_5 = $this->redis->zScore('lottery_' . $lottery_id . ':count_prize_5',$res[$k]);
            $row[$k]->count_prize_0 = $this->redis->zScore('lottery_' . $lottery_id . ':count_prize_0',$res[$k]);
            $row[$k]->count_submit = $this->redis->zScore('lottery_' . $lottery_id . ':count_submit',$res[$k]);
            foreach($row[$k] as $key => $val){
                if($row[$k]->$key == ''){
                    $row[$k]->$key = 0;
                }
            }
            $get[$k] = $row[$k];
        }
        if($limit == 'total'){
            return $total;
        }
        $num = explode(',',$limit);
        for($i = $num[0];$i < $num[0] + $num[1];$i++){
            if(isset($get[$i])){
                $ret[] = $get[$i];
            }
        }
        return $ret;
    }

    /*
     * 获取奖品等级以及转盘角度
     * @parms $id 转盘id
     */
    public function get_prize($id, $get = '')
    {
        $this->db->select('id,probability,name,prize_grade,lottery_id,information');
        $this->db->where('lottery_id', $id);
        $this->db->order_by('prize_grade');
        $query = $this->db->get('lottery_prize');
        $res = array();
        if ($get == 'get') {
            while ($row = $query->_fetch_object()) {
                $res[] = $row;
                $res['insert'] = 'update';
            }
            if ($res == array()) {
                for ($i = 0; $i <= 5; $i ++) {
                    $res[$i] = new stdClass();
                    $res[$i]->prize_grade = $i;
                    $res[$i]->name = '';
                    $res[$i]->probability = '';
                    $res[$i]->information = '';
                    $res[$i]->id = '';
                    $res['insert'] = 'insert';
                }
            }
            return $res;
        }
        $p = 0;
        $total = 0;
        while ($row = $query->_fetch_object()) {
            $total += $row->probability;
            $ret[] = $row;
        }
        foreach ($ret as $key => $val) {
            $p += $val->probability;
            $rand = rand(0, $total * 100);
            if ($rand / ($total * 100) <= $p) {
                $grade = $val->prize_grade;
                $r = rand($grade * 60 - 89, $grade * 60 - 31);
                $val->deg = ($r + 360) % 360;
                $this->redis->zIncrBy('lottery_count_all', 1, 'all');
                $this->redis->zIncrBy('lottery_' . $val->lottery_id . ':count_lottery', 1, 'all');
                $this->redis->zIncrBy('lottery_' . $val->lottery_id . ':count_lottery', 1, date('Y-m-d'));
                $this->redis->zIncrBy('lottery_' . $val->lottery_id . ':count_prize_' . $grade, 1, date('Y-m-d'));
                return $val;
            }
        }
    }

    public function translate($val, $config = null, $extend = null)
    {
        if ($config) {
            if ($this->translate_required('action', $config)) {
                $val->action = url_implode(' | ', $this->actions($val, 'lists'));
            }
        }
        return $val;
    }

    public function actions($row, $type)
    {
        $ret = array();
        if ($type == 'lists') {
            $ret[] = $this->acl->anchor('service/lottery/action/view_update', 'class="operate"', array(
                'uri_param' => 'id=' . $row->id
            ));
            $ret[] = $this->acl->anchor('service/lottery/action/set_prize', 'class="operate"', array(
                'uri_param' => 'id=' . $row->id
            ));
            $ret[] = $this->acl->anchor('service/lottery/action/activity_data', 'class="operate"', array(
                'uri_param' => 'id=' . $row->id
            ));
            $ret[] = $this->acl->anchor('service/lottery/action/lists_record', 'class="operate"', array(
                'uri_param' => 'id=' . $row->id
            ));
            $ret[] = $this->acl->anchor('service/lottery/action/ajax_delete', 'class="operate ajax_delete" jid="' . $row->id . '"', array(
                'uri_js' => true
            ));
            $ret[] = $this->acl->anchor('service/lottery/action/lists_lottery', 'class="operate"', array(
                'uri_param' => 'id=' . $row->id
            ));
        }
        return $ret;
    }

    public function isopen($stime, $etime)
    {
        $year = date('Y');
        if (! strtotime($stime)) {
            $stime = $year . '-' . $stime;
            $etime = $year . '-' . $etime;
        }
        $stime = strtotime($stime);
        $etime = strtotime($etime);
        if ($stime <= time() && $etime >= time()) {
            return '是';
        } else {
            return '否';
        }
    }
}