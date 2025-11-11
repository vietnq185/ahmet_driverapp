<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminDrivers extends pjAdmin
{
	public function pjActionCheckEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (!$this->_get->check('email') || $this->_get->toString('email') == '')
			{
				echo 'false';
				exit;
			}
			$pjMainDriverModel = pjMainDriverModel::factory()->where('t1.email', $this->_get->toString('email'));
			if ($this->isDriver())
			{
				$pjMainDriverModel->where('t1.id !=', $this->getUserId());
			} elseif ($this->_get->check('id') && $this->_get->toInt('id') > 0) {
				$pjMainDriverModel->where('t1.id !=', $this->_get->toInt('id'));
			}

			echo $pjMainDriverModel->findCount()->getData() == 0 ? 'true' : 'false';
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
		
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();	
		$this->set('locale_arr', $locale_arr);
		
		$this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminDrivers.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjMainDriverModel = pjMainDriverModel::factory()
			->join('pjBaseLocale', 't2.id=t1.locale_id', 'left outer')
			->where('t1.role_id', 3);
			
		if ($q = $this->_get->toString('q'))
		{
			$q = str_replace(array('_', '%'), array('\_', '\%'), $pjMainDriverModel->escapeStr($q));
			$pjMainDriverModel->where('(t1.name LIKE "%'.$q.'%" OR t1.email LIKE "%'.$q.'%" OR t1.phone LIKE "%'.$q.'%")');
		}
		if ($this->_get->toString('status') && in_array($this->_get->toString('status'), array('T', 'F')))
		{
			$pjMainDriverModel->where('t1.status', $this->_get->toString('status'));
		}
		$column = 'name';
		$direction = 'ASC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjMainDriverModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjMainDriverModel
					->select('t1.id, t1.name, t1.email, t1.phone, t1.status, t1.locale_id, t2.name AS locale')
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		foreach ($data as $k => $v) {
		    $v['name'] = pjSanitize::clean($v['name']);
		    $v['email'] = pjSanitize::clean($v['email']);
		    $v['phone'] = pjSanitize::clean($v['phone']);
		    $v['locale'] = pjSanitize::clean($v['locale']);
			$data[$k] = $v;
		}	
		self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	}
	
	public function pjActionAddDriver()
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
		
		if ($this->_post->check('add_driver'))
		{
			$post = $this->_post->raw();
			$data = array();
			$data['role_id'] = 3;
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			$driver_id = pjMainDriverModel::factory(array_merge($post, $data))->insert()->getInsertId();
			if ($driver_id !== false && (int) $driver_id > 0)
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
		if (pjMainDriverModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
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
		if (pjMainDriverModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
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
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();	
			$this->set('locale_arr', $locale_arr);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (self::isPost() && $this->_post->check('update_driver'))
		{
			$pjMainDriverModel = pjMainDriverModel::factory();
			$arr = $pjMainDriverModel->find($this->_post->toInt('id'))->getData();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if ($arr) {
				$pjMainDriverModel->reset()->set('id', $arr['id'])->modify(array_merge($this->_post->raw(), $data));
			} else {
				$pjMainDriverModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert();
			}			
			self::jsonResponse(array('status' => 'OK'));
		}
		
		if (self::isGet())
		{
			$arr = pjMainDriverModel::factory()->find($this->_get->toInt('id'))->getData();
			$this->set('arr', $arr);
			
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
				$this->set('locale_arr', $locale_arr);
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
		
		$pjMainDriverModel = pjMainDriverModel::factory();
		if (!in_array($params['column'], $pjMainDriverModel->getI18n()))
		{
			$pjMainDriverModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjMainDriver');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionSms()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if ($this->_post->check('send_sms') && $this->_post->check('to') && $this->_post->toString('to') != '' && $this->_post->toString('message') != '')
		{
			$result = pjAppController::messagebirdSendSMS(array($this->_post->toString('to')), $this->_post->toString('message'), $this->option_arr);
			if ($result['status'] == 'OK')
			{
				self::jsonResponse(array('status' => 'OK', 'text' => 'SMS has been sent.'));
			}
			self::jsonResponse(array('status' => 'ERR', 'text' => 'SMS failed to send.'));
		}
		
		if (self::isGet() && $this->_get->check('driver_id') && $this->_get->toInt('driver_id') > 0)
		{
			$driver_arr = pjAuthUserModel::factory()->find($this->_get->toInt('driver_id'))->getData();
			$this->set('arr', array(
				'phone' => $driver_arr['phone'],
				'message' => ''
			));
		} else {
			exit;
		}
	}
}
?>