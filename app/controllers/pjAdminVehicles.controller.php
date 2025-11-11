<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVehicles extends pjAdmin
{
	public function pjActionCheckRegistrationNumber()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (!$this->_get->check('registration_number') || $this->_get->toString('registration_number') == '')
			{
				echo 'false';
				exit;
			}
			$pjVehicleModel = pjVehicleModel::factory();
			if ($this->_get->check('id') && $this->_get->toInt('id') > 0)
			{
				$pjVehicleModel->where('t1.id !=', $this->_get->toInt('id'));
			}
			echo $pjVehicleModel->where('t1.registration_number', $this->_get->toString('registration_number'))->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		
		$this->setLocalesData();
		
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminVehicles.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjVehicleModel = pjVehicleModel::factory()
			->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
			
		if ($q = $this->_get->toString('q'))
		{
			$q = str_replace(array('_', '%'), array('\_', '\%'), $pjVehicleModel->escapeStr($q));
			$pjVehicleModel->where('t2.content LIKE "%'.$q.'%"');
		}
		if ($this->_get->toString('status') && in_array($this->_get->toString('status'), array('T', 'F')))
		{
			$pjVehicleModel->where('t1.status', $this->_get->toString('status'));
		}
		$column = 'name';
		$direction = 'ASC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjVehicleModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjVehicleModel
					->select('t1.*, t2.content AS name')
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		foreach ($data as $k => $v) {
		    $v['name'] = pjSanitize::clean($v['name']);
		    $v['registration_number'] = pjSanitize::clean($v['registration_number']);
			$data[$k] = $v;
		}	
		self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	}
	
	public function pjActionAddVehicle()
	{
		$this->setAjax(true);
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		
		if ($this->_post->check('add_vehicle'))
		{
			$post = $this->_post->raw();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if (isset($post['tuv']) && !empty($post['tuv']))
			{
			    $data['tuv'] = pjDateTime::formatDate($post['tuv'], $this->option_arr['o_date_format']);
			}
			$vehicle_id = pjVehicleModel::factory(array_merge($post, $data))->insert()->getInsertId();
			if ($vehicle_id !== false && (int) $vehicle_id > 0)
			{
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $vehicle_id, 'pjVehicle');
				}
				self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
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
		if (pjVehicleModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
			pjMultiLangModel::factory()->where('model', 'pjVehicle')->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
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
		if (pjVehicleModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			pjMultiLangModel::factory()->where('model', 'pjVehicle')->whereIn('foreign_id', $record)->eraseAll();
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionCreate()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (self::isGet())
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
				
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file'];
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', self::jsonEncode($lp_arr));
	
			$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
		}
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (self::isPost() && $this->_post->check('update_vehicle'))
		{
			$pjVehicleModel = pjVehicleModel::factory();
			$arr = $pjVehicleModel->find($this->_post->toInt('id'))->getData();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if ($this->_post->check('tuv') && $this->_post->toString('tuv') != '')
			{
			    $data['tuv'] = pjDateTime::formatDate($this->_post->toString('tuv'), $this->option_arr['o_date_format']);
			}
			if ($arr) {
				$vehicle_id = $this->_post->toInt('id');
				$pjVehicleModel->reset()->set('id', $vehicle_id)->modify(array_merge($this->_post->raw(), $data));
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $vehicle_id, 'pjVehicle');
				}
			} else {
				$vehicle_id = $pjVehicleModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $vehicle_id, 'pjVehicle');
				}
			}			
			self::jsonResponse(array('status' => 'OK'));
		}
		
		if (self::isGet())
		{
			$arr = pjVehicleModel::factory()->find($this->_get->toInt('id'))->getData();
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjVehicle');
			$this->set('arr', $arr);
			
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file'];
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', self::jsonEncode($lp_arr));
	
			$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
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
		
		$pjVehicleModel = pjVehicleModel::factory();
		if (!in_array($params['column'], $pjVehicleModel->getI18n()))
		{
			$pjVehicleModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjVehicle');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionGetServices()
	{
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    
	    if (!self::isGet())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'HTTP method not allowed.'));
	    }
	    
	    $arr = array();
	    if ($this->_get->check('vehicle_id') && $this->_get->toInt('vehicle_id') > 0) {
	        $arr = pjVehicleServiceModel::factory()->select('t1.*, t2.content AS service_repair')
	        ->join('pjMultiLang', "t2.model='pjVehicleService' AND t2.foreign_id=t1.id AND t2.field='service' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	        ->where('t1.vehicle_id', $this->_get->toInt('vehicle_id'))
	        ->orderBy('t1.date DESC')
	        ->findAll()->getData();
	    }
	    $this->set('arr', $arr);
	}
	
	public function pjActionAddService()
	{
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    
	    if ($this->_post->check('add_service'))
	    {
	        $data = array();
	        if ($this->_post->check('date') && $this->_post->toString('date') != '')
	        {
	            $data['date'] = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
	        }
	        $id = pjVehicleServiceModel::factory()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
	        if ($id !== false && (int)$id > 0) {
	            $i18n_arr = $this->_post->toI18n('i18n');
	            if (!empty($i18n_arr))
	            {
	                pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $id, 'pjVehicleService');
	            }
	            self::jsonResponse(array('status' => 'OK', 'text' => 'Service added!'));
	        } else {
	           self::jsonResponse(array('status' => 'ERR', 'text' => 'Failed to add service!'));
	        }
	    }
	    
	    if (self::isGet() && $this->_get->check('vehicle_id') && $this->_get->toInt('vehicle_id') > 0)
	    {
	        $locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
	        ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	        ->where('t2.file IS NOT NULL')
	        ->orderBy('t1.sort ASC')->findAll()->getData();
	        
	        $lp_arr = array();
	        foreach ($locale_arr as $item)
	        {
	            $lp_arr[$item['id']."_"] = $item['file'];
	        }
	        $this->set('lp_arr', $locale_arr);
	        $this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
	        
	        $arr = pjVehicleModel::factory()->find($this->_get->toInt('vehicle_id'))->getData();
	        $this->set('arr', $arr);
	    } else {
	        exit;
	    }
	}
	
	public function pjActionUpdateService()
	{
	    $this->setAjax(true);
	    
	    if (!$this->isXHR())
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
	    }
	    
	    if ($this->_post->check('update_service'))
	    {
	        $data = array();
	        if ($this->_post->check('date') && $this->_post->toString('date') != '')
	        {
	            $data['date'] = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
	        }
	        
	        pjVehicleServiceModel::factory()->set('id', $this->_post->toInt('id'))->modify(array_merge($this->_post->raw(), $data));
	        $i18n_arr = $this->_post->toI18n('i18n');
	        if (!empty($i18n_arr))
	        {
	            pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->_post->toInt('id'), 'pjVehicleService');
	        }
	        
	        self::jsonResponse(array('status' => 'OK', 'text' => 'Service updated!'));
	    }
	    
	    if (self::isGet())
	    {
	        $arr = pjVehicleServiceModel::factory()->find($this->_get->toInt('id'))->getData();
	        $arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjVehicleService');
	        $this->set('arr', $arr);
	        
	        $locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
	        ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	        ->where('t2.file IS NOT NULL')
	        ->orderBy('t1.sort ASC')->findAll()->getData();
	        
	        $lp_arr = array();
	        foreach ($locale_arr as $item)
	        {
	            $lp_arr[$item['id']."_"] = $item['file'];
	        }
	        $this->set('lp_arr', $locale_arr);
	        $this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
	    } else {
	        exit;
	    }
	}
	
	public function pjActionDeleteService()
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
	    
	    if (!self::isGet() && !$this->_post->check('id') && $this->_post->toInt('id') < 0)
	    {
	        self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
	    }
	    if (pjVehicleServiceModel::factory()->set('id', $this->_post->toInt('id'))->erase()->getAffectedRows() == 1)
	    {
	        pjMultiLangModel::factory()->where('model', 'pjVehicleService')->where('foreign_id', $this->_post->toInt('id'))->eraseAll();
	        $response = array('status' => 'OK');
	    } else {
	        $response = array('status' => 'ERR');
	    }
	    
	    self::jsonResponse($response);
	}
}
?>