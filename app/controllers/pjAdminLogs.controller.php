<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminLogs extends pjAdmin
{	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminLogs.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjLogModel = pjLogModel::factory()
		->join('pjAuthUser', 't2.id=t1.user_id', 'left outer');
			
		if ($q = $this->_get->toString('q'))
		{
			$q = str_replace(array('_', '%'), array('\_', '\%'), $pjLogModel->escapeStr($q));
			$pjLogModel->where('t1.action LIKE "%'.$q.'%"');
		}
		
		if ($this->_get->check('date') && $this->_get->toString('date') != '') {
		    $date = pjDateTime::formatDate($this->_get->toString('date'), $this->option_arr['o_date_format']);
		    
		} else {
		    $date = date('Y-m-d');
		}
		$pjLogModel->where("(booking_date='$date')");
		
		$column = 'created';
		$direction = 'DESC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjLogModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjLogModel
					->select('t1.*, t2.name AS created_by')
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		foreach ($data as $k => $v) {
		    $data[$k]['created_by'] = pjSanitize::clean($v['created_by']);
		    $data[$k]['created'] = date($this->option_arr['o_date_format'].', '.$this->option_arr['o_time_format'], strtotime($v['created']));
		    $data[$k]['date'] = date($this->option_arr['o_date_format'], strtotime($v['booking_date'])).', '.date($this->option_arr['o_time_format'], strtotime($v['created']));
		}
		$data = pjSanitize::clean($data);
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
		if (pjLogModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
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
		if (pjLogModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			self::jsonResponse(array('status' => 'OK'));
		}
		
		self::jsonResponse(array('status' => 'ERR'));
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
		
		$pjLogModel = pjLogModel::factory();
		if (!in_array($params['column'], $pjLogModel->getI18n()))
		{
			$pjLogModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjLog');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
}
?>