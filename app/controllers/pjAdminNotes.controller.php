<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminNotes extends pjAdmin
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
		->orderBy('t1.order ASC, t2.content ASC')
		->findAll()
		->getData();
		$this->set('vehicle_arr', $vehicle_arr);
		
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminNotes.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjNoteModel = pjNoteModel::factory()
		->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.vehicle_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjVehicle', 't3.id=t1.vehicle_id', 'left outer');
			
		if ($q = $this->_get->toString('q'))
		{
		    $q = str_replace(array('_', '%'), array('\_', '\%'), $pjNoteModel->escapeStr($q));
			$pjNoteModel->where('(t2.content LIKE "%'.$q.'%" OR t1.notes LIKE "%'.$q.'%")');
		}
		if ($this->_get->toString('status') && in_array($this->_get->toString('status'), array('T', 'F')))
		{
		    $pjNoteModel->where('t1.status', $this->_get->toString('status'));
		}
		$column = 'date';
		$direction = 'DESC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjNoteModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjNoteModel
					->select('t1.*, t2.content AS vehicle_name, t3.registration_number')
					->orderBy("`$column` $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		$_order_shift = __('_order_shift', true);
		foreach ($data as $k => $v) {
		    $v['vehicle_name'] = pjSanitize::clean($v['vehicle_name']).' | '.pjSanitize::clean($v['registration_number']);
		    $v['notes'] = nl2br($v['notes']);
		    $v['date'] = date($this->option_arr['o_date_format'], strtotime($v['date']));
		    $v['vehicle_order'] = @$_order_shift[$v['vehicle_order']];
			$data[$k] = $v;
		}	
		self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	}
	
	public function pjActionAdd()
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
		
		if ($this->_post->check('add_note'))
		{
			$post = $this->_post->raw();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if (isset($post['date']) && !empty($post['date']))
			{
			    $data['date'] = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
			}
			$note_id = pjNoteModel::factory(array_merge($post, $data))->insert()->getInsertId();
			if ($note_id !== false && (int) $note_id > 0)
			{
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
		if (pjNoteModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
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
		if (pjNoteModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
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
		    $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
		    ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		    ->where('t1.status', 'T')
		    ->orderBy('t1.order ASC, t2.content ASC')
		    ->findAll()
		    ->getData();
		    $this->set('vehicle_arr', $vehicle_arr);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (self::isPost() && $this->_post->check('update_note'))
		{
		    $pjNoteModel = pjNoteModel::factory();
		    $arr = $pjNoteModel->find($this->_post->toInt('id'))->getData();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if ($this->_post->check('date') && $this->_post->toString('date') != '')
			{
			    $data['date'] = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
			}
			if ($arr) {
				$note_id = $this->_post->toInt('id');
				$pjNoteModel->reset()->set('id', $note_id)->modify(array_merge($this->_post->raw(), $data));
			} else {
			    $note_id = $pjNoteModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
			}			
			self::jsonResponse(array('status' => 'OK'));
		}
		
		if (self::isGet())
		{
			$arr = pjNoteModel::factory()->find($this->_get->toInt('id'))->getData();
			$this->set('arr', $arr);
			
			$vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->where('t1.status', 'T')
			->orderBy('t1.order ASC, t2.content ASC')
			->findAll()
			->getData();
			$this->set('vehicle_arr', $vehicle_arr);
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
		
		$pjNoteModel = pjNoteModel::factory();
		if (!in_array($params['column'], $pjNoteModel->getI18n()))
		{
		    $pjNoteModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjNote');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
}
?>