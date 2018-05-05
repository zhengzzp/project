<?php
class Action extends User_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function lists()
	{
		$this->load->model('business/service/member/member_imodel');
		try {
			$this->data->info = $this->member_imodel->view_info($this->input->get('member_id'));
		} catch(Err $e) {
			echo alert_message($e->message());
			exit();
		}
		$this->lang->load('model/member_integral');
		$this->load->library('pagination');
		$this->load->model('business/service/member/member_integral_imodel');
		$uri_query = $this->input->get('integral_source,integral_type,member_id', true, true);
		$config = array();
		$config['total_rows'] = $this->member_integral_imodel->lists('count', $uri_query);
		$config['per_page'] = 30;
		$config['uri_segment'] = 6;
		$config['base_url'] = 'service/member/integral/action/lists';
		$this->pagination->initialize($config);
		$this->data->links = $this->pagination->create_links();
		$this->data->lists = $this->member_integral_imodel->lists($this->pagination->create_limit(), $uri_query);
		$this->data->uri_query = $uri_query;
		$this->data->integral_type_array = $this->lang->line('member_integral_type');
		$this->data->integral_source_array = $this->lang->line('member_integral_source');
		$this->views(null, 'WdatePicker,jqueryUI');
	}
	
	public function lists_collect()
	{
	    $this->lang->load('model/member_intergral');
	    $this->load->library('pagination');
	    $this->load->model('business/service/member/member_integral_imodel');
	    $uri_query = $this->input->input->get('keyword,source_type,stime,etime',true,true);
	    $config = array();
	    $config['total_rows'] = $this->member_integral_imodel->list('count',$uri_query);
	    $config['per_page'] = 30;
	    $config['uri_segment'] = 6;
	    $config['base_url'] = 'service/member/integral/action/lists_collect';
	    $this->pagination->initialize($config);
	    $this->data->links = $this->pagination->create_links();
	    $this->data->lists = $this->member_integral_imodel->lists($this->pagination->create_limit(),$uri_query);
	    $this->data->uri_query = $uri_query;
	    $this->data->source_type_array = $this->lang->line('member_integral_source_type');
        $this->data->actions .= "<div class='rowAdd'><a href=" . site_url('/service/member/integral/action/ajax_export_excel') ." class='Btn exportBtn'>导出CSV</a></div>";
	    $this->views(null,'jqueryUI');
	}

	public function ajax_license_city_json()
	{
		$this->load->model('dao/city/city_model');
		$ret = $this->city_model->list_name($this->input->post('id', true));
		echo json_encode($ret);
	}

		/*
		$this->load->library('excelhelper');
		$excel = new excelHelper();
		$headerarr = array(
				'会员ID', 
				'会员姓名', 
				'会员卡号', 
				'会员电话', 
				'所属门店', 
				'省份', 
				'城市', 
				'积分');
		$excel->exportData($headerarr, $data, 'member_integral_' . date('Y-m-d'));
		*/
}