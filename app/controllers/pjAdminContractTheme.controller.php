<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminContractTheme extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminContractTheme.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjContractThemeModel = pjContractThemeModel::factory();
			
		if ($q = $this->_get->toString('q'))
		{
		    $q = str_replace(array('_', '%'), array('\_', '\%'), $pjContractThemeModel->escapeStr($q));
			$pjContractThemeModel->where('t1.name LIKE "%'.$q.'%"');
		}
		if ($this->_get->toString('status') && in_array($this->_get->toString('status'), array('T', 'F')))
		{
		    $pjContractThemeModel->where('t1.status', $this->_get->toString('status'));
		}
		$column = 'name';
		$direction = 'ASC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjContractThemeModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjContractThemeModel
					->select('t1.*')
					->orderBy("`$column` $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		foreach ($data as $k => $v) {
		    $v['vehicle_name'] = pjSanitize::clean($v['name']);
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
		
		if ($this->_post->check('action_add'))
		{
			$post = $this->_post->raw();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			$id = pjContractThemeModel::factory(array_merge($post, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
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
		if (pjContractThemeModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
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
		if (pjContractThemeModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
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
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (self::isPost() && $this->_post->check('action_update'))
		{
		    $pjContractThemeModel = pjContractThemeModel::factory();
		    $arr = $pjContractThemeModel->find($this->_post->toInt('id'))->getData();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if ($arr) {
				$id = $this->_post->toInt('id');
				$pjContractThemeModel->reset()->set('id', $id)->modify(array_merge($this->_post->raw(), $data));
			} else {
			    $id = $pjContractThemeModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
			}			
			self::jsonResponse(array('status' => 'OK'));
		}
		
		if (self::isGet())
		{
		    $arr = pjContractThemeModel::factory()->find($this->_get->toInt('id'))->getData();
			$this->set('arr', $arr);
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
		
		$pjContractThemeModel = pjContractThemeModel::factory();
		if (!in_array($params['column'], $pjContractThemeModel->getI18n()))
		{
		    $pjContractThemeModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjContractTheme');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
}
?>