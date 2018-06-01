<?php
class Action extends User_Controller
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function lists_lottery()
	{
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $ret = array('data' => '','code' => 0);
	    if($this->input->post('ajax_do') == 1){
	        $ret['data'] = $this->lottery_imodel->get_prize($this->input->post('lottery_id'));
	        $ret['code'] = 1;
	        echo json_encode($ret);
	        exit;
	    }
	    $this->data->lottery = $this->lottery_imodel->lists_lottery($this->input->get('id'));
	    $this->view(null,'jqueryUI');
	}
	
	public function ajax_insert_record()
	{
	    $uri_query = $this->input->post('name,phone,address,prize_grade,lottery_id',true,true);
	    $this->load->model('dao/lottery/lottery_model');
	    $ret = array('code' => 0,'message' => '');
	    try{
	        $this->lottery_model->ajax_insert_record($uri_query);
	        $ret['code'] = 1;
	        $ret['message'] = $this->lottery_model->message();
	    }catch(Err $e){
	        $ret['message'] = $e->message();
	    }
	    echo json_encode($ret);
	}
	
	public function lists()
	{
	    $this->load->library('pagination');
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $uri_query = $this->input->get('keyword');
	    $config[] = array();
	    $config['per_page'] = 30;
	    $config['uri_segment'] = 5;
	    $config['base_url'] = '/service/lottery/action/lists';
	    $config['total_rows'] = $this->lottery_imodel->lists('count');
	    $this->pagination->initialize($config);
	    $this->data->uri_query = $uri_query;
	    $this->data->links = $this->pagination->create_links();
	    $this->data->lists = $this->lottery_imodel->lists($this->pagination->create_limit());
	    $this->data->actions = '';
	    $this->data->actions .= "<div class='rowAdd'><a href=" . site_url('/service/lottery/action/view_insert') ." class='addBtn'>添加转盘</a></div>";
	    $this->view(null,'jqueryUI');
	}
	
	public function view_insert()
	{
	    if($this->input->post('ajax_do')){
	        $this->insert_do();
	        exit();
	    }
	    $this->view(null,'jqueryUI');
	}
	
	public function insert_do()
	{
	    $ret = array('code' => 0,'message' => '');
	    $this->load->model('dao/lottery/lottery_model');
	    try{
	        $uri_query = $this->input->post('name,stime,etime',true,true);
	        $this->lottery_model->insert_do($uri_query);
	        $ret['code'] = 1;
	        $ret['message'] = $this->lottery_model->message();
	    }catch(Err $e){
	        $ret['message'] = $e->message();
	    }
	    echo json_encode($ret);
	}
	
	public function set_prize()
	{
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $this->data->info = $this->lottery_imodel->get_prize($this->input->get('id'),'get');
	    if($this->input->post('ajax_do')){
	       $this->set_prize_do();
	       exit;
	    }
	    $this->view(null,'jqueryUI');
	}
	
	public function set_prize_do()
	{
	    $this->load->model('dao/lottery/lottery_model');
	    $this->lang->load('model/lottery');
	    $ret = array('code' => 0,'message' => '');
	    try{
	        $insert = $this->input->post('ajax_do');
	        $uri_query = $this->input->post('id,prize_grade,name,probability,information',true,true);
	        $this->lottery_model->set_prize_do($this->input->post('lottery_id'),$uri_query,$insert);
	        $ret['code'] = 1;
	        $ret['message'] = $this->lottery_model->message();
	    }catch(Err $e){
	        $ret['message'] = $e->message();
	    }
	    echo json_encode($ret);
	}
	
	public function view_update()
	{
	    if($this->input->post('ajax_do')) {
	        $this->update_do();
	        exit;
	    }
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $this->data->info = $this->lottery_imodel->view_update($this->input->get('id'));
	    $this->view(null,'jqueryUI');
	}
	
	public function update_do()
	{
	    $ret = array('code' => 0,'message' => '');
	    $uri_query = $this->input->post('id,name,stime,etime',true,true);
	    $this->load->model('dao/lottery/lottery_model');
	    try{
	        $this->lottery_model->update_do($uri_query);
	        $ret['code'] = 1;
	        $ret['message'] = $this->lottery_model->message();
	    } catch(Err $e) {
	        $ret['message'] = $e->message();
	    }
	    echo json_encode($ret);
	}
	
	public function lists_record()
	{
	    $this->load->library('pagination');
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $this->data->uri_query = $this->input->get('id,keyword,stime,etime',true,true);
	    $config = array();
	    $config['base_url'] = '/service/lottery/action/lists_record';
	    $config['per_page'] = 30;
	    $config['uri_segment'] = 5;
	    $config['total_rows'] = $this->lottery_imodel->lists_record($this->data->uri_query,'count');
	    $this->pagination->initialize($config);
	    $this->data->links = $this->pagination->create_links();
	    $this->data->lists = $this->lottery_imodel->lists_record($this->data->uri_query,$this->pagination->create_limit());
	    $this->view(null,'WdatePicker,jqueryUI');
	}
	
	public function activity_data()
	{
	    $this->load->library('pagination');
	    $this->load->model('business/service/lottery/lottery_imodel');
	    $this->data->uri_query = $this->input->get('id,stime,etime',true,true);
	    $config = array();
	    $config['base_url'] = '/service/lottery/action/activity_data';
	    $config['per_page'] = 30;
	    $config['uri_segment'] = 5;
	    $config['total_rows'] = $this->lottery_imodel->activity_data($this->data->uri_query,'count');
	    $this->pagination->initialize($config);
	    $this->data->links = $this->pagination->create_links();
	    $this->data->total = $this->lottery_imodel->activity_data($this->data->uri_query,'total');
	    $this->data->lists = $this->lottery_imodel->activity_data($this->data->uri_query,$this->pagination->create_limit());
	    $this->view(null,'WdatePicker,jqueryUI');
	}
	
	public function ajax_delete()
	{
	    $this->load->model('dao/lottery/lottery_model');
	    $ret = array('code' => 0,'message' => '');
	    try{
	        $this->lottery_model->ajax_delete($this->input->post('id'));
	        $ret['code'] = 1;
	        $ret['message'] = $this->lottery_model->message();
	    }catch(Err $e){
	        $ret['message'] = $e->message();
	    }
	    echo json_encode($ret);
	}
}