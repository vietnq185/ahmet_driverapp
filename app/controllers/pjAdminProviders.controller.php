<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminProviders extends pjAdmin
{	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		$this->appendCss('jasny-bootstrap.min.css', PJ_THIRD_PARTY_PATH . 'jasny/');
		$this->appendJs('jasny-bootstrap.min.js', PJ_THIRD_PARTY_PATH . 'jasny/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjAdminProviders.js');
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		$pjProviderModel = pjProviderModel::factory();
			
		if ($q = $this->_get->toString('q'))
		{
			$q = str_replace(array('_', '%'), array('\_', '\%'), $pjProviderModel->escapeStr($q));
			$pjProviderModel->where('t1.name LIKE "%'.$q.'%"');
		}
		if ($this->_get->toString('status') && in_array($this->_get->toString('status'), array('T', 'F')))
		{
			$pjProviderModel->where('t1.status', $this->_get->toString('status'));
		}
		$column = 'name';
		$direction = 'ASC';
		if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
		{
			$column = $this->_get->toString('column');
			$direction = strtoupper($this->_get->toString('direction'));
		}

		$total = $pjProviderModel->findCount()->getData();
		$rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 10;
		$pages = ceil($total / $rowCount);
		$page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
		$offset = ((int) $page - 1) * $rowCount;
		if ($page > $pages)
		{
			$page = $pages;
		}
		$data = $pjProviderModel
					->select('t1.*')
					->orderBy("$column $direction")
					->limit($rowCount, $offset)
					->findAll()
					->getData();
		foreach ($data as $k => $v) {
		    $v['name'] = pjSanitize::clean($v['name']);
			$data[$k] = $v;
		}	
		self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	}
	
	public function pjActionAddProvider()
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
		
		if ($this->_post->check('add_provider'))
		{
			$post = $this->_post->raw();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			$provider_id = pjProviderModel::factory(array_merge($post, $data))->insert()->getInsertId();
			if ($provider_id !== false && (int) $provider_id > 0)
			{
			    if (isset($_FILES['name_sign_logo']))
			    {
			        if($_FILES['name_sign_logo']['error'] == 0)
			        {
			            if(getimagesize($_FILES['name_sign_logo']["tmp_name"]) != false)
			            {
			                $Image = new pjImage();
			                if ($Image->getErrorCode() !== 200)
			                {
			                    $Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
			                    if ($Image->load($_FILES['name_sign_logo']))
			                    {
			                        $resp = $Image->isConvertPossible();
			                        if ($resp['status'] === true)
			                        {
			                            $hash = md5(uniqid(rand(), true));
			                            $image_path = PJ_UPLOAD_PATH . 'logos/' . $provider_id . '_' . $hash . '.' . $Image->getExtension();
			                            
			                            $data = array();
			                            
			                            $data['name_sign_logo'] = $image_path;
			                            
			                            $Image->loadImage($_FILES['name_sign_logo']["tmp_name"]);
			                            $Image->saveImage($image_path);
			                            
			                            pjProviderModel::factory()->reset()->where('id', $provider_id)->limit(1)->modifyAll($data);
			                        }
			                    }
			                }
			            }else{
			                //$err = 'ALY09';
			            }
			        }else if($_FILES['name_sign_logo']['error'] != 4){
			            //$err = 'ALY09';
			        }
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
		$arr = pjProviderModel::factory()->find($this->_get->toInt('id'))->getData();
		if (pjProviderModel::factory()->reset()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows() == 1)
		{
		    @clearstatcache();
		    if (!empty($arr['name_sign_logo']) && is_file($arr['name_sign_logo']))
		    {
		        @unlink($arr['name_sign_logo']);
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
		$arr = pjProviderModel::factory()->whereIn('id', $record)->findAll()->getData();
		if (pjProviderModel::factory()->reset()->whereIn('id', $record)->eraseAll()->getAffectedRows() > 0)
		{
		    @clearstatcache();
		    foreach ($arr as $val)
		    {
		        if (!empty($val['name_sign_logo']) && is_file($val['name_sign_logo']))
		        {
		            @unlink($val['name_sign_logo']);
		        }
		    }
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
			
		}
	}
	
	public function pjActionUpdate()
	{
		$this->setAjax(true);
	
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}

		if (self::isPost() && $this->_post->check('update_provider'))
		{
			$pjProviderModel = pjProviderModel::factory();
			$arr = $pjProviderModel->find($this->_post->toInt('id'))->getData();
			$data = array();
			$data['status'] = $this->_post->check('status') ? 'T' : 'F';
			if ($arr) {
				$provider_id = $this->_post->toInt('id');
				$pjProviderModel->reset()->set('id', $provider_id)->modify(array_merge($this->_post->raw(), $data));
			} else {
			    $provider_id = $pjProviderModel->reset()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
			}			
			
			if ($provider_id !== false && (int)$provider_id > 0) {
			    if (isset($_FILES['name_sign_logo']))
			    {
			        if($_FILES['name_sign_logo']['error'] == 0)
			        {
			            $size = getimagesize($_FILES['name_sign_logo']['tmp_name']);
			            if($size == true)
			            {
			                $pjImage = new pjImage();
			                $pjImage->setAllowedExt($this->extensions)->setAllowedTypes($this->mimeTypes);
			                if ($pjImage->load($_FILES['name_sign_logo']))
			                {
			                    @clearstatcache();
			                    if($arr && !empty($arr['name_sign_logo']))
			                    {
			                        @unlink(PJ_INSTALL_PATH . $arr['name_sign_logo']);
			                    }
			                    
			                    $hash = md5(uniqid(rand(), true));
			                    $image_path = PJ_UPLOAD_PATH . 'logos/' . $provider_id . '_' . $hash . '.' . $pjImage->getExtension();
			                    $pjImage
			                    ->loadImage()
			                    ->saveImage($image_path);
			                    
			                    $data = array();
			                    $data['name_sign_logo'] = $image_path;
			                    $pjProviderModel->reset()->set('id', $provider_id)->modify($data);
			                }
			            }else{
			                
			            }
			        }else if($_FILES['name_sign_logo']['error'] != 4){
			            
			        }
			    }
			}
			
			self::jsonResponse(array('status' => 'OK'));
		}
		
		if (self::isGet())
		{
			$arr = pjProviderModel::factory()->find($this->_get->toInt('id'))->getData();
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
		
		$pjProviderModel = pjProviderModel::factory();
		if (!in_array($params['column'], $pjProviderModel->getI18n()))
		{
			$pjProviderModel->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
		} else {
			pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($params['column'] => $params['value'])), $params['id'], 'pjProvider');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200));
	}
	
	public function pjActionDeleteImage()
	{
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
	        $pjProviderModel = pjProviderModel::factory();
	        $arr = $pjProviderModel->find($id)->getData();
	        if (!empty($arr))
	        {
	            $pjProviderModel->modify(array('name_sign_logo' => ':NULL'));
	            
	            @clearstatcache();
	            if (!empty($arr['name_sign_logo']) && is_file($arr['name_sign_logo']))
	            {
	                @unlink($arr['name_sign_logo']);
	            }
	            
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	        }
	    }
	    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
}
?>