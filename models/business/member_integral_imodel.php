<?php
class Member_integral_imodel extends MY_Model
{
	var $table = 'member_integral';

	function __construct()
	{
		parent::__construct();
		$this->lang->load('model/member_integral');
	}
	
	public function list_collect($limit,$config = array())
	{
	    $default = array(
	        'keyword' = '',
	        'source_type' = '',
	        'stime' = '',
	        'etime' = '')
	    $data = extend($default,$config);
	    $this->db->select('sum(ed_member_integral.integral) as integral_str,member.card_id,member.fullname,member.mobile_phone,store.name');
	    $this->db->join('member','member_integral.member_id = member.id','left');
	    $this->db->join('store','member.store_id = store.id','left');
	    $this->db->group_by('member_integral.member_id')
	    if ($this->keyword) {
	        $this->db->where('ed_member_card_id like "%'. $keyword .'%" and ed_member.fullname like "%' . $keyword . '%" and ed_member.mobile_phone like "%' . $keyword .'%" and ed_store.name like "%' . $keyword .'%"');
	    }
	    if ($this->source_type) {
	        $keyword = trim($this->source_type);
	        $this->db->where("(ed_member_integral.remark like '%" . $keyword . "%')");
	    }
	    if ($this->stime) {
	        $this->db->where('ed_member_integral.ctime >=',strtotime($this->stime . '00:00:00'));
	    }
	    if ($this->etime) {
	        $this->db->where('ed_member_intergral.ctime <=',strtotime($this->etime . '23:59:59'));
	    }
	    if ($limit = 'count') {
	        return $this->db->count_all_results($this->table);
	    }
	    $this->db->order_by('member_integral.member_id');
	    $this->db->limit($limit);
	    $query = $this->db->get($this->table);
	    while($row = $query->_fetch_object()){
	        $ret[] = $this->translate($row,'action_lists_collect');
	    }
	    return $ret;
	}

	public function lists_collect_detail($limit, $config = array())
	{
		$default = array(
				'member_id' => '', 
				'stime' => '', 
				'etime' => '', 
				'source_type' => '');
		$this->extend($default, $config);
		$this->db->join('member', 'member.id = member_integral.member_id', 'left');
		$this->db->where('member_integral.source', 'sys');
		$this->db->where('member_integral.state', 'valid');
		$this->db->where('member_integral.member_id', $this->member_id);
		if ($this->stime) {
			$this->db->where('member_integral.ctime >=', strtotime($this->stime . ' 00:00:00'));
		}
		if ($this->etime) {
			$this->db->where('member_integral.ctime <=', strtotime($this->etime . ' 23:59:59'));
		}
		if ($this->source_type) {
			$keyword = trim($this->source_type);
			$this->db->where("(ed_member_integral.remark like '%" . $keyword . "%')", null, false);
		}
		if ($limit == 'count') {
			return $this->fetch_count();
		}
		if ($limit == 'total_all') {
			$this->db->select('sum(ed_member_integral.integral) as integral');
			$row = $this->fetch_row();
			$ret = new stdClass();
			$ret->integral = $row->integral;
			$this->translate($ret, 'integral');
			return $ret;
		}
		$this->db->select('member_integral.member_id,member_integral.integral,member_integral.type,member_integral.remark,,member_integral.integral_total,member_integral.ctime');
		$this->db->select('member.card_id,member.fullname,member.mobile_phone');
		$this->db->order_by('member_integral.ctime', 'desc');
		$this->db->limit($limit);
		$query = $this->db->get('member_integral');
		$total = new stdClass();
		$total->integral = 0;
		$ret = array();
		while($row = $query->_fetch_object()) {
			$total->integral += $row->integral;
			$ret[] = $this->translate($row, 'integral,integral_total,ctime');
		}
		$this->set_total($total);
		$this->translate($total, 'integral');
		return $ret;
	}

	protected function set_member_id($val)
	{
		$val = (int)$val;
		$this->member_id = $val;
	}

	protected function set_store_list()
	{
		$this->db->select('store.id,store.name');
		$this->db->select('province.chinese as province_name');
		$this->db->select('city.chinese as city_name');
		$this->db->from('store');
		$this->db->join('province', 'province.id = store.province_id', 'left');
		$this->db->join('city', 'city.id = store.city_id and ed_city.province_id = ed_store.province_id', 'left');
		$query = $this->db->get();
		$ret = array();
		while($row = $query->_fetch_object()) {
			$ret[$row->id] = $row;
		}
		$this->store_list = $ret;
	}

	public function lists($limit, $config = array())
	{
		$default = array(
				'integral_source' => '', 
				'integral_type' => '', 
				'member_id' => '');
		$this->extend($default, $config);
		$this->db->where('state', 'valid');
		if ($this->member_id) {
			$this->db->where('member_id', $this->member_id);
		}
		if ($this->integral_source) {
			$this->db->where('source', $this->integral_source);
		}
		if ($this->integral_type) {
			$this->db->where('type', $this->integral_type);
		}
		if ($limit == 'count') {
			return $this->fetch_count();
		}
		$this->db->select('id,integral,integral_total,type,remark,source,ctime');
		$this->db->order_by('id', 'desc');
		$this->db->limit($limit);
		$ret = $this->translate_result('source,type,ctime,action_lists');
		return $ret;
	}

	protected function translate($row, $config = null, $extend = null)
	{
		if ($config) {
			if ($this->translate_required('store_name', $config)) {
				$row->store_name_str = $row->store_name ? $row->store_name : '-';
			}
			if ($this->translate_required('store_name_daogou', $config)) {
				$row->store_name_daogou_str = $row->store_name_daogou ? $row->store_name_daogou : '-';
			}
			if ($this->translate_required('store_area', $config)) {
				$this->lang->load('model/store');
				$row->store_area_str = $this->lang->line($row->store_area, 'store_area');
			}
			if ($this->translate_required('integral_total', $config)) {
				$row->integral_total_str = $row->integral_total ? number_format($row->integral_total) : '-';
			}
			if ($this->translate_required('integral', $config)) {
				$row->integral_str = number_format($row->integral);
			}
			if ($this->translate_required('city_name', $config)) {
				$row->city_name_str = $row->city_name ? $row->city_name : '-';
			}
			if ($this->translate_required('province_name', $config)) {
				$row->province_name_str = $row->province_name ? $row->province_name : '-';
			}
			if ($this->translate_required('ctime', $config)) {
				$row->ctime_str = $row->ctime ? date('Y-m-d H:i:s', $row->ctime) : '-';
			}
			if ($this->translate_required('type', $config)) {
				$row->type_str = $this->lang->line($row->type, 'member_integral_type');
			}
			if ($this->translate_required('source', $config)) {
				$row->source_str = $this->lang->line($row->source, 'member_integral_source');
			}
			if ($this->translate_required('action_lists', $config)) {
				$row->action = url_implode(' | ', $this->action_lists($row));
			}
			if ($this->translate_required('action_lists_collect', $config)) {
				$row->action = url_implode(' | ', $this->action_lists_collect($row));
			}
			return $row;
		}
	}

	protected function action_lists_collect($row)
	{
		$ret = array();
		$ret[] = $this->acl->anchor('service/member/integral/action/lists_collect_detail', 'class="operate"', array(
				'uri_param' => http_build_query($this->input->get('member_id,stime,etime,source_type', true, array(
						'member_id' => $row->member_id)))));
		return $ret;
	}

	protected function action_lists($row)
	{
		$ret = array();
		return $ret;
	}
}