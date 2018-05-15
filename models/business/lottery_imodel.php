<?php
class Lottery_imodel extends MY_Model
{
	var $table = 'lottery';
	var $primary = 'id';

	function __construct()
	{
		parent::__construct();
	}
	
	public function lists($config)
	{
	    if ($config == 'state') {
	        $this->db->select('id,name,url,stime,etime');
	        $this->db->where('state','valid');
	        $query = $this->db->get($this->table);
	        $year = date('Y');
	        while($row = $query->_fetch_object()) {
	            $row->stime = $year . '-' .$row->stime;
	            $row->etime = $year . '-' .$row->etime;
	            $stime = strtotime($row->stime);
	            $etime = strtotime($row->etime);
	            if(time() >= $stime && time() <= $etime) {
	                return $row;
	            }
	        }
	    }
	    
	}
	
    /* 获取奖品等级以及转盘角度
     * @parms $id 转盘id
     */
	public function get_prize($id)
	{
	    $this->db->select('probability,name,prize_grade,lottery_id,message');
	    $this->db->where('lottery_id',$id);
	    $query = $this->db->get('lottery_prize');
	    $p = 0;
	    $total = 0;
	    while($row = $query->_fetch_object()){
	        $total += $row->probability;
	        $ret[] = $row;
	    }
	    foreach($ret as $key => $val){
	        $p += $val->probability;
	        $rand = rand(0,$total*100);
	        if ($rand/($total*100) <= $p) {
	            $grade = $val->prize_grade;
	            $r = rand($grade * 60 - 89,$grade * 60 - 31);
	            $val->deg = ($r + 360) % 360;
	            return $val;
	        }
	    }
	}
	
}