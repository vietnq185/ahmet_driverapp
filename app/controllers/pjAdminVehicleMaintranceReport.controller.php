<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVehicleMaintranceReport extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
		->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		->where('t1.status', 'T')
		->where('t1.type', 'own')
		->orderBy('t1.order ASC, t2.content ASC')
		->findAll()
		->getData();
		$this->set('vehicle_arr', $vehicle_arr);
		
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('pjAdminVehicleMaintranceReport.js');
	}
	
	public function pjActionGetReport() {
	    $this->setAjax(true);
	    $pjVehicleMaintranceServiceModel = pjVehicleMaintranceServiceModel::factory()
	       ->join('pjVehicleMaintrance', 't2.id=t1.foreign_id', 'inner')
	       ->join('pjMultiLang', "t3.model='pjVehicleMaintranceServiceType' AND t3.foreign_id=t1.service_type_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	       ->where('t2.vehicle_id', $this->_post->toInt('vehicle_id'));
	    
	    $from_date = date('Y-m-d');
	    $to_date = date('Y-m-d');
	    if ($this->_post->toString('from_date') != '')
	    {
	        $from_date = pjDateTime::formatDate($this->_post->toString('from_date'), $this->option_arr['o_date_format']);
	    }
	    if ($this->_post->toString('to_date') != '')
	    {
	        $to_date = pjDateTime::formatDate($this->_post->toString('to_date'), $this->option_arr['o_date_format']);
	    }
	    $pjVehicleMaintranceServiceModel->where('t1.date BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
	    $arr = $pjVehicleMaintranceServiceModel->select('t1.*, t3.content AS service_type')->orderBy('t1.date DESC')->findAll()->getData();
	    $this->set('arr', $arr);
	}
}
?>