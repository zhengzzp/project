<?php
class Action extends User_Controller
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function lists()
	{
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $this->data->lottery = $this->lottery_imodel->lists('state');
	    $ret = array('data' => '','code' => 0);
	    if($this->input->post('ajax_do') == 1){
	        $ret['data'] = $this->lottery_imodel->get_prize($this->data->lottery->id);
	        $ret['code'] = 1;
	        echo json_encode($ret);
	        exit();
	    }
	    $this->view();
	}
	
	public function ajax_insert()
	{
	    $uri_query = $this->input->post('name,phone,address,prize_grade,lottery_id',true,true);
	    $this->load->model('dao/lottery/lottery_model');
	    $ret = array('code' => 0,'message' => '');
	    try{
	        $this->lottery_model->ajax_insert($uri_query);
	        $ret['code'] = 1;
	        $ret['message'] = $this->lottery_model->message();
	    }catch(Err $e){
	        $ret['message'] = $e->message();
	    }
	    echo json_encode($ret);
	}
	
}