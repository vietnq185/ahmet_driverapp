<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVehiclesMaintrance extends pjAdmin
{	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$this->set('has_update', pjAuth::factory('pjAdminVehiclesMaintrance', 'pjActionUpdate')->hasAccess());
		$this->set('has_create', pjAuth::factory('pjAdminVehiclesMaintrance', 'pjActionCreate')->hasAccess());
		$this->set('has_delete', pjAuth::factory('pjAdminVehiclesMaintrance', 'pjActionDeleteService')->hasAccess());
		
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminVehiclesMaintrance.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjVehicleMaintranceModel = pjVehicleMaintranceModel::factory()
		->join('pjVehicle', 't2.id=t1.vehicle_id', 'left outer')
		->join('pjMultiLang', "t3.model='pjVehicle' AND t3.foreign_id=t1.vehicle_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer');
			
		if ($q = $this->_get->toString('q'))
		{
		    $q = str_replace(array('_', '%'), array('\_', '\%'), $pjVehicleMaintranceModel->escapeStr($q));
		    $pjVehicleMaintranceModel->where('(t3.content LIKE "%'.$q.'%" OR t1.tuv LIKE "%'.$q.'%" OR t2.registration_number LIKE "%'.$q.'%")');
		}
		
		$column = 'created';
		$direction = 'DESC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjVehicleMaintranceModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjVehicleMaintranceModel
					->select("t1.*, t2.registration_number, t3.content AS vehicle_name, (SELECT CONCAT_WS('~.~', `date`, `km`) FROM `".pjVehicleMaintranceServiceModel::factory()->getTable()."` WHERE `foreign_id`=t1.id ORDER BY `date` DESC LIMIT 1) AS last_service")
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		foreach ($data as $k => $v) {
		    $v['vehicle_name'] = pjSanitize::clean($v['vehicle_name']);
		    $last_service = '';
		    if (!empty($v['last_service'])) {
		        list($date, $km) = explode("~.~", $v['last_service']);
		        $last_service = date($this->option_arr['o_date_format'], strtotime($date)).'<br/>km: '.$km;
		    }
		    $v['last_service'] = $last_service;
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
		if (pjVehicleMaintranceModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
		    // Attributes
		    pjVehicleMaintranceAttributeModel::factory()->reset()->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
		    
		    // Photos & Documents
		    $vm_file_arr = pjVehicleMaintranceFileModel::factory()->where('t1.foreign_id', $this->_get->toInt('id'))->whereIn('t1.type', array('photo','document'))->findAll()->getData();
		    $vm_file_ids_arr = array();
		    @clearstatcache();
		    foreach ($vm_file_arr as $val) {
		        $vm_file_ids_arr[] = $val['id'];
		        if (!empty($val['source_path']) && is_file($val['source_path']))
    		    {
    		        @unlink($val['source_path']);
    		    }
		    }
		    if ($vm_file_ids_arr) {
		        pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $vm_file_ids_arr)->eraseAll();
		    }
		    
		    // Accidents
		    $vm_accident_arr = pjVehicleMaintranceAccidentModel::factory()->where('t1.foreign_id', $this->_get->toInt('id'))->findAll()->getDataPair(null, 'id');
		    if ($vm_accident_arr) {
		        $vm_file_arr = pjVehicleMaintranceFileModel::factory()->reset()->whereIn('t1.foreign_id', $vm_accident_arr)->where('t1.type', 'accident')->findAll()->getData();
		        $vm_file_ids_arr = array();
		        @clearstatcache();
		        foreach ($vm_file_arr as $val) {
		            $vm_file_ids_arr[] = $val['id'];
		            if (!empty($val['source_path']) && is_file($val['source_path']))
		            {
		                @unlink($val['source_path']);
		            }
		        }
		        if ($vm_file_ids_arr) {
		            pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $vm_file_ids_arr)->eraseAll();
		        }
		        pjVehicleMaintranceAccidentModel::factory()->reset()->whereIn('id', $vm_accident_arr)->eraseAll();
		    }
		    
		    // Services
		    $vm_service_arr = pjVehicleMaintranceServiceModel::factory()->where('t1.foreign_id', $this->_get->toInt('id'))->findAll()->getDataPair(null, 'id');
		    if ($vm_service_arr) {
		        $vm_file_arr = pjVehicleMaintranceFileModel::factory()->reset()->whereIn('t1.foreign_id', $vm_service_arr)->where('t1.type', 'service_invoice')->findAll()->getData();
		        $vm_file_ids_arr = array();
		        @clearstatcache();
		        foreach ($vm_file_arr as $val) {
		            $vm_file_ids_arr[] = $val['id'];
		            if (!empty($val['source_path']) && is_file($val['source_path']))
		            {
		                @unlink($val['source_path']);
		            }
		        }
		        if ($vm_file_ids_arr) {
		            pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $vm_file_ids_arr)->eraseAll();
		        }
		        pjVehicleMaintranceServiceModel::factory()->reset()->whereIn('id', $vm_service_arr)->eraseAll();
		        pjVehicleMaintranceServicesTypesModel::factory()->whereIn('service_id', $vm_service_arr)->eraseAll();
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
		if (pjVehicleMaintranceModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
		    // Attributes
		    pjVehicleMaintranceAttributeModel::factory()->reset()->whereIn('foreign_id', $record)->eraseAll();
		    
		    // Photos & Documents
		    $vm_file_arr = pjVehicleMaintranceFileModel::factory()->whereIn('t1.foreign_id', $record)->whereIn('t1.type', array('photo','document'))->findAll()->getData();
		    $vm_file_ids_arr = array();
		    @clearstatcache();
		    foreach ($vm_file_arr as $val) {
		        $vm_file_ids_arr[] = $val['id'];
		        if (!empty($val['source_path']) && is_file($val['source_path']))
		        {
		            @unlink($val['source_path']);
		        }
		    }
		    if ($vm_file_ids_arr) {
		        pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $vm_file_ids_arr)->eraseAll();
		    }
		    
		    // Accidents
		    $vm_accident_arr = pjVehicleMaintranceAccidentModel::factory()->whereIn('t1.foreign_id', $record)->findAll()->getDataPair(null, 'id');
		    if ($vm_accident_arr) {
		        $vm_file_arr = pjVehicleMaintranceFileModel::factory()->reset()->whereIn('t1.foreign_id', $vm_accident_arr)->where('t1.type', 'accident')->findAll()->getData();
		        $vm_file_ids_arr = array();
		        @clearstatcache();
		        foreach ($vm_file_arr as $val) {
		            $vm_file_ids_arr[] = $val['id'];
		            if (!empty($val['source_path']) && is_file($val['source_path']))
		            {
		                @unlink($val['source_path']);
		            }
		        }
		        if ($vm_file_ids_arr) {
		            pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $vm_file_ids_arr)->eraseAll();
		        }
		        pjVehicleMaintranceAccidentModel::factory()->reset()->whereIn('id', $vm_accident_arr)->eraseAll();
		    }
		    
		    // Services
		    $vm_service_arr = pjVehicleMaintranceServiceModel::factory()->whereIn('t1.foreign_id', $record)->findAll()->getDataPair(null, 'id');
		    if ($vm_service_arr) {
		        $vm_file_arr = pjVehicleMaintranceFileModel::factory()->reset()->whereIn('t1.foreign_id', $vm_service_arr)->where('t1.type', 'service_invoice')->findAll()->getData();
		        $vm_file_ids_arr = array();
		        @clearstatcache();
		        foreach ($vm_file_arr as $val) {
		            $vm_file_ids_arr[] = $val['id'];
		            if (!empty($val['source_path']) && is_file($val['source_path']))
		            {
		                @unlink($val['source_path']);
		            }
		        }
		        if ($vm_file_ids_arr) {
		            pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $vm_file_ids_arr)->eraseAll();
		        }
		        pjVehicleMaintranceServiceModel::factory()->reset()->whereIn('id', $vm_service_arr)->eraseAll();
		        pjVehicleMaintranceServicesTypesModel::factory()->whereIn('service_id', $vm_service_arr)->eraseAll();
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
	        $data = array();
	        $post = $this->_post->raw();
	        if (isset($post['buy_date']) && !empty($post['buy_date']))
	        {
	            $data['buy_date'] = pjDateTime::formatDate($post['buy_date'], $this->option_arr['o_date_format']);
	        }
	        $id = pjVehicleMaintranceModel::factory()->setAttributes(array_merge($post, $data))->insert()->getInsertId();
	        if ($id !== false && (int)$id > 0) {
	            pjVehicleMaintranceFileModel::factory()
	            ->whereNotIn('type', array('accident','service_invoice'))
	            ->where('tmp_hash', $this->_post->toString('foreign_id'))
	            ->modifyAll(array('foreign_id' => $id, 'tmp_hash' => ':NULL'));
	            
	            pjVehicleMaintranceAccidentModel::factory()
	            ->where('tmp_hash', $this->_post->toString('foreign_id'))
	            ->modifyAll(array('foreign_id' => $id, 'tmp_hash' => ':NULL'));
	            
	            pjVehicleMaintranceServiceModel::factory()
	            ->where('tmp_hash', $this->_post->toString('foreign_id'))
	            ->modifyAll(array('foreign_id' => $id, 'tmp_hash' => ':NULL'));
	            
	            $pjVehicleMaintranceAttributeTypeModel = pjVehicleMaintranceAttributeTypeModel::factory();
	            if (isset($post['attr_cat'])) {
	                $pjVehicleMaintranceAttributeModel = pjVehicleMaintranceAttributeModel::factory()->setBatchFields(array('foreign_id', 'attribute_type_id', 'content'));
	                foreach ($post['attr_cat'] as $k => $v) {
	                    if ($v == 'other') {
	                        if (!empty($post['new_attr_cat'][$k])) {
    	                        $attribute_type_id = $pjVehicleMaintranceAttributeTypeModel->reset()->setAttributes(array('status' => 'T'))->insert()->getInsertId();
    	                        if ($attribute_type_id !== false && (int) $attribute_type_id > 0)
    	                        {
    	                            $i18n_arr[$this->getLocaleId()] = array(
    	                                'name' =>  $post['new_attr_cat'][$k]
    	                            );
    	                            if (!empty($i18n_arr))
    	                            {
    	                                pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $attribute_type_id, 'pjVehicleMaintranceAttributeType');
    	                            }
    	                        }
	                        }
	                    } else {
	                        $attribute_type_id = $v;
	                    }
	                    $pjVehicleMaintranceAttributeModel->addBatchRow(array($id, $attribute_type_id, $post['attr_content'][$k]));
	                }
	                $pjVehicleMaintranceAttributeModel->insertBatch();
	            }
	            $err = 'AVM01';
	        } else {
	            $err = 'AVM04';
	        }
	        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVehiclesMaintrance&action=pjActionIndex&err=$err");
	    }
		if (self::isGet())
		{
		    $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
		    ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		    ->where('t1.status', 'T')
		    ->where('t1.type', 'own')
		    ->orderBy('t1.order ASC, t2.content ASC')
		    ->findAll()
		    ->getData();
		    $this->set('vehicle_arr', $vehicle_arr);
		    
		    $attribute_arr = pjVehicleMaintranceAttributeTypeModel::factory()->select('t1.*, t2.content AS name')
		    ->join('pjMultiLang', "t2.model='pjVehicleMaintranceAttributeType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		    ->where('t1.status', 'T')
		    ->orderBy('t2.content ASC')
		    ->findAll()
		    ->getData();
		    $this->set('attribute_arr', $attribute_arr);
		    
		    $tmp_hash = $this->generateAlphaHash();
		    $this->set('tmp_hash', $tmp_hash);
		    
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
		    $this->appendJs('pjAdminVehiclesMaintrance.js');
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
		    $pjVehicleMaintranceModel = pjVehicleMaintranceModel::factory();
		    $data = array();
		    $post = $this->_post->raw();
		    if (isset($post['buy_date']) && !empty($post['buy_date']))
		    {
		        $data['buy_date'] = pjDateTime::formatDate($post['buy_date'], $this->option_arr['o_date_format']);
		    }
		    $pjVehicleMaintranceModel->set('id', $this->_post->toInt('id'))->modify(array_merge($post, $data));
		    
		    $pjVehicleMaintranceAttributeTypeModel = pjVehicleMaintranceAttributeTypeModel::factory();
		    if (isset($post['attr_cat'])) {
		        $pjVehicleMaintranceAttributeModel = pjVehicleMaintranceAttributeModel::factory();
		        foreach ($post['attr_cat'] as $k => $v) {
		            $data_attr = array();
		            $data_attr['foreign_id'] = $this->_post->toInt('id');
		            if ($v == 'other') {
		                $attribute_type_id = $pjVehicleMaintranceAttributeTypeModel->reset()->setAttributes(array('status' => 'T'))->insert()->getInsertId();
		                if ($attribute_type_id !== false && (int) $attribute_type_id > 0)
		                {
		                    $i18n_arr[$this->getLocaleId()] = array(
		                        'name' =>  $post['new_attr_cat'][$k]
		                    );
		                    if (!empty($i18n_arr))
		                    {
		                        pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $attribute_type_id, 'pjVehicleMaintranceAttributeType');
		                    }
		                }
		            } else {
		                $attribute_type_id = $v;
		            }
		            $data_attr['attribute_type_id'] = $attribute_type_id;
		            $data_attr['content'] = $post['attr_content'][$k];
		            if(strpos($k, 'new_') !== false) {
		                $pjVehicleMaintranceAttributeModel->reset()->setAttributes($data_attr)->insert();
		            } else {
		                $pjVehicleMaintranceAttributeModel->reset()->set('id', $k)->modify($data_attr);
		            }
		        }
		    }
		    
		    pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionIndex&err=AVM03");
		}
		
		if (self::isGet())
		{
			$arr = pjVehicleMaintranceModel::factory()->find($this->_get->toInt('id'))->getData();
			if (empty($arr))
			{
			    pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionIndex&err=AVM08");
			}
			$this->set('arr', $arr);
			
			$vm_attr_arr = pjVehicleMaintranceAttributeModel::factory()->where('t1.foreign_id', $arr['id'])->findAll()->getData();
			$this->set('vm_attr_arr', $vm_attr_arr);
			
			$vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->where('t1.status', 'T')
			->where('t1.type', 'own')
			->orderBy('t1.order ASC, t2.content ASC')
			->findAll()
			->getData();
			$this->set('vehicle_arr', $vehicle_arr);
			
			$attribute_arr = pjVehicleMaintranceAttributeTypeModel::factory()->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjVehicleMaintranceAttributeType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->where('t1.status', 'T')
			->orderBy('t2.content ASC')
			->findAll()
			->getData();
			$this->set('attribute_arr', $attribute_arr);
			
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
			$this->appendJs('pjAdminVehiclesMaintrance.js');
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
	    $uploadType = $_REQUEST['upload_type'];
	    $file = $_FILES['file'];
	    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	    $allowed = [
	        'vehicle_photos' => ['jpg', 'jpeg', 'png', 'gif'],
	        'vehicle_documents'   => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
	        'vehicle_accidents' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
	        'service_invoice'   => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']
	    ];
	    if (!isset($allowed[$uploadType]) || !in_array($extension, $allowed[$uploadType])) {
	        echo json_encode(['status' => 'ERR', 'msg' => 'Định dạng file không được hỗ trợ cho loại này.']);
	        exit;
	    }
	    
	    $map_type_arr = array(
	       'vehicle_photos' => 'photo',
	       'vehicle_documents' => 'document',
	       'vehicle_accidents' => 'accident',
	       'service_invoice' => 'service_invoice'
	    );
	    
	    $randomPart = bin2hex(random_bytes(10));
	    $uniquePart = uniqid('', true);
	    $filename = $randomPart . '_' . $uniquePart . '.' . $extension;
	    
	    $file_path = PJ_UPLOAD_PATH.'files/'.$uploadType.'/'.$filename;
	    if (@move_uploaded_file($file['tmp_name'], $file_path)) {
	        $data = array();
	        if (isset($_REQUEST['foreign_id']) && (int)$_REQUEST['foreign_id'] > 0) {
	            $data['foreign_id'] = (int)$_REQUEST['foreign_id'];
	        } else {
	            $data['tmp_hash'] = $_REQUEST['foreign_id'];
	        }
	        $data['filename'] = $filename;
	        $data['source_path'] = $file_path;
	        $data['type'] = $map_type_arr[$uploadType];
	        
	        pjVehicleMaintranceFileModel::factory()->setAttributes($data)->insert();
	    }
	    echo 'OK';
	    exit;
	}
	
	public function pjActionGetFiles() {
	    $this->setAjax(true);
	    $pjVehicleMaintranceFileModel = pjVehicleMaintranceFileModel::factory();
	    if ($this->_get->check('foreign_id')) {
	        if ($this->_get->toInt('foreign_id') > 0) {
	            $pjVehicleMaintranceFileModel->where('t1.foreign_id', $this->_get->toInt('foreign_id'));
	        } else {
	            $pjVehicleMaintranceFileModel->where('t1.tmp_hash', $this->_get->toString('foreign_id'));
	        }
	    } else {
	        $pjVehicleMaintranceFileModel->where('t1.foreign_id', '-9999');
	    }
	    $map_type_arr = array(
	        'vehicle_photos' => 'photo',
	        'vehicle_documents' => 'document',
	        'vehicle_accidents' => 'accident',
	        'service_invoice' => 'service_invoice'
	    );
	    $type = @$map_type_arr[$this->_get->toString('type')];
	    
	    $pjVehicleMaintranceFileModel->where('t1.type', $type);
	    $arr = $pjVehicleMaintranceFileModel->orderBy('t1.created ASC')->findAll()->getData();
	    $this->set('arr', $arr);
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
	        $pjVehicleMaintranceFileModel = pjVehicleMaintranceFileModel::factory();
	        $arr = $pjVehicleMaintranceFileModel->find($id)->getData();
	        if (!empty($arr))
	        {
	            $pjVehicleMaintranceFileModel->reset()->set('id', $id)->erase();
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
	        $arr = pjVehicleMaintranceFileModel::factory()->find($this->_get->toInt('id'))->getData();
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
	
	public function pjActionAccidentForm() {
	    $this->setAjax(true);
	    if ($this->_post->check('save_accident')) {
	        $data = array();
	        $post = $this->_post->raw();
	        if (isset($post['date']) && !empty($post['date']))
	        {
	            $data['date'] = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
	        }
	        if (isset($post['time']) && !empty($post['time'])) {
	            $data['time'] = pjDateTime::formatTime($post['time'], $this->option_arr['o_time_format'], 'H:i:s');
	        }
	        $data['is_second_vehicle_involved'] = isset($post['is_second_vehicle_involved']) ? 1 : 0;
	        if ($data['is_second_vehicle_involved'] == 0) {
	            $data['second_driver_name'] = ':NULL';
	            $data['second_licence_plate_number'] = ':NULL';
	            $data['second_licence_plate_number'] = ':NULL';
	        }
	        if ((int)$post['maintrance_id'] > 0) {
	            $data['foreign_id'] = (int)$post['maintrance_id'];
	        } else {
	            $data['tmp_hash'] = $post['maintrance_id'];
	        }
	        if ((int)$post['accident_id'] > 0) {
	            pjVehicleMaintranceAccidentModel::factory()->set('id', $post['accident_id'])->modify(array_merge($post, $data));
	        } else {
	            $id = pjVehicleMaintranceAccidentModel::factory()->setAttributes(array_merge($post, $data))->insert()->getInsertId();
	            if ($id !== false && (int)$id > 0) {
	                pjVehicleMaintranceFileModel::factory()
	                ->where('type', 'accident')
	                ->where('tmp_hash', $this->_post->toString('accident_id'))
	                ->modifyAll(array('foreign_id' => $id, 'tmp_hash' => ':NULL'));
	            }
	        }
	        
	        pjAppController::jsonResponse(array('status' => 'OK'));
	    }
	    if ($this->_get->check('accident_id') && $this->_get->toInt('accident_id') > 0) {
	        $accident_id = $this->_get->toInt('accident_id');
	        $arr = pjVehicleMaintranceAccidentModel::factory()->find($accident_id)->getData();
	        $this->set('arr', $arr);
	    } else {
	        $accident_id = $this->generateAlphaHash();
	    }
	    
	    $this->set('accident_id', $accident_id);
	}
	
	public function pjActionGetAccidents() {
	    $this->setAjax(true);
	    $pjVehicleMaintranceAccidentModel = pjVehicleMaintranceAccidentModel::factory();
	    if ($this->_get->toInt('foreign_id') > 0) {
	        $pjVehicleMaintranceAccidentModel->where('t1.foreign_id', $this->_get->toInt('foreign_id'));
	    } else {
	        $pjVehicleMaintranceAccidentModel->where('t1.tmp_hash', $this->_get->toString('foreign_id'));
	    }
	    $arr = $pjVehicleMaintranceAccidentModel->orderBy('t1.date DESC, t1.time DESC')->findAll()->getData();
	    $this->set('arr', $arr);
	}
	
	public function pjActionDeleteAccident() {
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
	        $pjVehicleMaintranceAccidentModel = pjVehicleMaintranceAccidentModel::factory();
	        $arr = $pjVehicleMaintranceAccidentModel->find($id)->getData();
	        if (!empty($arr))
	        {
	            $pjVehicleMaintranceAccidentModel->reset()->set('id', $id)->erase();
	            
	            $file_arr = pjVehicleMaintranceFileModel::factory()
	            ->where('t1.foreign_id', $arr['id'])
	            ->where('t1.type', 'accident')
	            ->findAll()->getData();
	            if ($file_arr) {
    	            @clearstatcache();
    	            $file_ids_arr = array();
    	            foreach ($file_arr as $val) {
    	                $file_ids_arr[] = $val['id'];
    	                if (!empty($val['source_path']) && is_file($val['source_path']))
        	            {
        	                @unlink($val['source_path']);
        	            }
    	            }
    	            pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $file_ids_arr)->eraseAll();
	            }
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	        }
	    }
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
	
	public function pjActionServiceForm() {
	    $this->setAjax(true);
	    if ($this->_post->check('save_service')) {
	        $data = array();
	        $post = $this->_post->raw();
	        if (isset($post['date']) && !empty($post['date']))
	        {
	            $data['date'] = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
	        }
	        if ((int)$post['maintrance_id'] > 0) {
	            $data['foreign_id'] = (int)$post['maintrance_id'];
	        } else {
	            $data['tmp_hash'] = $post['maintrance_id'];
	        }
	        if ((int)$post['service_id'] > 0) {
	            $id = (int)$post['service_id'];
	            pjVehicleMaintranceServiceModel::factory()->set('id', $id)->modify(array_merge($post, $data));
	        } else {
	            $id = pjVehicleMaintranceServiceModel::factory()->setAttributes(array_merge($post, $data))->insert()->getInsertId();
	            if ($id !== false && (int)$id > 0) {
	                pjVehicleMaintranceFileModel::factory()
	                ->where('type', 'service_invoice')
	                ->where('tmp_hash', $this->_post->toString('service_id'))
	                ->modifyAll(array('foreign_id' => $id, 'tmp_hash' => ':NULL'));
	            }
	        }
	        
	        if ($id !== false && (int)$id > 0) {
	            pjVehicleMaintranceServicesTypesModel::factory()->where('service_id', $id)->eraseAll();
	            if (isset($post['service_type_id']) && count($post['service_type_id']) > 0) {
    	            $pjVehicleMaintranceServicesTypesModel = pjVehicleMaintranceServicesTypesModel::factory()->reset()->setBatchFields(array('service_id', 'type_id'));
    	            foreach ($post['service_type_id'] as $k => $v) {
    	                $pjVehicleMaintranceServicesTypesModel->addBatchRow(array($id, $v));
    	            }
    	            $pjVehicleMaintranceServicesTypesModel->insertBatch();
	            }
	        }
	            
	        pjAppController::jsonResponse(array('status' => 'OK'));
	    }
	    if ($this->_get->check('service_id') && $this->_get->toInt('service_id') > 0) {
	        $service_id = $this->_get->toInt('service_id');
	        $arr = pjVehicleMaintranceServiceModel::factory()->find($service_id)->getData();
	        $this->set('arr', $arr);
	        
	        $service_type_ids_arr = pjVehicleMaintranceServicesTypesModel::factory()->where('t1.service_id', $service_id)->findAll()->getDataPair(null, 'type_id');
	        $this->set('service_type_ids_arr', $service_type_ids_arr);
	    } else {
	        $service_id = $this->generateAlphaHash();
	    }
	    
	    $this->set('service_id', $service_id);
	    
	    $service_type_arr = pjVehicleMaintranceServiceTypeModel::factory()->select('t1.*, t2.content AS name')
	    ->join('pjMultiLang', "t2.model='pjVehicleMaintranceServiceType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	    ->where('t1.status', 'T')
	    ->orderBy('t2.content ASC')
	    ->findAll()
	    ->getData();
	    $this->set('service_type_arr', $service_type_arr);
	}
	
	public function pjActionGetServices() {
	    $this->setAjax(true);
	    $pjVehicleMaintranceServiceModel = pjVehicleMaintranceServiceModel::factory()
	    ->join('pjMultiLang', "t2.model='pjVehicleMaintranceServiceType' AND t2.foreign_id=t1.service_type_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
	    if ($this->_get->toInt('foreign_id') > 0) {
	        $pjVehicleMaintranceServiceModel->where('t1.foreign_id', $this->_get->toInt('foreign_id'));
	    } else {
	        $pjVehicleMaintranceServiceModel->where('t1.tmp_hash', $this->_get->toString('foreign_id'));
	    }
	    
	    $tblServicesTypes = pjVehicleMaintranceServicesTypesModel::factory()->getTable();
	    $tblMultiLang = pjMultiLangModel::factory()->getTable();
	    $arr = $pjVehicleMaintranceServiceModel
	    ->select("t1.*, (
            SELECT GROUP_CONCAT(ml.content SEPARATOR '<br/>') FROM `".$tblServicesTypes."` AS st 
            LEFT OUTER JOIN `".$tblMultiLang."` AS ml ON ml.foreign_id=st.type_id AND ml.model='pjVehicleMaintranceServiceType'
                AND ml.field='name' AND ml.locale='".$this->getLocaleId()."' WHERE st.service_id=t1.id LIMIT 1
        ) AS service_types")->orderBy('t1.date DESC')->findAll()->getData();
	    $this->set('arr', $arr);
	}
	
	public function pjActionDeleteService() {
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
	        $pjVehicleMaintranceServiceModel = pjVehicleMaintranceServiceModel::factory();
	        $arr = $pjVehicleMaintranceServiceModel->find($id)->getData();
	        if (!empty($arr))
	        {
	            $pjVehicleMaintranceServiceModel->reset()->set('id', $id)->erase();
	            pjVehicleMaintranceServicesTypesModel::factory()->where('service_id', $id)->eraseAll();
	            
	            $file_arr = pjVehicleMaintranceFileModel::factory()
	            ->where('t1.foreign_id', $arr['id'])
	            ->where('t1.type', 'service_invoice')
	            ->findAll()->getData();
	            if ($file_arr) {
	                @clearstatcache();
	                $file_ids_arr = array();
	                foreach ($file_arr as $val) {
	                    $file_ids_arr[] = $val['id'];
	                    if (!empty($val['source_path']) && is_file($val['source_path']))
	                    {
	                        @unlink($val['source_path']);
	                    }
	                }
	                pjVehicleMaintranceFileModel::factory()->reset()->whereIn('id', $file_ids_arr)->eraseAll();
	            }
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	        }
	    }
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
	
	public function pjActionDeleteAttribute() {
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
	        pjVehicleMaintranceAttributeModel::factory()->set('id', $id)->erase();
	        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	    }
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
}
?>