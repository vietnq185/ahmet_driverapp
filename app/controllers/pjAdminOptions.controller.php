<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionIndex()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$pjOptionModel = pjOptionModel::factory();
		
		$arr = $pjOptionModel
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.tab_id', 1)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
		$this->set('arr', $arr);
		
		$tmp = $pjOptionModel->reset()->where('foreign_id', $this->getForeignId())->findAll()->getData();
		$o_arr = array();
		foreach ($tmp as $item)
		{
			$o_arr[$item['key']] = $item;
		}
		$this->set('o_arr', $o_arr);
		
		$this->appendCss('jasny-bootstrap.min.css', PJ_THIRD_PARTY_PATH . 'jasny/');
		$this->appendJs('jasny-bootstrap.min.js', PJ_THIRD_PARTY_PATH . 'jasny/');
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionInstall()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
		$locale_arr = pjBaseLocaleModel::factory()
			->select('t1.*, t2.file, t2.title')
			->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();
		$this->set('locale_arr', $locale_arr);
				
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionNotifications()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.tab_id', 3)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
	
		$this->set('arr', $arr);
	
		$this->setLocalesData();
	
		$this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionPreview()
	{
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		if (self::isPost() && $this->_post->toInt('options_update'))
		{
			$pjOptionModel = new pjOptionModel();
			$pjOptionModel
				->where('foreign_id', $this->getForeignId())
				->where('type', 'bool')
				->where('tab_id', $this->_post->toInt('tab'))
				->modifyAll(array('value' => '1|0::0'));
			
			foreach ($this->_post->raw() as $key => $value)
			{
				if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
				{
					list(, $type, $k) = explode("-", $key);
					if (!empty($k))
					{
						$_value = ':NULL';
						if ($value)
						{
							switch ($type)
							{
								case 'string':
								case 'text':
								case 'enum':
								case 'color':
									$_value = $this->_post->toString($key);
									break;
								case 'int':
								case 'bool':
									$_value = $this->_post->toInt($key);
									break;
								case 'float':
									$_value = $this->_post->toFloat($key);
									break;
							}
						}
			
						$pjOptionModel
						->reset()
						->where('foreign_id', $this->getForeignId())
						->where('`key`', $k)
						->limit(1)
						->modifyAll(array('value' => $_value));
					}
				}
			}
			
			$i18n_arr = $this->_post->toI18n('i18n');
			if (!empty($i18n_arr))
			{
				pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->getForeignId(), 'pjOption', 'data');
			}
			
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
							if (is_file($this->option_arr['o_name_sign_logo']))
							{
								@unlink($this->option_arr['o_name_sign_logo']);
							}
							$image_path = PJ_UPLOAD_PATH . 'logos/' . $_FILES['name_sign_logo']['name'];
							$pjImage
									->loadImage()
									->saveImage($image_path);
							$pjOptionModel
								->reset()
								->where('foreign_id', $this->getForeignId())
								->where('`key`', 'o_name_sign_logo')
								->limit(1)
								->modifyAll(array('value' => $image_path));
						}
					}else{
						
					}
				}else if($_FILES['name_sign_logo']['error'] != 4){
					
				}
			}
			
			if ($this->_post->check('tab'))
			{
				switch ($this->_post->toInt('tab'))
				{
					case '1':
						$err = 'AO06';
						break;
					case '2':
						$err = 'AO03';
						break;
					case '3':
						$err = 'AO09';
						break;
				}
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . $this->_post->toString('next_action') . "&err=$err");
		}
	}

	public function pjActionUpdateTheme()
	{
	$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if(!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		
		if (!$this->_post->has('theme'))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		pjOptionModel::factory()
			->where('foreign_id', $this->getForeignId())
			->where('`key`', 'o_theme')
			->limit(1)
			->modifyAll(array('value' => 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10::' . $this->_post->toString('theme')));
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Theme has been changed.'));
	}

	public function pjActionNotificationsGetMetaData()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isGet())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}
		
		if (!(isset($this->query['recipient']) && pjValidation::pjActionNotEmpty($this->query['recipient'])))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$this->set('arr', pjNotificationModel::factory()
			->where('t1.recipient', $this->query['recipient'])
			->orderBy('t1.id ASC')
			->findAll()
			->getData());
	}
	
	public function pjActionNotificationsGetContent()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isGet())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}
		
		if (!($this->_get->check('recipient') && $this->_get->check('variant') && $this->_get->check('transport'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('recipient'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('variant'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('transport'))
			&& in_array($this->_get->toString('transport'), array('email', 'sms'))
		)
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$arr = pjNotificationModel::factory()
			->where('t1.recipient', $this->_get->toString('recipient'))
			->where('t1.variant', $this->_get->toString('variant'))
			->where('t1.transport', $this->_get->toString('transport'))
			->limit(1)
			->findAll()
			->getDataIndex(0);
		
		if (!$arr)
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Message not found.'));
		}
		
		$arr['i18n'] = pjBaseMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
		$this->set('arr', $arr);
		
		# Check SMS
		$this->set('is_sms_ready', (isset($this->option_arr['plugin_sms_api_key']) && !empty($this->option_arr['plugin_sms_api_key']) ? 1 : 0));
		
		# Get locales
		$locale_arr = pjBaseLocaleModel::factory()
			->select('t1.*, t2.file, t2.title')
			->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();
		
		$lp_arr = array();
		foreach ($locale_arr as $item)
		{
			$lp_arr[$item['id']."_"] = array($item['file'], $item['title']);
		}
		$this->set('lp_arr', $locale_arr);
		$this->set('locale_str', self::jsonEncode($lp_arr));
		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
	}
	
	public function pjActionNotificationsSetContent()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}
		
		if (!(isset($this->body['id']) && pjValidation::pjActionNumeric($this->body['id'])))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$isToggle = $this->_post->check('is_active') && in_array($this->_post->toInt('is_active'), array(1,0));
		$isFormSubmit = $this->_post->check('i18n') && !$this->_post->isEmpty('i18n');
		
		if (!($isToggle xor $isFormSubmit))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Data mismatch.'));
		}
		
		if ($isToggle)
		{
			pjNotificationModel::factory()
				->set('id', $this->_post->toInt('id'))
				->modify(array('is_active' => $this->_post->toInt('is_active')));
		} elseif ($isFormSubmit) {
			pjBaseMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->getForeignId(), 'pjOption');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Notification has been updated.'));
	}
	
	public function pjActionDeleteLogo()
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
	
		if ($this->_post->toInt('delete_logo'))
		{
			@clearstatcache();
			if (is_file($this->option_arr['o_name_sign_logo']))
			{
				@unlink($this->option_arr['o_name_sign_logo']);
			}
			pjOptionModel::factory()
				->where('foreign_id', $this->getForeignId())
				->where('`key`', 'o_name_sign_logo')
				->limit(1)
				->modifyAll(array('value' => ':NULL'));
			self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Logo has been deleted.'));
		}
		self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
	}
		
	public function pjActionUpdate14819() {
	    $pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
	    $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
	    
	    $permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');
	    
	    $user_arr = array();
	    $u_arr = pjAuthUserModel::factory()->whereIn('t1.role_id', array(1,2,3))->findAll()->getData();
	    if ($u_arr) {
	        $user_ids = array();
	        foreach ($u_arr as $val) {
	            $user_arr[$val['role_id']][] = $val['id'];
	            $user_ids[] = $val['id'];
	        }
	        $pjAuthUserPermissionModel->whereIn('user_id', $user_ids)->eraseAll();
	    }
	    $roles = array(1 => 'admin', 2 => 'editor', 3 => 'driver');
	    foreach ($roles as $role_id => $role)
	    {
	        if (isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && !empty($GLOBALS['CONFIG']["role_permissions_{$role}"]))
	        {
	            $pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();
	            
	            foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission)
	            {
	                if($role_permission == '*')
	                {
	                    // Grant full permissions for the role
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                        if (isset($user_arr[$role_id])) {
	                            foreach ($user_arr[$role_id] as $user_id) {
	                                $pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
	                            }
	                        }
	                    }
	                    break;
	                }
	                else
	                {
	                    $hasAsterix = strpos($role_permission, '*') !== false;
	                    if($hasAsterix)
	                    {
	                        $role_permission = str_replace('*', '', $role_permission);
	                    }
	                    
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        if($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false))
	                        {
	                            $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                            if (isset($user_arr[$role_id])) {
	                                foreach ($user_arr[$role_id] as $user_id) {
	                                    $pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	    
	    echo 'Data updated!';
	    exit;
	}
	
	public function pjActionUpdate14819CC6() {
	    $pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
	    $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
	    
	    $permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');
	    
	    $user_arr = array();
	    $u_arr = pjAuthUserModel::factory()->whereIn('t1.role_id', array(3))->findAll()->getData();
	    if ($u_arr) {
	        $user_ids = array();
	        foreach ($u_arr as $val) {
	            $user_arr[$val['role_id']][] = $val['id'];
	            $user_ids[] = $val['id'];
	        }
	        $pjAuthUserPermissionModel->whereIn('user_id', $user_ids)->eraseAll();
	    }
	    $roles = array(3 => 'driver');
	    foreach ($roles as $role_id => $role)
	    {
	        if (isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && !empty($GLOBALS['CONFIG']["role_permissions_{$role}"]))
	        {
	            $pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();
	            
	            foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission)
	            {
	                if($role_permission == '*')
	                {
	                    // Grant full permissions for the role
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                        if (isset($user_arr[$role_id])) {
	                            foreach ($user_arr[$role_id] as $user_id) {
	                                $pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
	                            }
	                        }
	                    }
	                    break;
	                }
	                else
	                {
	                    $hasAsterix = strpos($role_permission, '*') !== false;
	                    if($hasAsterix)
	                    {
	                        $role_permission = str_replace('*', '', $role_permission);
	                    }
	                    
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        if($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false))
	                        {
	                            $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                            if (isset($user_arr[$role_id])) {
	                                foreach ($user_arr[$role_id] as $user_id) {
	                                    $pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	    
	    echo 'Data updated!';
	    exit;
	}
	
	public function pjActionUpdate08072025() {
	    $pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
	    $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
	    
	    $permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');
	    
	    $user_arr = array();
	    $u_arr = pjAuthUserModel::factory()->whereIn('t1.role_id', array(1,2))->findAll()->getData();
	    if ($u_arr) {
	        $user_ids = array();
	        foreach ($u_arr as $val) {
	            $user_arr[$val['role_id']][] = $val['id'];
	            $user_ids[] = $val['id'];
	        }
	        $pjAuthUserPermissionModel->whereIn('user_id', $user_ids)->eraseAll();
	    }
	    $roles = array(1 => 'admin', 2 => 'editor');
	    foreach ($roles as $role_id => $role)
	    {
	        if (isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && !empty($GLOBALS['CONFIG']["role_permissions_{$role}"]))
	        {
	            $pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();
	            
	            foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission)
	            {
	                if($role_permission == '*')
	                {
	                    // Grant full permissions for the role
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                        if (isset($user_arr[$role_id])) {
	                            foreach ($user_arr[$role_id] as $user_id) {
	                                $pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
	                            }
	                        }
	                    }
	                    break;
	                }
	                else
	                {
	                    $hasAsterix = strpos($role_permission, '*') !== false;
	                    if($hasAsterix)
	                    {
	                        $role_permission = str_replace('*', '', $role_permission);
	                    }
	                    
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        if($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false))
	                        {
	                            $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                            if (isset($user_arr[$role_id])) {
	                                foreach ($user_arr[$role_id] as $user_id) {
	                                    $pjAuthUserPermissionModel->reset()->setAttributes(array('user_id' => $user_id, 'permission_id' => $permission_id))->insert();
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	    
	    echo 'Data updated!';
	    exit;
	}
}
?>