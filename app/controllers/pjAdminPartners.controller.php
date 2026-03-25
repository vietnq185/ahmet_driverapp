<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_INSTALL_PATH. 'dompdf/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

class pjAdminPartners extends pjAdmin
{	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$this->set('has_update', pjAuth::factory('pjAdminPartners', 'pjActionUpdate')->hasAccess());
		$this->set('has_create', pjAuth::factory('pjAdminPartners', 'pjActionCreate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminPartners', 'pjActionDeleteService')->hasAccess());
		
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendCss('pjAdminPartners.css');
		$this->appendJs('pjAdminPartners.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjPartnerModel = pjPartnerModel::factory();
			
		if ($q = $this->_get->toString('q'))
		{
		    $q = str_replace(array('_', '%'), array('\_', '\%'), $pjPartnerModel->escapeStr($q));
		    $pjPartnerModel->where('(t1.name LIKE "%'.$q.'%" OR t1.company_name LIKE "%'.$q.'%" OR t1.phone LIKE "%'.$q.'%" OR t1.email LIKE "%'.$q.'%" OR t1.company_number LIKE "%'.$q.'%" OR t1.tax_number LIKE "%'.$q.'%")');
		}
		
		$column = 'created';
		$direction = 'DESC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjPartnerModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		
		$tblVehicle = pjVehicleModel::factory()->getTable();
		$tblPartnerVehicle = pjPartnerVehicleModel::factory()->getTable();
		$tblMultiLang = pjMultiLangModel::factory()->getTable();
		$tblPartnerReport = pjPartnerReportModel::factory()->getTable();
		$data = $pjPartnerModel
					->select("t1.*,
            (
                SELECT GROUP_CONCAT(tml.content SEPARATOR '<br/>') FROM `".$tblPartnerVehicle."` AS tpv 
                INNER JOIN `".$tblVehicle."` AS tv ON tv.id=tpv.vehicle_id 
                LEFT OUTER JOIN `".$tblMultiLang."` AS tml ON tml.model='pjVehicle' AND tml.foreign_id=tv.id AND tml.field='name' AND tml.locale=".$this->getLocaleId()."
                WHERE tpv.partner_id=t1.id LIMIT 1
            ) AS `vehicles`,
            (SELECT CONCAT_WS('~.~', `date_from`, `date_to`) FROM `".$tblPartnerReport."` WHERE `partner_id`=t1.id ORDER BY `created` DESC LIMIT 1) AS last_billing,
            (SELECT `status` FROM `".$tblPartnerReport."` WHERE `partner_id`=t1.id ORDER BY `created` DESC LIMIT 1) AS status_last_billing
        ")
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		$report_billing_statuses = __('report_billing_statuses', true);
		foreach ($data as $k => $v) {
		    if (!empty($v['last_billing'])) {
    		    $last_billing_arr = explode("~.~", $v['last_billing']);
    		    $date_from = date($this->option_arr['o_date_format'], strtotime($last_billing_arr[0]));
    		    $date_to = date($this->option_arr['o_date_format'], strtotime($last_billing_arr[1]));
    		    $v['last_billing_formated'] = $date_from.' - '.$date_to;
    		    $v['status_last_billing_formated'] = @$report_billing_statuses[$v['status_last_billing']];
		    } else {
		        $v['last_billing_formated'] = '';
		        $v['status_last_billing_formated'] = '';
		    }
			$data[$k] = $v;
		}	
		self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if (!pjAuth::factory()->hasAccess())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Access denied.'));
		}
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isGet() && !$this->_get->check('id') && $this->_get->toInt('id') < 0)
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if (pjPartnerModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
		    pjPartnerVehicleModel::factory()->whereIn('partner_id', $this->_get->toInt('id'))->eraseAll();
		    
		    // Documents
		    $file_arr = pjPartnerContractDocumentModel::factory()->where('t1.foreign_id', $this->_get->toInt('id'))->getData();
		    $file_ids_arr = array();
		    @clearstatcache();
		    foreach ($file_arr as $val) {
		        $file_ids_arr[] = $val['id'];
		        if (!empty($val['source_path']) && is_file($val['source_path']))
    		    {
    		        @unlink($val['source_path']);
    		    }
		    }
		    if ($file_ids_arr) {
		        pjPartnerContractDocumentModel::factory()->reset()->whereIn('id', $file_ids_arr)->eraseAll();
		    }
		    
		    // Report
		    $report_arr = pjPartnerReportModel::factory()->where('t1.partner_id', $this->_get->toInt('id'))->findAll()->getData();
		    if ($report_arr) {
		        $report_ids_arr = array();
		        @clearstatcache();
		        foreach ($report_arr as $val) {
		            $report_ids_arr[] = $val['id'];
		            if (!empty($val['pdf_path']) && is_file($val['pdf_path']))
		            {
		                @unlink($val['pdf_path']);
		            }
		        }
		        
		       
		        pjPartnerReportModel::factory()->reset()->whereIn('partner_id', $report_ids_arr)->eraseAll();
		    }
		    
			$response = array('status' => 'OK');
		} else {
			$response = array('status' => 'ERR');
		}
		
		self::jsonResponse($response);
	}
	
	public function pjActionDeleteBulk()
	{
		$this->setAjax(true);
	
		if (!pjAuth::factory()->hasAccess())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Access denied.'));
		}
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}

		if (!$this->_post->has('record') || !($record = $this->_post->toArray('record')))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid data.'));
		}
		if (pjPartnerModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
		    pjPartnerVehicleModel::factory()->whereIn('partner_id', $record)->eraseAll();
		    
		    // Documents
		    $file_arr = pjPartnerContractDocumentModel::factory()->whereIn('t1.foreign_id', $record)->getData();
		    $file_ids_arr = array();
		    @clearstatcache();
		    foreach ($file_arr as $val) {
		        $file_ids_arr[] = $val['id'];
		        if (!empty($val['source_path']) && is_file($val['source_path']))
		        {
		            @unlink($val['source_path']);
		        }
		    }
		    if ($file_ids_arr) {
		        pjPartnerContractDocumentModel::factory()->reset()->whereIn('id', $file_ids_arr)->eraseAll();
		    }
		    
		    // Report
		    $report_arr = pjPartnerReportModel::factory()->whereIn('t1.partner_id', $record)->findAll()->getData();
		    if ($report_arr) {
		        $report_ids_arr = array();
		        @clearstatcache();
		        foreach ($report_arr as $val) {
		            $report_ids_arr[] = $val['id'];
		            if (!empty($val['pdf_path']) && is_file($val['pdf_path']))
		            {
		                @unlink($val['pdf_path']);
		            }
		        }
		        
		        
		        pjPartnerReportModel::factory()->reset()->whereIn('partner_id', $report_ids_arr)->eraseAll();
		    }
		    
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionCreate()
	{
	    $this->checkLogin();
	    
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
	    
	    if (self::isPost() && $this->_post->check('action_add')) {
	        $id = pjPartnerModel::factory()->setAttributes($this->_post->raw())->insert()->getInsertId();
	        if ($id !== false && (int)$id > 0) {
	            if ($this->_post->toArray('vehicle_ids') && $this->_post->toArray('vehicle_ids') != '')
	            {
	                $pjPartnerVehicleModel = pjPartnerVehicleModel::factory()->setBatchFields(array('vehicle_id', 'partner_id'));
	                foreach ($this->_post->toArray('vehicle_ids') as $vehicle_id)
	                {
	                    $pjPartnerVehicleModel->addBatchRow(array($vehicle_id, $id));
	                }
	                $pjPartnerVehicleModel->insertBatch();
	            }
	            
	            $this->generatePartneContractPdf($id);
	            
	            $err = 'APAN01';
	        } else {
	            $err = 'APAN04';
	        }
	        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminPartners&action=pjActionUpdate&id=$id&err=$err");
	    }
		if (self::isGet())
		{
		    $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
		    ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		    ->where('t1.status', 'T')
		    ->where('t1.type', 'partner')
		    ->orderBy('t1.order ASC, t2.content ASC')
		    ->findAll()
		    ->getData();
		    $this->set('vehicle_arr', $vehicle_arr);
		    
		    $contract_theme_arr = pjContractThemeModel::factory()->where('t1.status', 'T')->orderBy('t1.name')->findAll()->getData();
		    $this->set('contract_theme_arr', $contract_theme_arr);
		    
		    $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		    $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		    
		    $this->appendCss('pjAdminPartners.css');
		    $this->appendJs('pjAdminPartners.js');
		}
	}
	
	public function pjActionUpdate()
	{
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }

		if (self::isPost() && $this->_post->check('action_update'))
		{
		    $pjPartnerModel = pjPartnerModel::factory();
		    $pjPartnerModel->set('id', $this->_post->toInt('id'))->modify($this->_post->raw());
		    
		    $pjPartnerVehicleModel = pjPartnerVehicleModel::factory();
		    $pjPartnerVehicleModel->where('partner_id', $this->_post->toInt('id'))->eraseAll();
		    if ($this->_post->toArray('vehicle_ids') && $this->_post->toArray('vehicle_ids') != '')
		    {
		        $pjPartnerVehicleModel->reset()->setBatchFields(array('vehicle_id', 'partner_id'));
		        foreach ($this->_post->toArray('vehicle_ids') as $vehicle_id)
		        {
		            $pjPartnerVehicleModel->addBatchRow(array($vehicle_id, $this->_post->toInt('id')));
		        }
		        $pjPartnerVehicleModel->insertBatch();
		    }
		    
		    if ($this->_post->toInt('update_type') == 2) {
		        $this->generatePartneContractPdf($this->_post->toInt('id'));
		    }
		    
		    pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminPartners&action=pjActionIndex&err=APAN03");
		}
		
		if (self::isGet())
		{
			$arr = pjPartnerModel::factory()->find($this->_get->toInt('id'))->getData();
			if (empty($arr))
			{
			    pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminPartners&action=pjActionIndex&err=APAN08");
			}
			$this->set('arr', $arr);
			
			$this->set('partner_vehicle_arr', pjPartnerVehicleModel::factory()->where('t1.partner_id', $arr['id'])->findAll()->getDataPair(null, 'vehicle_id'));
			
			$vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->where('t1.status', 'T')
			->where('t1.type', 'partner')
			->orderBy('t1.order ASC, t2.content ASC')
			->findAll()
			->getData();
			$this->set('vehicle_arr', $vehicle_arr);
			
			$contract_theme_arr = pjContractThemeModel::factory()->where('t1.status', 'T')->orderBy('t1.name')->findAll()->getData();
			$this->set('contract_theme_arr', $contract_theme_arr);
			
			$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
			$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			
			$this->appendCss('magnific-popup.min.css', PJ_THIRD_PARTY_PATH . 'magnific_popup/');
			$this->appendJs('jquery.magnific-popup.min.js', PJ_THIRD_PARTY_PATH . 'magnific_popup/');
			
			$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
			$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
			$this->appendCss('clockpicker.css', PJ_THIRD_PARTY_PATH . 'clockpicker/');
			$this->appendJs('clockpicker.js');
			
			$this->appendJs('jquery.form.js', PJ_THIRD_PARTY_PATH . 'jquery/');
			$this->appendJs('jquery.plupload.full.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
			$this->appendCss('pjAdminPartners.css');
			$this->appendJs('pjAdminPartners.js');
		}
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'HTTP method not allowed.'));
		}
		
		$params = array(
				'id' => $this->_get->toInt('id'),
				'column' => $this->_post->toString('column'),
				'value' => $this->_post->toString('value'),
		);
		if (!(isset($params['id'], $params['column'], $params['value'])
				&& pjValidation::pjActionNumeric($params['id'])
				&& pjValidation::pjActionNotEmpty($params['column'])))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$pjProviderModel = pjProviderModel::factory();
		if (!in_array($params['column'], $pjProviderModel->getI18n()))
		{
			$pjProviderModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjProvider');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionUploadFiles() {
	    $this->setAjax(true);
	    $file = $_FILES['file'];
	    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
	    if (!in_array($extension, $allowed)) {
	        echo json_encode(['status' => 'ERR', 'msg' => 'Định dạng file không được hỗ trợ cho loại này.']);
	        exit;
	    }
	    
	    $randomPart = bin2hex(random_bytes(10));
	    $uniquePart = uniqid('', true);
	    $filename = $randomPart . '_' . $uniquePart . '.' . $extension;
	    
	    $file_path = PJ_UPLOAD_PATH.'files/contract_documents/'.$filename;
	    if (@move_uploaded_file($file['tmp_name'], $file_path)) {
	        $data = array();
	        if (isset($_REQUEST['foreign_id']) && (int)$_REQUEST['foreign_id'] > 0) {
	            $data['foreign_id'] = (int)$_REQUEST['foreign_id'];
	        } else {
	            $data['tmp_hash'] = $_REQUEST['foreign_id'];
	        }
	        $data['filename'] = $file['name'];
	        $data['source_path'] = $file_path;
	        
	        pjPartnerContractDocumentModel::factory()->setAttributes($data)->insert();
	    }
	    echo 'OK';
	    exit;
	}
	
	public function pjActionGetFiles() {
	    $this->setAjax(true);
	    $pjPartnerContractDocumentModel = pjPartnerContractDocumentModel::factory();
	    if ($this->_get->check('foreign_id')) {
	        if ($this->_get->toInt('foreign_id') > 0) {
	            $pjPartnerContractDocumentModel->where('t1.foreign_id', $this->_get->toInt('foreign_id'));
	        } else {
	            $pjPartnerContractDocumentModel->where('t1.tmp_hash', $this->_get->toString('foreign_id'));
	        }
	    } else {
	        $pjPartnerContractDocumentModel->where('t1.foreign_id', '-9999');
	    }
	    $arr = $pjPartnerContractDocumentModel->orderBy('t1.created ASC')->findAll()->getData();
	    $this->set('arr', $arr);
	}
	
	public function pjActionDeleteReport() {
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    
	    if (!self::isPost())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method not allowed.'));
	    }
	    
	    $id = NULL;
	    if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
	        $id = $this->_post->toInt('id');
	    }
	    
	    if (!is_null($id))
	    {
	        $pjPartnerReportModel = pjPartnerReportModel::factory();
	        $arr = $pjPartnerReportModel->find($id)->getData();
	        if (!empty($arr))
	        {
	            $pjPartnerReportModel->reset()->set('id', $id)->erase();
	            @clearstatcache();
	            if (!empty($arr['pdf_path']) && is_file($arr['pdf_path']))
	            {
	                @unlink($arr['pdf_path']);
	            }
	            
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	        }
	    }
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
	
	public function pjActionDeleteFile() {
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    
	    if (!self::isPost())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method not allowed.'));
	    }
	    
	    $id = NULL;
	    if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
	        $id = $this->_post->toInt('id');
	    }
	    
	    if (!is_null($id))
	    {
	        $pjPartnerContractDocumentModel = pjPartnerContractDocumentModel::factory();
	        $arr = $pjPartnerContractDocumentModel->find($id)->getData();
	        if (!empty($arr))
	        {
	            $pjPartnerContractDocumentModel->reset()->set('id', $id)->erase();
	            @clearstatcache();
	            if (!empty($arr['source_path']) && is_file($arr['source_path']))
	            {
	                @unlink($arr['source_path']);
	            }
	            
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	        }
	    }
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
	
	public function pjActionDownloadFile()
	{
	    $this->checkLogin();
	    
	    if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
	        $arr = pjPartnerContractDocumentModel::factory()->find($this->_get->toInt('id'))->getData();
	        if ($arr) {
	            $data = file_get_contents($arr['source_path']);
	            pjToolkit::download($data, $arr['filename']);
	        } else {
	            echo 'Missing parameter!!!';
	            exit;
	        }
	    } else {
	        echo 'Missing parameter!!!';
	        exit;
	    }
	}
	
	public function pjActionDownloadReport()
	{
	    $this->checkLogin();
	    
	    if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
	        $arr = pjPartnerReportModel::factory()->find($this->_get->toInt('id'))->getData();
	        if ($arr) {
	            $data = file_get_contents($arr['pdf_path']);
	            pjToolkit::download($data, basename($arr['pdf_path']));
	        } else {
	            echo 'Missing parameter!!!';
	            exit;
	        }
	    } else {
	        echo 'Missing parameter!!!';
	        exit;
	    }
	}
	
	public function pjActionGetReport() {
	    $this->setAjax(true);
	    $pjPartnerReportModel = pjPartnerReportModel::factory();
	    if ($this->_get->check('foreign_id') && $this->_get->toInt('foreign_id') > 0) {
	        $pjPartnerReportModel->where('t1.partner_id', $this->_get->toInt('foreign_id'));
	    } else {
	        $pjPartnerReportModel->where('t1.partner_id', '-9999');
	    }
	    $arr = $pjPartnerReportModel->orderBy('t1.created DESC')->findAll()->getData();
	    $this->set('arr', $arr);
	}
	
	public function pjActionReportForm() {
	    $this->setAjax(true);
	    
	    if ($this->_post->check('action_generate_billing')) {
	        $data = array();
	        $data['date_from'] = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
	        $data['date_to'] = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
	        $insert_new = false;
	        if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
	            $report_id = $this->_post->toInt('id');
	            pjPartnerReportModel::factory()->set('id', $report_id)->modify(array_merge($this->_post->raw(), $data));
	        } else { 
	           $report_id = pjPartnerReportModel::factory()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
	           $insert_new = true;
	        }
	        if ($report_id !== false && (int)$report_id > 0) {
	            if ($insert_new) {
	                pjPartnerReportBookingAmountModel::factory()
	                ->where('tmp_hash', $this->_post->toString('tmp_hash'))
	                ->modifyAll(array('report_id' => $report_id, 'tmp_hash' => ':NULL'));
	            }
	            $pdf_file = $this->generateReportBillingPdf($report_id);
	            pjPartnerReportModel::factory()->reset()->set('id', $report_id)->modify(array('pdf_path' => $pdf_file));
	            
	            pjAppController::jsonResponse(array('status' => 'OK', 'partner_id' => $this->_post->toInt('partner_id')));
	        } else {
	            pjAppController::jsonResponse(array('status' => 'ERR'));
	        }
	    }
	    
	    if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
	        $arr = pjPartnerReportModel::factory()->find($this->_get->toInt('id'))->getData();
	        $this->set('arr', $arr);
	        $tmp_hash = '';
	    } else {
	        $tmp_hash = $this->generateAlphaHash();
	    }
	    $this->set('tmp_hash', $tmp_hash);
	}
	
	private function generatePartneContractPdf($partner_id) {
	    $partner_arr = pjPartnerModel::factory()->select('t1.*, t2.content AS contract_content')
	    ->join('pjContractTheme', 't2.id=t1.contract_theme', 'left outer')
	    ->find($partner_id)->getData();
	    
	    $vehicle_arr = pjPartnerVehicleModel::factory()->select('t1.*, t3.content AS vehicle_name')
	       ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
	       ->join('pjMultiLang', "t3.model='pjVehicle' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	       ->where('t1.partner_id', $partner_arr['id'])
	       ->orderBy('t3.content ASC')
	       ->findAll()->getData();
	    
	    $vehicles = '<ul>';
	    foreach ($vehicle_arr as $val) {
	        $vehicles .= '<li>'.$val['vehicle_name'].'</li>';
	    }
	    $vehicles .= '<ul>';
	    
	    $search = array('{PartnerName}','{Phone}','{Email}','{CompanyName}','{Address}','{TaxNumber}','{CompanyNumber}','{Iban}','{Bic}','{CommissionPercentage}','{Notes}','{Vehicles}');
	    $replace = array(
	        $partner_arr['name'],
	        $partner_arr['phone'],
	        $partner_arr['email'],
	        $partner_arr['company_name'],
	        $partner_arr['address'],
	        $partner_arr['tax_number'],
	        $partner_arr['company_number'],
	        $partner_arr['iban'],
	        $partner_arr['bic'],
	        $partner_arr['commission_pct'].'%',
	        $partner_arr['notes'],
	        $vehicles
	    );
	    
	    $content = str_replace($search, $replace, $partner_arr['contract_content']);
	    
	    $options = new Options();
	    $options->set('defaultFont', 'DejaVu Sans'); // Hỗ trợ hiển thị ký hiệu € và tiếng Việt
	    $dompdf = new Dompdf($options);
	    
	    $html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
    </style>
</head>
<body>
	  '.$content.'      
</body>
</html>';
	    
	    $dompdf->loadHtml($html);
	    $dompdf->setPaper('A4', 'portrait');
	    $dompdf->render();
	    
	    $file_name = $partner_arr['name'].' - '.$partner_arr['company_number'].'.pdf';
	    //$dompdf->stream($file_name, ["Attachment" => false]);
	    $pdf_file = PJ_UPLOAD_PATH.'files/partners/contract/'.$file_name;
	    $output = $dompdf->output();
	    file_put_contents($pdf_file, $output);
	    
	    return $pdf_file;
	}
	
	private function generateReportBillingPdf($report_id) {
	    $report_arr = pjPartnerReportModel::factory()->select('t1.*, t2.name AS partner_name')
	    ->join('pjPartner', 't2.id=t1.partner_id', 'inner')
	    ->find($report_id)->getData();
	    $arr = $this->getBilling($report_arr['partner_id'], $report_arr['date_from'], $report_arr['date_to']);
	    
	    $custom_amount_arr = pjPartnerReportBookingAmountModel::factory()->where('t1.report_id', $report_id)->findAll()->getDataPair('booking_id', null);
	    
	    $options = new Options();
	    $options->set('defaultFont', 'DejaVu Sans'); // Hỗ trợ hiển thị ký hiệu € và tiếng Việt
	    $dompdf = new Dompdf($options);
	    
	    // Dữ liệu giả lập (Bạn thay bằng dữ liệu từ Database của mình)
	    $partnerName = $report_arr['partner_name'];
	    $dateRange = date($this->option_arr['o_date_format'], strtotime($report_arr['date_from']))." - ".date($this->option_arr['o_date_format'], strtotime($report_arr['date_to']));
	    $commissionRate = $report_arr['commission_pct'];
	    
	    $total_bookings = $total_amount = $total_paid = $total_cc = $total_cash = 0;
	    foreach ($arr['report_arr'] as $val) {
	        $total_bookings++;
	        $price = $val['price'];
	        
	        if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
	            if (isset($custom_amount_arr[$val['id']]['total_cash']) && (float)$custom_amount_arr[$val['id']]['total_cash'] > 0) {
	                $total_cash += $custom_amount_arr[$val['id']]['total_cash'];
	                $price = $custom_amount_arr[$val['id']]['total_cash'];
	            } else {
	                $total_cash += $val['price'];
	            }
	        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
	            if (isset($custom_amount_arr[$val['id']]['total_cc']) && (float)$custom_amount_arr[$val['id']]['total_cc'] > 0) {
	                $total_cc += $custom_amount_arr[$val['id']]['total_cc'];
	                $price = $custom_amount_arr[$val['id']]['total_cc'];
	            } else {
	                $total_cc += $val['price'];
	            }
	        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
	            if (isset($custom_amount_arr[$val['id']]['total_paid']) && (float)$custom_amount_arr[$val['id']]['total_paid'] > 0) {
	                $total_paid += $custom_amount_arr[$val['id']]['total_paid'];
	                $price = $custom_amount_arr[$val['id']]['total_paid'];
	            } else {
	                $total_paid += $val['price'];
	            }
	        } elseif ($val['payment_method'] == 'cash'){
	            if (isset($custom_amount_arr[$val['id']]['total_cash']) && (float)$custom_amount_arr[$val['id']]['total_cash'] > 0) {
	                $total_cash += $custom_amount_arr[$val['id']]['total_cash'];
	                $price = $custom_amount_arr[$val['id']]['total_cash'];
	            } else {
	                $total_cash += $val['price'];
	            }
	        } elseif ($val['payment_method'] == 'creditcard_later'){
	            if (isset($custom_amount_arr[$val['id']]['total_cc']) && (float)$custom_amount_arr[$val['id']]['total_cc'] > 0) {
	                $total_cc += $custom_amount_arr[$val['id']]['total_cc'];
	                $price = $custom_amount_arr[$val['id']]['total_cc'];
	            } else {
	                $total_cc += $val['price'];
	            }
	        } else {
	            if (isset($custom_amount_arr[$val['id']]['total_paid']) && (float)$custom_amount_arr[$val['id']]['total_paid'] > 0) {
	                $total_paid += $custom_amount_arr[$val['id']]['total_paid'];
	                $price = $custom_amount_arr[$val['id']]['total_paid'];
	            } else {
	                $total_paid += $val['price'];
	            }
	        }
	        $total_amount += $price;
	    }
	    
	    $html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { margin-bottom: 40px; }
        .header h1 { font-size: 20px; color: #666; margin: 0; }
        .header p { font-size: 16px; color: #888; margin: 5px 0; }
	        
        .section-title { color: #f39c12; font-size: 18px; margin-bottom: 10px; border-bottom: 1px dotted #ddd; padding-bottom: 5px; }
	        
        /* Layout Grid cho phần Total */
        .summary-grid { width: 100%; margin-bottom: 30px; }
        .summary-grid td { vertical-align: top; width: 20%; }
        .label { font-weight: bold; font-size: 11px; display: block; }
        .value { font-size: 13px; display: block; margin-top: 5px; }
	        
        /* Khối tính toán Billing */
        .billing-box { width: 100%; margin-bottom: 40px; }
        .billing-left { width: 30%; font-size: 16px; vertical-align: middle; }
        .billing-right { width: 70%; }
        .billing-table { width: 100%; border-collapse: collapse; }
        .billing-table td { padding: 5px 0; font-size: 15px; }
        .billing-table .text-label { text-align: left; color: #555; }
        .billing-table .text-value { text-align: right; font-weight: bold; }
        .total-row { font-size: 18px !important; border-top: 2px solid #333; }
	        
        /* Bảng chi tiết đơn hàng */
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table th { background-color: #f8f9fa; text-align: left; padding: 10px 5px; border-bottom: 2px solid #eee; font-size: 10px; text-transform: uppercase; }
        .details-table td { padding: 10px 5px; border-bottom: 1px solid #eee; font-size: 11px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
	        
    <div class="header">
        <h1>Partner: ' . $partnerName . '</h1>
        <p>Date: ' . $dateRange . '</p>
    </div>
            
    <div class="section-title">'.__('lblReportBillingTotal', true).'</div>
    <table class="summary-grid">
        <tr>
            <td><span class="label">'.__('lblReportBillingTotalBookings', true).':</span><span class="value">'.$total_bookings.'</span></td>
            <td><span class="label">'.__('lblReportBillingTotalAmount', true).':</span><span class="value">'.pjCurrency::formatPrice($total_amount).'</span></td>
            <td><span class="label">'.__('lblReportBillingPaid', true).':</span><span class="value">'.pjCurrency::formatPrice($total_paid).'</span></td>
            <td><span class="label">'.__('lblReportBillingCreditCard', true).':</span><span class="value">'.pjCurrency::formatPrice($total_cc).'</span></td>
            <td><span class="label">'.__('lblReportBillingCash', true).':</span><span class="value">'.pjCurrency::formatPrice($total_cash).'</span></td>
        </tr>
    </table>
            
    <table class="billing-box">
        <tr>
            <td class="billing-left">
                <span style="color: #888;">Commission ' . $commissionRate . '%</span><br>
                <strong style="font-size: 22px;">'.pjCurrency::formatPrice($report_arr['commission_amount']).'</strong>
            </td>
            <td class="billing-right">
                <table class="billing-table">
                    <tr><td class="text-label">'.__('lblReportBillingTotal', true).':</td><td class="text-value">'.pjCurrency::formatPrice($total_amount).'</td></tr>
                    <tr><td class="text-label">'.__('lblReportBillingPaid', true).':</td><td class="text-value">'.pjCurrency::formatPrice($total_paid).'</td></tr>
                    <tr><td class="text-label">'.__('lblReportBillingCommission', true).' . ' . $commissionRate . '%:</td><td class="text-value" style="color: red;">-'.pjCurrency::formatPrice((float)$report_arr['commission_amount']).'</td></tr>
                    <tr><td class="text-label">'.__('lblReportBillingPaidBookingsWeMade', true).':</td><td class="text-value">'. ((float)$report_arr['paid_bookings_we_made'] >= 0 ? '+' : '-') .pjCurrency::formatPrice((float)$report_arr['paid_bookings_we_made']).'</td></tr>
                    <tr class="total-row">
                        <td class="text-label"><strong>'.__('lblReportBillingBillingAmount', true).':</strong></td>
                        <td class="text-value"><strong>'. ((float)$report_arr['billing_amount'] >= 0 ? '+' : '') .pjCurrency::formatPrice((float)$report_arr['billing_amount']).'</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
                        
    <table class="details-table">
        <thead>
            <tr>
                <th>'.__('lblReportBillingDate', true).'</th>
                <th>'.__('lblReportBillingFromTo', true).'</th>
                <th class="text-right" align="right">'.__('lblReportBillingPaid', true).'</th>
                <th class="text-right" align="right">'.__('lblReportBillingCreditCard', true).'</th>
                <th class="text-right" align="right">'.__('lblReportBillingCash', true).'</th>
            </tr>
        </thead>
        <tbody>';
	    foreach ($arr['report_arr'] as $order) {
	        $paid = $cc = $cash = 0;
	        if (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(1,5))) {
	            if (isset($custom_amount_arr[$order['id']]['total_cash']) && (float)$custom_amount_arr[$order['id']]['total_cash'] > 0) {
	                $cash = $custom_amount_arr[$order['id']]['total_cash'];
	            } else {
	                $cash = $order['price'];
	            }
	        } elseif (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(2,6))){
	            if (isset($custom_amount_arr[$order['id']]['total_cc']) && (float)$custom_amount_arr[$order['id']]['total_cc'] > 0) {
	                $cc = $custom_amount_arr[$order['id']]['total_cc'];
	            } else {
	                $cc = $order['price'];
	            }
	        } elseif (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(8))){
	            if (isset($custom_amount_arr[$order['id']]['total_paid']) && (float)$custom_amount_arr[$order['id']]['total_paid'] > 0) {
	                $paid = $custom_amount_arr[$order['id']]['total_paid'];
	            } else {
	                $paid = $order['price'];
	            }
	        } elseif ($order['payment_method'] == 'cash'){
	            if (isset($custom_amount_arr[$order['id']]['total_cash']) && (float)$custom_amount_arr[$order['id']]['total_cash'] > 0) {
	                $cash = $custom_amount_arr[$order['id']]['total_cash'];
	            } else {
	                $cash = $order['price'];
	            }
	        } elseif ($order['payment_method'] == 'creditcard_later'){
	            if (isset($custom_amount_arr[$order['id']]['total_cc']) && (float)$custom_amount_arr[$order['id']]['total_cc'] > 0) {
	                $cc = $custom_amount_arr[$order['id']]['total_cc'];
	            } else {
	                $cc = $order['price'];
	            }
	        } else {
	            if (isset($custom_amount_arr[$order['id']]['total_paid']) && (float)$custom_amount_arr[$order['id']]['total_paid'] > 0) {
	                $paid = $custom_amount_arr[$order['id']]['total_paid'];
	            } else {
	                $paid = $order['price'];
	            }
	        }
	        if(!empty($order['return_id'])) {
	            $from_to = pjSanitize::html($order['location2'].' - '.$order['dropoff2']);
	        } else {
	            $from_to = pjSanitize::html($order['location'].' - '.$order['dropoff']);
	        }
	        $html .= '
            <tr>
                <td>'.date($this->option_arr['o_date_format'], strtotime($order['booking_date'])).'</td>
                <td>'.$from_to.'</td>
                <td class="text-right">'.pjCurrency::formatPrice($paid).'</td>
                <td class="text-right">'.pjCurrency::formatPrice($cc).'</td>
                <td class="text-right">'.pjCurrency::formatPrice($cash).'</td>
            </tr>';
	    }
	    $html .= '</tbody>
    </table>
                        
</body>
</html>';
	    
	    $dompdf->loadHtml($html);
	    $dompdf->setPaper('A4', 'portrait');
	    $dompdf->render();
	    //$dompdf->stream("Partner_" . $report_arr['partner_id'].'-'.time() . ".pdf", ["Attachment" => false]);
	    $pdf_file = PJ_UPLOAD_PATH.'files/partners/reports/'.$partnerName.' - '.$dateRange.'.pdf';
	    $output = $dompdf->output();
	    file_put_contents($pdf_file, $output);
	    
	    return $pdf_file;
	}
	
	public function pjActionGenerateBilling() {
	    $this->setAjax(true);
	    
	    $partner_arr = pjPartnerModel::factory()->find($this->_get->toInt('partner_id'))->getData();
	    
	    /* $report_arr = pjPartnerReportModel::factory()->select('t1.*, t2.name AS partner_name, t2.commission_pct')
	    ->join('pjPartner', 't2.id=t1.partner_id', 'inner')
	    ->find($this->_get->toInt('id'))->getData(); */
	    $partner_id = $this->_post->toInt('partner_id');
	    $date_from = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
	    $date_to = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
	    $data = $this->getBilling($partner_id, $date_from, $date_to);
	    $this->set('data', $data);
	    
	    if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
	        $arr = pjPartnerReportModel::factory()->find($this->_post->toInt('id'))->getData();
	        $this->set('arr', $arr);
	        
	        $custom_amount_arr = pjPartnerReportBookingAmountModel::factory()->where('t1.report_id', $this->_post->toInt('id'))->findAll()->getDataPair('booking_id', null);
	        $this->set('custom_amount_arr', $custom_amount_arr);
	    }
	}
	
	public function pjActionDownloadReportBilling() {
	    $partner_id = $this->_post->toInt('partner_id');
	    $date_from = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
	    $date_to = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
	    $arr = $this->getBilling($partner_id, $date_from, $date_to);
	    
	    $partner_arr = pjPartnerModel::factory()->find($partner_id)->getData();
	    
	    $pjPartnerReportBookingAmountModel = pjPartnerReportBookingAmountModel::factory();
	    if ($this->_post->toInt('id') > 0) {
	        $pjPartnerReportBookingAmountModel->where('t1.report_id', $this->_post->toInt('id'));
	    } else {
	        $pjPartnerReportBookingAmountModel->where('t1.tmp_hash', $this->_post->toString('tmp_hash'));
	    }
	    $custom_amount_arr = $pjPartnerReportBookingAmountModel->findAll()->getDataPair('booking_id', null);
	    
	    $options = new Options();
	    $options->set('defaultFont', 'DejaVu Sans'); // Hỗ trợ hiển thị ký hiệu € và tiếng Việt
	    $dompdf = new Dompdf($options);
	    
	    // Dữ liệu giả lập (Bạn thay bằng dữ liệu từ Database của mình)
	    $partnerName = $partner_arr['name'];
	    $dateRange = date($this->option_arr['o_date_format'], strtotime($date_from))." - ".date($this->option_arr['o_date_format'], strtotime($date_to));
	    $commissionRate = $this->_post->toFloat('commission_pct');
	    
	    $total_bookings = $total_amount = $total_paid = $total_cc = $total_cash = 0;
	    foreach ($arr['report_arr'] as $val) {
	        $total_bookings++;
	        $price = $val['price'];
	        
	        if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
	            if (isset($custom_amount_arr[$val['id']]['total_cash']) && (float)$custom_amount_arr[$val['id']]['total_cash'] > 0) {
	                $total_cash += $custom_amount_arr[$val['id']]['total_cash'];
	                $price = $custom_amount_arr[$val['id']]['total_cash'];
	            } else {
	                $total_cash += $val['price'];
	            }
	        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
	            if (isset($custom_amount_arr[$val['id']]['total_cc']) && (float)$custom_amount_arr[$val['id']]['total_cc'] > 0) {
	                $total_cc += $custom_amount_arr[$val['id']]['total_cc'];
	                $price = $custom_amount_arr[$val['id']]['total_cc'];
	            } else {
	                $total_cc += $val['price'];
	            }
	        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
	            if (isset($custom_amount_arr[$val['id']]['total_paid']) && (float)$custom_amount_arr[$val['id']]['total_paid'] > 0) {
	                $total_paid += $custom_amount_arr[$val['id']]['total_paid'];
	                $price = $custom_amount_arr[$val['id']]['total_paid'];
	            } else {
	                $total_paid += $val['price'];
	            }
	        } elseif ($val['payment_method'] == 'cash'){
	            if (isset($custom_amount_arr[$val['id']]['total_cash']) && (float)$custom_amount_arr[$val['id']]['total_cash'] > 0) {
	                $total_cash += $custom_amount_arr[$val['id']]['total_cash'];
	                $price = $custom_amount_arr[$val['id']]['total_cash'];
	            } else {
	                $total_cash += $val['price'];
	            }
	        } elseif ($val['payment_method'] == 'creditcard_later'){
	            if (isset($custom_amount_arr[$val['id']]['total_cc']) && (float)$custom_amount_arr[$val['id']]['total_cc'] > 0) {
	                $total_cc += $custom_amount_arr[$val['id']]['total_cc'];
	                $price = $custom_amount_arr[$val['id']]['total_cc'];
	            } else {
	                $total_cc += $val['price'];
	            }
	        } else {
	            if (isset($custom_amount_arr[$val['id']]['total_paid']) && (float)$custom_amount_arr[$val['id']]['total_paid'] > 0) {
	                $total_paid += $custom_amount_arr[$val['id']]['total_paid'];
	                $price = $custom_amount_arr[$val['id']]['total_paid'];
	            } else {
	                $total_paid += $val['price'];
	            }
	        }
	        
	        $total_amount += $price;
	    }
	    
	    $html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { margin-bottom: 40px; }
        .header h1 { font-size: 20px; color: #666; margin: 0; }
        .header p { font-size: 16px; color: #888; margin: 5px 0; }
	        
        .section-title { color: #f39c12; font-size: 18px; margin-bottom: 10px; border-bottom: 1px dotted #ddd; padding-bottom: 5px; }
	        
        /* Layout Grid cho phần Total */
        .summary-grid { width: 100%; margin-bottom: 30px; }
        .summary-grid td { vertical-align: top; width: 20%; }
        .label { font-weight: bold; font-size: 11px; display: block; }
        .value { font-size: 13px; display: block; margin-top: 5px; }
	        
        /* Khối tính toán Billing */
        .billing-box { width: 100%; margin-bottom: 40px; }
        .billing-left { width: 30%; font-size: 16px; vertical-align: middle; }
        .billing-right { width: 70%; }
        .billing-table { width: 100%; border-collapse: collapse; }
        .billing-table td { padding: 5px 0; font-size: 15px; }
        .billing-table .text-label { text-align: left; color: #555; }
        .billing-table .text-value { text-align: right; font-weight: bold; }
        .total-row { font-size: 18px !important; border-top: 2px solid #333; }
	        
        /* Bảng chi tiết đơn hàng */
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table th { background-color: #f8f9fa; text-align: left; padding: 10px 5px; border-bottom: 2px solid #eee; font-size: 10px; text-transform: uppercase; }
        .details-table td { padding: 10px 5px; border-bottom: 1px solid #eee; font-size: 11px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
	        
    <div class="header">
        <h1>Partner: ' . $partnerName . '</h1>
        <p>Date: ' . $dateRange . '</p>
    </div>
            
    <div class="section-title">'.__('lblReportBillingTotal', true).'</div>
    <table class="summary-grid">
        <tr>
            <td><span class="label">'.__('lblReportBillingTotalBookings', true).':</span><span class="value">'.$total_bookings.'</span></td>
            <td><span class="label">'.__('lblReportBillingTotalAmount', true).':</span><span class="value">'.pjCurrency::formatPrice($total_amount).'</span></td>
            <td><span class="label">'.__('lblReportBillingPaid', true).':</span><span class="value">'.pjCurrency::formatPrice($total_paid).'</span></td>
            <td><span class="label">'.__('lblReportBillingCreditCard', true).':</span><span class="value">'.pjCurrency::formatPrice($total_cc).'</span></td>
            <td><span class="label">'.__('lblReportBillingCash', true).':</span><span class="value">'.pjCurrency::formatPrice($total_cash).'</span></td>
        </tr>
    </table>
                
    <table class="billing-box">
        <tr>
            <td class="billing-left">
                <span style="color: #888;">Commission ' . $commissionRate . '%</span><br>
                <strong style="font-size: 22px;">'.pjCurrency::formatPrice($this->_post->toFloat('commission_amount')).'</strong>
            </td>
            <td class="billing-right">
                <table class="billing-table">
                    <tr><td class="text-label">'.__('lblReportBillingTotal', true).':</td><td class="text-value">'.pjCurrency::formatPrice($total_amount).'</td></tr>
                    <tr><td class="text-label">'.__('lblReportBillingPaid', true).':</td><td class="text-value">'.pjCurrency::formatPrice($total_paid).'</td></tr>
                    <tr><td class="text-label">'.__('lblReportBillingCommission', true).' . ' . $commissionRate . '%:</td><td class="text-value" style="color: red;">-'.pjCurrency::formatPrice((float)$report_arr['commission_amount']).'</td></tr>
                    <tr><td class="text-label">'.__('lblReportBillingPaidBookingsWeMade', true).':</td><td class="text-value">'. ((float)$this->_post->toFloat('paid_bookings_we_made') >= 0 ? '+' : '-') .pjCurrency::formatPrice((float)$this->_post->toFloat('paid_bookings_we_made')).'</td></tr>
                    <tr class="total-row">
                        <td class="text-label"><strong>'.__('lblReportBillingBillingAmount', true).':</strong></td>
                        <td class="text-value"><strong>'. ((float)$this->_post->toFloat('billing_amount') >= 0 ? '+' : '') .pjCurrency::formatPrice((float)$this->_post->toFloat('billing_amount')).'</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
                            
    <table class="details-table">
        <thead>
            <tr>
                <th>'.__('lblReportBillingDate', true).'</th>
                <th>'.__('lblReportBillingFromTo', true).'</th>
                <th class="text-right" align="right">'.__('lblReportBillingPaid', true).'</th>
                <th class="text-right" align="right">'.__('lblReportBillingCreditCard', true).'</th>
                <th class="text-right" align="right">'.__('lblReportBillingCash', true).'</th>
            </tr>
        </thead>
        <tbody>';
	    foreach ($arr['report_arr'] as $order) {
	        $paid = $cc = $cash = 0;
	        if (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(1,5))) {
	            if (isset($custom_amount_arr[$order['id']]['total_cash']) && (float)$custom_amount_arr[$order['id']]['total_cash'] > 0) {
	                $cash = $custom_amount_arr[$order['id']]['total_cash'];
	            } else {
	                $cash = $order['price'];
	            }
	        } elseif (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(2,6))){
	            if (isset($custom_amount_arr[$order['id']]['total_cc']) && (float)$custom_amount_arr[$order['id']]['total_cc'] > 0) {
	                $cc = $custom_amount_arr[$order['id']]['total_cc'];
	            } else {
	                $cc = $order['price'];
	            }
	        } elseif (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(8))){
	            if (isset($custom_amount_arr[$order['id']]['total_paid']) && (float)$custom_amount_arr[$order['id']]['total_paid'] > 0) {
	                $paid = $custom_amount_arr[$order['id']]['total_paid'];
	            } else {
	                $paid = $order['price'];
	            }
	        } elseif ($order['payment_method'] == 'cash'){
	            if (isset($custom_amount_arr[$order['id']]['total_cash']) && (float)$custom_amount_arr[$order['id']]['total_cash'] > 0) {
	                $cash = $custom_amount_arr[$order['id']]['total_cash'];
	            } else {
	                $cash = $order['price'];
	            }
	        } elseif ($order['payment_method'] == 'creditcard_later'){
	            if (isset($custom_amount_arr[$order['id']]['total_cc']) && (float)$custom_amount_arr[$order['id']]['total_cc'] > 0) {
	                $cc = $custom_amount_arr[$order['id']]['total_cc'];
	            } else {
	                $cc = $order['price'];
	            }
	        } else {
	            if (isset($custom_amount_arr[$order['id']]['total_paid']) && (float)$custom_amount_arr[$order['id']]['total_paid'] > 0) {
	                $paid = $custom_amount_arr[$order['id']]['total_paid'];
	            } else {
	                $paid = $order['price'];
	            }
	        }
	        if(!empty($order['return_id'])) {
	            $from_to = pjSanitize::html($order['location2'].' - '.$order['dropoff2']);
	        } else {
	            $from_to = pjSanitize::html($order['location'].' - '.$order['dropoff']);
	        }
	        $html .= '
            <tr>
                <td>'.date($this->option_arr['o_date_format'], strtotime($order['booking_date'])).'</td>
                <td>'.$from_to.'</td>
                <td class="text-right">'.pjCurrency::formatPrice($paid).'</td>
                <td class="text-right">'.pjCurrency::formatPrice($cc).'</td>
                <td class="text-right">'.pjCurrency::formatPrice($cash).'</td>
            </tr>';
	    }
	    $html .= '</tbody>
    </table>
	        
</body>
</html>';
	    
	    $dompdf->loadHtml($html);
	    $dompdf->setPaper('A4', 'portrait');
	    $dompdf->render();
	    //$dompdf->stream("Billing_" . '-'.time() . ".pdf", ["Attachment" => false]);
	    
	    $file_name = $partnerName.' - '.$dateRange.'.pdf';
	    
	    $pdf_file = PJ_UPLOAD_PATH.'files/partners/reports/' . $file_name;
	    $output = $dompdf->output();
	    file_put_contents($pdf_file, $output);
	    
	    pjAppController::jsonResponse(array('pdf_file' => PJ_INSTALL_URL . $pdf_file));
	}
	
	private function getBilling($partner_id, $date_from, $date_to) {
	    $partner_arr = pjPartnerModel::factory()->find($partner_id)->getData();
	    
	    $tblPartnerVehicle = pjPartnerVehicleModel::factory()->getTable();
	    $pjBookingModel = pjBookingModel::factory()
	    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	    ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	    ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
	    ->join('pjClient', "t6.id=t1.client_id", 'left')
	    ->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
	    ->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
	    ->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
	    ->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
	    ->join('pjLocation', 't11.id=t1.location_id', 'left outer')
	    ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))')
	    ->where('t1.admin_confirm_cancelled', 0)
	    ->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.$tblPartnerVehicle.'` WHERE `partner_id`='.(int)$partner_id.')')
	    ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
	    ->whereNotIn('t1.driver_status', array(4,5));
	    
	    $report_arr = $pjBookingModel
	    ->select("t1.*, t2.content as fleet,
	    (IF (t1.return_id != '', (SELECT `uuid` FROM `".pjBookingModel::factory()->getTable()."` WHERE `id`=t1.return_id LIMIT 1), '')) AS return_uuid,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
		t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
		t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
		t6.title, t6.fname, t6.lname, t6.email,t6.phone, t10.content AS c_country_title, t11.color AS location_color")
		->orderBy("t1.booking_date ASC")
		->findAll()
		->getData();
		
		return array('report_arr' => $report_arr, 'partner_arr' => $partner_arr);
	}
	
	public function pjActionDownloadContract()
	{
	    $this->checkLogin();
	    
	    if ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
	        $arr = pjPartnerModel::factory()->find($this->_get->toInt('id'))->getData();
	        if ($arr) {
	            $file_name = $arr['name'].' - '.$arr['company_number'].'.pdf';
	            $pdf_file = PJ_UPLOAD_PATH.'files/partners/contract/'.$file_name;
	            
	            $data = file_get_contents($pdf_file);
	            pjToolkit::download($data, $file_name);
	        } else {
	            echo 'Missing parameter!!!';
	            exit;
	        }
	    } else {
	        echo 'Missing parameter!!!';
	        exit;
	    }
	}
	
	public function pjActionSaveCustomPrice() {
	    $this->setAjax(true);
	    if ($this->_post->check('save_custom_price')) {
	        $pjPartnerReportBookingAmountModel = pjPartnerReportBookingAmountModel::factory();
	        
	        $partner_id = $this->_post->toInt('partner_id');
	        $report_id = $this->_post->toInt('report_id');
	        $tmp_hash = $this->_post->toString('tmp_hash');
	        $booking_id = $this->_post->toInt('booking_id');
	        $column = $this->_post->toString('column');
	        $price = $this->_post->toFloat('price');
	        
	        $pjPartnerReportBookingAmountModel
	        ->where('t1.partner_id', $partner_id)
	        ->where('t1.booking_id', $booking_id);
	        if ((int)$report_id > 0) {
	            $pjPartnerReportBookingAmountModel->where('t1.report_id', $report_id);
	        } else {
	            $pjPartnerReportBookingAmountModel->where('t1.tmp_hash', $tmp_hash);
	        }
	        
	        $arr = $pjPartnerReportBookingAmountModel->limit(1)->findAll()->getDataIndex(0);
	        if ($arr) {
	            $pjPartnerReportBookingAmountModel->reset()->set('id', $arr['id'])->modify(array($column => $price));
	        } else {
	            $pjPartnerReportBookingAmountModel->reset()->setAttributes(array(
	                'partner_id' => $partner_id,
	                'report_id' => $report_id,
	                'tmp_hash' => $tmp_hash,
	                'booking_id' => $booking_id,
	                $column => $price
	            ))->insert();
	        }
	        pjAppController::jsonResponse(array('status' => 'OK'));
	    } else {
	        pjAppController::jsonResponse(array('status' => 'ERR'));
	    }
	}
}
?>