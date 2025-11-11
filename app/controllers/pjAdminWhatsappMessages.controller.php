<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminWhatsappMessages extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		
		$this->setLocalesData();
		
		$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminWhatsappMessages.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjWhatsappMessageModel = pjWhatsappMessageModel::factory()
			->join('pjMultiLang', "t2.model='pjWhatsappMessage' AND t2.foreign_id=t1.id AND t2.field='subject' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
			
		if ($q = $this->_get->toString('q'))
		{
			$q = str_replace(array('_', '%'), array('\_', '\%'), $pjWhatsappMessageModel->escapeStr($q));
			$pjWhatsappMessageModel->where('t2.content LIKE "%'.$q.'%"');
		}
		if ($this->_get->toString('status') && in_array($this->_get->toString('status'), array('T', 'F')))
		{
			$pjWhatsappMessageModel->where('t1.status', $this->_get->toString('status'));
		}
		$column = 'subject';
		$direction = 'ASC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjWhatsappMessageModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjWhatsappMessageModel
					->select('t1.*, t2.content AS subject')
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		$wm_available_for = __('wm_available_for', true);
		foreach ($data as $k => $v) {
		    $v['subject'] = pjSanitize::clean($v['subject']);
		    $v['available_for_formated'] = @$wm_available_for[$v['available_for']];
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
		
		if ($this->_post->check('add_wm'))
		{
			$post = $this->_post->raw();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if (isset($post['tuv']) && !empty($post['tuv']))
			{
			    $data['tuv'] = pjDateTime::formatDate($post['tuv'], $this->option_arr['o_date_format']);
			}
			$vehicle_id = pjWhatsappMessageModel::factory(array_merge($post, $data))->insert()->getInsertId();
			if ($vehicle_id !== false && (int) $vehicle_id > 0)
			{
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $vehicle_id, 'pjWhatsappMessage');
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
		if (pjWhatsappMessageModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
			pjMultiLangModel::factory()->where('model', 'pjWhatsappMessage')->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
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
		if (pjWhatsappMessageModel::factory()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
			pjMultiLangModel::factory()->where('model', 'pjWhatsappMessage')->whereIn('foreign_id', $record)->eraseAll();
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

		if (self::isPost() && $this->_post->check('update_wm'))
		{
			$pjWhatsappMessageModel = pjWhatsappMessageModel::factory();
			$arr = $pjWhatsappMessageModel->find($this->_post->toInt('id'))->getData();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if ($this->_post->check('tuv') && $this->_post->toString('tuv') != '')
			{
			    $data['tuv'] = pjDateTime::formatDate($this->_post->toString('tuv'), $this->option_arr['o_date_format']);
			}
			if ($arr) {
				$vehicle_id = $this->_post->toInt('id');
				$pjWhatsappMessageModel->reset()->set('id', $vehicle_id)->modify(array_merge($this->_post->raw(), $data));
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $vehicle_id, 'pjWhatsappMessage');
				}
			} else {
				$vehicle_id = $pjWhatsappMessageModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
				$i18n_arr = $this->_post->toI18n('i18n');
				if (!empty($i18n_arr))
				{
					pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $vehicle_id, 'pjWhatsappMessage');
				}
			}			
			self::jsonResponse(array('status' => 'OK'));
		}
		
		if (self::isGet())
		{
			$arr = pjWhatsappMessageModel::factory()->find($this->_get->toInt('id'))->getData();
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjWhatsappMessage');
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
		
		$pjWhatsappMessageModel = pjWhatsappMessageModel::factory();
		if (!in_array($params['column'], $pjWhatsappMessageModel->getI18n()))
		{
			$pjWhatsappMessageModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjWhatsappMessage');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
}
?>