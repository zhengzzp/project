<?php
class Member_integral_model extends MY_Model
{
	var $table = 'member_integral';
	var $info_member = '';
	var $info_coupon = array();

	function __construct()
	{
		parent::__construct();
		$this->lang->load('model/member_integral');
	}

	protected function set_quantify($val)
	{
		$val = (int)$val;
		$this->quantify = $val;
	}

	/**
	 * 兑换优惠券
	 * @param int $config
	 */
	public function exchange_coupon($config)
	{
		$default = array(
				'act' => 'exchange_coupon', 
				'field_validate_database' => true, 
				'member_id' => '', 
				'coupon_id' => '', 
				'quantify' => 1);
		$this->extend($default, $config);
		if (! $this->info_member) {
			throw new Err('member_integral_member_invalid');
		}
		if ($this->info_member->card_type != 'online') {
			//throw new Err('member_integral_member_type_invalid');
		}
		if (! $this->info_coupon) {
			throw new Err('优惠券信息异常');
		}
		if (! $this->quantify) {
			throw new Err('member_integral_exchange_coupon_quantify_invalid');
		}
		$this->load->model('dao/coupon/coupon_model');
		try {
			$this->check_count_receive();
			$this->coupon_info = $this->coupon_model->exchange_check(array(
					'id' => $this->coupon_id, 
					'quantify' => $this->quantify));
			$this->consume_coupon_check();
			$this->exchange_info = $this->coupon_model->exchange(array(
					'id' => $this->coupon_id, 
					'member_id' => $this->member_id, 
					'quantify' => $this->quantify));
			$this->consume_coupon();
		} catch(Err $e) {
			throw new Err($e->code());
		}
		$this->set_message('member_integral_exchange_coupon_success');
	}

	protected function set_coupon_id($val)
	{
		$val = (int)$val;
		if ($this->field_validate_database) {
			$this->db->where('id', $val);
			$this->db->where('state', 'valid');
			$this->info_coupon = $this->db->get('coupon')->row();
		}
		$this->coupon_id = $val;
	}

	protected function check_count_receive()
	{
		if ($this->info_coupon->count_apiece) {
			$this->db->where('coupon_id', $this->coupon_id);
			$this->db->where('member_id', $this->member_id);
			$this->db->where('state !=', 'invalid');
			$count = $this->db->count_all_results('coupon_report');
			if (($this->quantify + $count) > $this->info_coupon->count_apiece) {
				throw new Err('兑换数量已超过该优惠券限制的兑换数量');
			}
		}
	}

	protected function push_message_integral()
	{
		if (DEVELOPMENT) {
			return;
		}
		$this->load->library('weixin/weixin_template');
		$data = array();
		$data['touser'] = $this->info_member->open_id;
		$data['template_id'] = '6oG0JhBZlDku1fbt6Fk5aQDHKzw_T2Ddp50W_oZq7gg';
		$data['url'] = SITE_FANS . 'fans/member/integral/action/lists';
		$data['first'] = ($this->info_member->nickname ? $this->info_member->nickname : $this->info_member->fullname) . '，您好：';
		$data['keyword1'] = '无';
		$data['keyword2'] = $this->coupon_info->integral . '分';
		$data['keyword3'] = $this->info_member->integral . '分';
		$data['keyword4'] = date('Y-m-d H:i:s');
		$data['remark'] = '积分兑换活动';
		try {
			$this->weixin_template->send('opentm201838927', $data);
		} catch(Err $e) {
			if ($this->is_inside_ip()) {
				$this->log($e->message());
			}
		}
	}

	protected function consume_coupon_check()
	{
		$this->integral_require = $this->coupon_info->integral * $this->quantify;
		if ($this->integral_require > $this->info_member->integral) {
			throw new Err('member_integral_require_invalid');
		}
	}

	protected function consume_coupon()
	{
		$this->db->set('integral', 'integral - ' . $this->integral_require, FALSE);
		$this->db->where('id', $this->member_id);
		$this->db->update('member');
		foreach($this->exchange_info->coupon_report_id_list as $object_id) {
			$data = array();
			$data['member_id'] = $this->member_id;
			$data['integral'] = 0 - $this->coupon_info->integral;
			$this->info_member->integral = $this->info_member->integral - $this->coupon_info->integral;
			$data['integral_total'] = $this->info_member->integral;
			$data['type'] = 'out';
			$data['object_type'] = 'coupon';
			$data['object_id'] = $object_id;
			$data['remark'] = '兑换优惠券消费积分';
			$data['source'] = 'sys';
			$data['state'] = 'valid';
			$data['ctime'] = time();
			$this->db->insert($this->table, $data);
			$this->push_message_integral();
		}
	}

	public function in($config)
	{
		$default = array(
				'field_validate_database' => true, 
				'member_id' => '', 
				'integral' => '', 
				'remark' => '', 
				'source' => '');
		$data = $this->extend($default, $config);
		unset($data['field_validate_database']);
		if (! $this->info_member) {
			throw new Err('member_integral_member_invalid');
		}
		$data['integral_total'] = $this->info_member->integral;
		$data['type'] = 'in';
		$data['state'] = 'valid';
		$data['ctime'] = time();
		if (! $this->insert($data)) {
			throw new Err('member_integral_in_failure');
		}
	}

	protected function set_member_id($val)
	{
		$val = (int)$val;
		if ($this->field_validate_database) {
			$this->db->select('integral,card_type,nickname,open_id,fullname');
			$this->db->where('id', $val);
			$this->info_member = $this->db->get('member')->row();
		}
		$this->member_id = $val;
	}

	protected function set_integral($val)
	{
		$val = (int)$val;
		$this->integral = $val;
	}

	public function slave_insert_activity($config)
	{
		$default = array();
		$default['member_id'] = '';
		$default['integral'] = '';
		$default['integral_total'] = '';
		$default['type'] = '';
		$default['object_type'] = '';
		$default['object_id'] = '';
		$default['remark'] = '';
		$data = $this->extend($default, $config);
		$data['source'] = 'sys';
		$data['state'] = 'valid';
		$data['ctime'] = time();
		if (! $this->insert($data)) {
			throw new Err('member_slave_insert_activity_failure');
		}
	}

	public function slave_insert_sell($config)
	{
		$default = array();
		$default['member_id'] = '';
		$default['integral'] = '';
		$default['integral_total'] = '';
		$default['type'] = '';
		$default['object_type'] = '';
		$default['object_id'] = '';
		$default['remark'] = '';
		$data = $this->extend($default, $config);
		$data['source'] = 'sys';
		$data['state'] = 'valid';
		$data['ctime'] = time();
		if (! $this->insert($data)) {
			throw new Err('member_slave_insert_activity_failure');
		}
	}

	public function return_integral($config)
	{
		$default = array(
				'field_validate_database' => true, 
				'act' => 'return_integral', 
				'object_id' => '', 
				'member_id' => '', 
				'object_type' => '');
		$this->extend($default, $config);
		if ($this->object_id && $this->member_id && $this->object_type && $this->info_member) {
			$this->db->where('member_id', $this->member_id);
			$this->db->where('type', 'out');
			$this->db->where('object_id', $this->object_id);
			$this->db->where('object_type', $this->object_type);
			$this->db->where('state', 'valid');
			if ($info = $this->fetch_row()) {
				$data = array(
						'member_id' => $this->member_id, 
						'integral' => abs($info->integral), 
						'integral_total' => abs($info->integral) + $this->info_member->integral, 
						'type' => 'in', 
						'object_type' => $this->object_type, 
						'object_id' => $this->object_id, 
						'remark' => '订单取消返还积分', 
						'source' => 'sys', 
						'state' => 'valid', 
						'ctime' => time());
				if (! $this->insert($data)) {
					$this->log('订单取消返还积分失败，订单id:' . $this->object_id);
					return;
				}
				$this->db->where('id', $this->member_id);
				$this->db->set('integral', 'integral+' . abs($info->integral), false);
				$this->db->update('member');
				if (! $this->db->affected_rows()) {
					$content = '订单取消返还积分给用户失败,用户ID：' . $this->member_id . ',积分' . abs($info->integral);
					$this->log($content);
				}
			}
		}
	}
}