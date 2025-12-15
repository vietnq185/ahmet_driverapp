<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjApiSync extends pjFront
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	
	static private function syncGeneralData($api_endpoint) {
		set_time_limit(0);
		$pjClientModel = pjClientModel::factory();
		$pjDriverModel = pjDriverModel::factory();
		$pjExtraModel = pjExtraModel::factory();
		$pjExtraLimitationModel = pjExtraLimitationModel::factory();
		$pjFleetModel = pjFleetModel::factory();
		$pjFleetDiscountModel = pjFleetDiscountModel::factory();
		$pjFleetDiscountPeriodModel = pjFleetDiscountPeriodModel::factory();
		$pjFleetFeeModel = pjFleetFeeModel::factory();
		$pjLocationModel = pjLocationModel::factory();
		$pjDropoffModel = pjDropoffModel::factory();
		$pjDropoffAreaModel = pjDropoffAreaModel::factory();
		$pjVoucherModel = pjVoucherModel::factory();
		$pjAreaModel = pjAreaModel::factory();
		$pjAreaCoordModel = pjAreaCoordModel::factory();
		$pjStationModel = pjStationModel::factory();
		$pjStationFeeModel = pjStationFeeModel::factory();
		
		$locale_arr = pjLocaleModel::factory()->findAll()->getDataPair('language_iso', 'id');
		$pjHttp = new pjHttp();
		$pjHttp->setMethod('GET');
		$result = $pjHttp->curlRequest($api_endpoint);
		$resp = $result->getResponse();
		$resp = json_decode($resp, true);
		
		/* Clients */
		$client_arr = array();
		if (isset($resp['client_arr']) && count($resp['client_arr']) > 0) {
			foreach ($resp['client_arr'] as $client) {
				$c_data = $client;
				unset($c_data['id']);
				$c_data['domain'] = $resp['domain'];
				$c_data['external_id'] = $client['id'];
				$arr = $pjClientModel->reset()->where('t1.external_id', $client['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjClientModel->reset()->set('id', $arr['id'])->modify($c_data);
					$client_arr[$client['id']] = $arr['id'];
				} else {
					$client_id = $pjClientModel->reset()->setAttributes($c_data)->insert()->getInsertId();
					if ($client_id !== false && (int)$client_id > 0) {
						$client_arr[$client['id']] = $client_id;
					}
				}
			}
		}
		
		/* Drivers */
		$driver_arr = array();
		if (isset($resp['driver_arr']) && count($resp['driver_arr']) > 0) {
			foreach ($resp['driver_arr'] as $driver) {
				$d_data = $driver;
				unset($d_data['id']);
				$d_data['domain'] = $resp['domain'];
				$d_data['external_id'] = $driver['id'];
				$arr = $pjDriverModel->reset()->where('t1.external_id', $driver['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjDriverModel->reset()->set('id', $arr['id'])->modify($d_data);
					$driver_arr[$driver['id']] = $arr['id'];
				} else {
					$driver_id = $pjDriverModel->reset()->setAttributes($d_data)->insert()->getInsertId();
					if ($driver_id !== false && (int)$driver_id > 0) {
						$driver_arr[$driver['id']] = $driver_id;
					}
				}
			}
		}
		
		/* Fleets */
		$fleet_arr = array();
		if (isset($resp['fleet_arr']) && count($resp['fleet_arr']) > 0) {
			foreach ($resp['fleet_arr'] as $fleet) {
				$fleet_data = $fleet;
				unset($fleet_data['id']);
				$fleet_data['domain'] = $resp['domain'];
				$fleet_data['external_id'] = $fleet['id'];
				$i18n_arr = array();
				if (isset($fleet['i18n']) && count($fleet['i18n']) > 0) {
					foreach ($locale_arr as $iso => $locale_id) {
						if (isset($fleet['i18n'][$iso])) {
							$i18n_arr[$locale_id] = $fleet['i18n'][$iso];
						} else {
							$i18n_arr[$locale_id] = reset($fleet['i18n']);
						}
					}
				}
				$arr = $pjFleetModel->reset()->where('t1.external_id', $fleet['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjFleetModel->reset()->set('id', $arr['id'])->modify($fleet_data);
					$fleet_arr[$fleet['id']] = $arr['id'];
					if ($i18n_arr) {
						pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjFleet');
					}
					$fleet_id = $arr['id'];
				} else {
					$fleet_id = $pjFleetModel->reset()->setAttributes($fleet_data)->insert()->getInsertId();
					if ($fleet_id !== false && (int)$fleet_id > 0) {
						$fleet_arr[$fleet['id']] = $fleet_id;
						if ($i18n_arr) {
							pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $fleet_id, 'pjFleet');
						}
					}
				}
				if (isset($fleet_id) && (int)$fleet_id > 0) {
					if (isset($fleet['discount_arr']) && count($fleet['discount_arr']) > 0) {
						foreach ($fleet['discount_arr'] as $discount) {
							unset($discount['id']);
							$discount['fleet_id'] = $fleet_id;
							$discount_id = $pjFleetDiscountModel->reset()->setAttributes($discount)->insert()->getInsertId();
							if ($discount_id !== false && (int)$discount_id > 0) {
								if (isset($discount['period']) && count($discount['period']) > 0) {
									foreach ($discount['period'] as $dp) {
										unset($dp['id']);
										$dp['fleet_discount_id'] = $discount_id;
										$pjFleetDiscountPeriodModel->reset()->setAttributes($dp)->insert();
									}
								}
							}
						}
					}
					$pjFleetFeeModel->reset()->where('fleet_id', $fleet_id)->eraseAll();
					if (isset($fleet['fee_arr']) && count($fleet['fee_arr']) > 0) {
						foreach ($fleet['fee_arr'] as $fee) {
							unset($fee['id']);
							$fee['fleet_id'] = $fleet_id;
							$pjFleetFeeModel->reset()->setAttributes($fee)->insert();
						}
					}
				}
			}
		}
		
		/* Extras */
		$extra_arr = array();
		if (isset($resp['extra_arr']) && count($resp['extra_arr']) > 0) {
			foreach ($resp['extra_arr'] as $ex) {
				$ex_data = $ex;
				unset($ex_data['id']);
				$ex_data['domain'] = $resp['domain'];
				$ex_data['external_id'] = $ex['id'];
				$i18n_arr = array();
				if (isset($ex['i18n']) && count($ex['i18n']) > 0) {
					foreach ($locale_arr as $iso => $locale_id) {
						if (isset($ex['i18n'][$iso])) {
							$i18n_arr[$locale_id] = $ex['i18n'][$iso];
						} else {
							$i18n_arr[$locale_id] = reset($ex['i18n']);
						}
					}
				}
				$arr = $pjExtraModel->reset()->where('t1.external_id', $ex['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjExtraModel->reset()->set('id', $arr['id'])->modify($ex_data);
					$extra_arr[$ex['id']] = $arr['id'];
					if ($i18n_arr) {
						pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjExtra');
					}
					$extra_id = $arr['id'];
				} else {
					$extra_id = $pjExtraModel->reset()->setAttributes($ex_data)->insert()->getInsertId();
					if ($extra_id !== false && (int)$extra_id > 0) {
						$extra_arr[$ex['id']] = $extra_id;
						if ($i18n_arr) {
							pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $extra_id, 'pjExtra');
						}
					}
				}
				if (isset($extra_id) && (int)$extra_id > 0 && isset($ex['limitations']) && count($ex['limitations']) > 0) {
					foreach ($ex['limitations'] as $ex_limit) {
						unset($ex_limit['id']);
						$ex_limit['fleet_id'] = @$fleet_arr[$ex_limit['fleet_id']];
						$ex_limit['extra_id'] = $extra_id;
						$pjExtraLimitationModel->reset()->setAttributes($ex_limit)->insert();
					}
				}
			}
		}
		
		/* Area */
		$area_arr = $place_arr = array();
		if (isset($resp['area_arr']) && count($resp['area_arr']) > 0) {
			foreach ($resp['area_arr'] as $area) {
				$area_data = $area;
				unset($area_data['id']);
				$area_data['domain'] = $resp['domain'];
				$area_data['external_id'] = $area['id'];
				$i18n_arr = array();
				if (isset($area['i18n']) && count($area['i18n']) > 0) {
					foreach ($locale_arr as $iso => $locale_id) {
						if (isset($area['i18n'][$iso])) {
							$i18n_arr[$locale_id] = $area['i18n'][$iso];
						} else {
							$i18n_arr[$locale_id] = reset($area['i18n']);
						}
					}
				}
				$arr = $pjAreaModel->reset()->where('t1.external_id', $area['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjAreaModel->reset()->set('id', $arr['id'])->modify($area_data);
					$area_arr[$area['id']] = $arr['id'];
					if ($i18n_arr) {
						pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjArea');
					}
					$area_id = $arr['id'];
				} else {
					$area_id = $pjAreaModel->reset()->setAttributes($area_data)->insert()->getInsertId();
					if ($area_id !== false && (int)$area_id > 0) {
						$area_arr[$area['id']] = $area_id;
						if ($i18n_arr) {
							pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $area_id, 'pjArea');
						}
					}
				}
				
				if (isset($area_id) && (int)$area_id > 0 && isset($area['coords']) && count($area['coords']) > 0) {
					foreach ($area['coords'] as $coords) {
						$coords_data = $coords;
						unset($coords_data['id']);
						$coords_data['domain'] = $resp['domain'];
						$coords_data['external_id'] = $coords['id'];
						$coords_data['area_id'] = $area_arr[$coords['area_id']];
						$i18n_arr = array();
						if (isset($coords['i18n']) && count($coords['i18n']) > 0) {
							foreach ($locale_arr as $iso => $locale_id) {
								if (isset($coords['i18n'][$iso])) {
									$i18n_arr[$locale_id] = $coords['i18n'][$iso];
								} else {
									$i18n_arr[$locale_id] = reset($coords['i18n']);
								}
							}
						}
						$arr = $pjAreaCoordModel->reset()->where('t1.external_id', $coords['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
						if ($arr) {
							$pjAreaCoordModel->reset()->set('id', $arr['id'])->modify($coords_data);
							$place_arr[$coords['id']] = $arr['id'];
							if ($i18n_arr) {
								pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjAreaCoord');
							}
						} else {
							$place_id = $pjAreaCoordModel->reset()->setAttributes($coords_data)->insert()->getInsertId();
							if ($place_id !== false && (int)$place_id > 0) {
								$place_arr[$coords['id']] = $place_id;
								if ($i18n_arr) {
									pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $place_id, 'pjAreaCoord');
								}
							}
						}
					}	
				}
			}
		}
		
		/* Station */
		$station_arr = array();
		if (isset($resp['station_arr']) && count($resp['station_arr']) > 0) {
			foreach ($resp['station_arr'] as $station) {
				$station_data = $station;
				unset($station_data['id']);
				$station_data['domain'] = $resp['domain'];
				$station_data['external_id'] = $station['id'];
				$i18n_arr = array();
				if (isset($station['i18n']) && count($station['i18n']) > 0) {
					foreach ($locale_arr as $iso => $locale_id) {
						if (isset($station['i18n'][$iso])) {
							$i18n_arr[$locale_id] = $station['i18n'][$iso];
						} else {
							$i18n_arr[$locale_id] = reset($station['i18n']);
						}
					}
				}
				$arr = $pjStationModel->reset()->where('t1.external_id', $station['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjStationModel->reset()->set('id', $arr['id'])->modify($station_data);
					$station_arr[$station['id']] = $arr['id'];
					if ($i18n_arr) {
						pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjStation');
					}
					$station_id = $arr['id'];
				} else {
					$station_id = $pjStationModel->reset()->setAttributes($station_data)->insert()->getInsertId();
					if ($station_id !== false && (int)$station_id > 0) {
						$station_arr[$station['id']] = $station_id;
						if ($i18n_arr) {
							pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $station_id, 'pjStation');
						}
					}
				}
				
				if (isset($station_id) && (int)$station_id > 0 && isset($station['fee_arr']) && count($station['fee_arr']) > 0) {
					$pjStationFeeModel->reset()->where('station_id', $station_id)->eraseAll();
					$pjStationFeeModel = pjStationFeeModel::factory()->reset()->setBatchFields(array('station_id', 'start', 'end', 'price'));
					foreach ($station['fee_arr'] as $fee) {
						$pjStationFeeModel->addBatchRow(array(
							$station_id,
							$fee['start'],
							$fee['end'],
							$fee['price']
						));
					}
					$pjStationFeeModel->insertBatch();
				}
			}
		}
		
		/* Locations and Dropoff */
		$location_arr = $dropoff_arr = array();
		if (isset($resp['location_arr']) && count($resp['location_arr']) > 0) {
			foreach ($resp['location_arr'] as $loc) {
				$loc_data = $loc;
				unset($loc_data['id']);
				$loc_data['domain'] = $resp['domain'];
				$loc_data['external_id'] = $loc['id'];
				$i18n_arr = array();
				if (isset($loc['i18n']) && count($loc['i18n']) > 0) {
					foreach ($locale_arr as $iso => $locale_id) {
						if (isset($loc['i18n'][$iso])) {
							$i18n_arr[$locale_id] = $loc['i18n'][$iso];
						} else {
							$i18n_arr[$locale_id] = reset($loc['i18n']);
						}
					}
				}
				$arr = $pjLocationModel->reset()->where('t1.external_id', $loc['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjLocationModel->reset()->set('id', $arr['id'])->modify($loc_data);
					$location_arr[$loc['id']] = $arr['id'];
					if ($i18n_arr) {
						pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjLocation');
					}
					$location_id = $arr['id'];
				} else {
					$location_id = $pjLocationModel->reset()->setAttributes($loc_data)->insert()->getInsertId();
					if ($location_id !== false && (int)$location_id > 0) {
						$location_arr[$loc['id']] = $location_id;
						if ($i18n_arr) {
							pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $location_id, 'pjLocation');
						}
					}
				}
				if (isset($location_id) && (int)$location_id > 0 && isset($loc['dropoff_arr']) && count($loc['dropoff_arr']) > 0) {
					foreach ($loc['dropoff_arr'] as $dropoff) {
						$dropoff_data = $dropoff;
						unset($dropoff_data['id']);
						$dropoff_data['domain'] = $resp['domain'];
						$dropoff_data['external_id'] = $dropoff['id'];
						$dropoff_data['location_id'] = $location_arr[$dropoff['location_id']];
						$i18n_arr = array();
						if (isset($dropoff['i18n']) && count($dropoff['i18n']) > 0) {
							foreach ($locale_arr as $iso => $locale_id) {
								if (isset($loc['i18n'][$iso])) {
									$i18n_arr[$locale_id] = $dropoff['i18n'][$iso];
								} else {
									$i18n_arr[$locale_id] = reset($dropoff['i18n']);
								}
							}
						}
						$arr = $pjDropoffModel->reset()->where('t1.external_id', $dropoff['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
						if ($arr) {
							$pjDropoffModel->reset()->set('id', $arr['id'])->modify($dropoff_data);
							$dropoff_arr[$dropoff['id']] = $arr['id'];
							if ($i18n_arr) {
								pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjDropoff');
							}
							$dropoff_id = $arr['id'];							
						} else {
							$dropoff_id = $pjDropoffModel->reset()->setAttributes($dropoff_data)->insert()->getInsertId();
							if ($dropoff_id !== false && (int)$dropoff_id > 0) {
								$dropoff_arr[$dropoff['id']] = $dropoff_id;
								if ($i18n_arr) {
									pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $dropoff_id, 'pjDropoff');
								}
							}
						}
						if (isset($dropoff_id) && (int)$dropoff_id > 0) {
							$pjDropoffAreaModel->reset()->where('dropoff_id', $dropoff_id)->eraseAll();
							if (isset($dropoff['areas']) && count($dropoff['areas']) > 0) {
								foreach ($dropoff['areas'] as $d_area) {
									$d_area['dropoff_id'] = $dropoff_id;
									$d_area['area_id'] = $area_arr[$d_area['area_id']];
									$pjDropoffAreaModel->reset()->setAttributes($d_area)->insert()->getInsertId();
								}
							}
						}
					}	
				}
			}	
		}
		
		/* Prices */
		if (isset($resp['price_arr']) && count($resp['price_arr']) > 0) {
			$pjPriceModel = pjPriceModel::factory()->setBatchFields(array('fleet_id', 'dropoff_id', 'price_1', 'price_2', 'price_3', 'price_4', 'price_5', 'price_6', 'price_7'));
			foreach ($resp['price_arr'] as $price) {
				$pjPriceModel->addBatchRow(array(
					$fleet_arr[$price['fleet_id']],
					$dropoff_arr[$price['dropoff_id']],
					$price['price_1'],
					$price['price_2'],
					$price['price_3'],
					$price['price_4'],
					$price['price_5'],
					$price['price_6'],
					$price['price_7']
				));
			}
			$pjPriceModel->insertBatch();
		}
		
		/* Vouchers */
		$voucher_arr = array();
		if (isset($resp['voucher_arr']) && count($resp['voucher_arr']) > 0) {
			foreach ($resp['voucher_arr'] as $voucher) {
				$vc_data = $voucher;
				unset($vc_data['id']);
				$vc_data['domain'] = $resp['domain'];
				$vc_data['external_id'] = $voucher['id'];
				$arr = $pjVoucherModel->reset()->where('t1.external_id', $voucher['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjVoucherModel->reset()->set('id', $arr['id'])->modify($vc_data);
					$voucher_arr[$voucher['id']] = $arr['id'];
				} else {
					$voucher_id = $pjVoucherModel->reset()->setAttributes($vc_data)->insert()->getInsertId();
					if ($voucher_id !== false && (int)$voucher_id > 0) {
						$voucher_arr[$voucher['id']] = $voucher_id;
					}
				}
			}
		}
		$domain = $resp['domain'];
		return compact('fleet_arr', 'client_arr', 'driver_arr', 'driver_arr', 'location_arr', 'dropoff_arr', 'extra_arr', 'area_arr', 'place_arr', 'station_arr', 'domain');
	}
	
	static public function pjActionPullGeneralData($option_arr)
	{
		set_time_limit(0);
		$provider_arr = pjProviderModel::factory()->where('t1.status', 'T')->findAll()->getData();
		$booking_arr = array();
		foreach ($provider_arr as $provider) {
			$general_data = pjApiSync::syncGeneralData($provider['url'].'/index.php?controller=pjApiSync&action=pjActionPushGeneralData&last_update_time='.strtotime($option_arr['o_last_update_data'])); 
		}
		pjOptionModel::factory()->where('`key`', 'o_last_update_data')->limit(1)->modifyAll(array('value' => date('Y-m-d H:i:s')));
		return ;
	}
	
	public function syncBooking() {
		$pjBookingModel = pjBookingModel::factory();
		$pjBookingExtraModel = pjBookingExtraModel::factory();
		$pjBookingPaymentModel = pjBookingPaymentModel::factory();
		
		$post = $this->_post->raw();
		$resp = array('status' => 'ERR', 'text' => 'Unknow error');
		switch ($post['sync_action']) {
			case 'create':
			case 'update':				
				$option_arr = pjOptionModel::factory()->getPairs(1);
				$general_data = pjApiSync::syncGeneralData($post['domain'].'/index.php?controller=pjApiSync&action=pjActionPushGeneralData&last_update_time='.strtotime($option_arr['o_last_update_data'])); 
				$client_arr = @$general_data['client_arr'];
				$driver_arr = @$general_data['driver_arr'];
				$fleet_arr = @$general_data['fleet_arr'];
				$location_arr = @$general_data['location_arr'];
				$dropoff_arr = @$general_data['dropoff_arr'];
				$place_arr = @$general_data['place_arr'];
				$station_arr = @$general_data['station_arr'];
				$extra_arr = pjExtraModel::factory()->where('t1.domain', $post['domain'])->findAll()->getDataPair('external_id');
				
				if (isset($client_arr[$post['client_id']])) {
					$client_id = $client_arr[$post['client_id']];
				} else {
					$client_arr = pjClientModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['client_id'])->limit(1)->findAll()->getDataIndex(0);
					$client_id = $client_arr ? $client_arr['id'] : ':NULL';
				}
				if (isset($driver_arr[$post['driver_id']])) {
					$driver_id = $driver_arr[$post['driver_id']];
				} else {
					$driver_arr = pjDriverModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['driver_id'])->limit(1)->findAll()->getDataIndex(0);
					$driver_id = $driver_arr ? $driver_arr['id'] : ':NULL';
				}
				if (isset($fleet_arr[$post['fleet_id']])) {
					$fleet_id = $fleet_arr[$post['fleet_id']];
				} else {
					$fleet_arr = pjFleetModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['fleet_id'])->limit(1)->findAll()->getDataIndex(0);
					$fleet_id = $fleet_arr ? $fleet_arr['id'] : ':NULL';
				}
				if ($post['pickup_type'] == 'server') {
					if (isset($location_arr[$post['location_id']])) {
						$location_id = $location_arr[$post['location_id']];
					} else {
						$location_arr = pjLocationModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['location_id'])->limit(1)->findAll()->getDataIndex(0);
						$location_id = $location_arr ? $location_arr['id'] : ':NULL';	
					}
				} else {
					$location_id = $post['location_id'];
				}
				if ($post['dropoff_type'] == 'server' || ($post['dropoff_type'] == 'google' && (int)$post['dropoff_id'] > 0 && (int)$post['dropoff_place_id'] > 0)) {
					if (isset($dropoff_arr[$post['dropoff_id']])) {
						$dropoff_id = $dropoff_arr[$post['dropoff_id']];
					} else {
						$dropoff_arr = pjDropoffModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['dropoff_id'])->limit(1)->findAll()->getDataIndex(0);
						$dropoff_id = $dropoff_arr ? $dropoff_arr['id'] : ':NULL';
					}				
					if (isset($place_arr[$post['dropoff_place_id']])) {
						$dropoff_place_id = $place_arr[$post['dropoff_place_id']];
					} else {
						$palce_arr = pjAreaCoordModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['dropoff_place_id'])->limit(1)->findAll()->getDataIndex(0);
						$dropoff_place_id = $palce_arr ? $palce_arr['id'] : ':NULL';
					}
				} else {
					$dropoff_id = $post['dropoff_id'];
					$dropoff_place_id = $post['dropoff_place_id'];
				}
				if (isset($station_arr[$post['station_id']])) {
					$station_id = $station_arr[$post['station_id']];
				} else {
					$station_arr = pjStationModel::factory()->where('t1.domain', $post['domain'])->where('t1.external_id', $post['station_id'])->limit(1)->findAll()->getDataIndex(0);
					$station_id = $station_arr ? $station_arr['id'] : ':NULL';
				}
			
				$post['external_id'] = $post['id'];
				unset($post['id']);
				$post['client_id'] = $client_id;
				$post['driver_id'] = $driver_id;
				$post['fleet_id'] = $fleet_id;
				$post['location_id'] = $location_id;
				$post['dropoff_id'] = $dropoff_id;
				$post['dropoff_place_id'] = $dropoff_place_id;
				$post['station_id'] = $station_id;
				$post['last_update'] = date('Y-m-d H:i:s');
				
				$provider_arr = pjProviderModel::factory()
				->where("CONCAT(t1.url,'/')", $post['domain'])
				->orWhere('t1.url', $post['domain'])
			    ->limit(1) ->findAll()->getDataIndex(0);
			    $provider_prefix = $provider_arr ? strtoupper(substr($provider_arr['name'], 0, 1)) : '';
			    if (!empty($post['uuid'])) {
				    $post['uuid'] = $provider_prefix.$post['uuid'];
			    }
				
				$arr = $pjBookingModel
					->where('t1.external_id', @$post['external_id'])
					->where('t1.domain', $post['domain'])
					->where('(t1.ref_id IS NULL OR t1.ref_id="")')
					->limit(1)->findAll()->getDataIndex(0);
				if ((int)$post['return_id'] > 0) {
					$return_arr = $pjBookingModel->reset()->where('t1.domain', $post['domain'])->where('external_id', (int)$post['return_id'])->limit(1)->findAll()->getDataIndex(0);
					if ($return_arr) {
						$post['return_id'] = $return_arr['id'];
					}
				}
				if ((int)$post['passengers'] > 8) {
					$post['price'] = $post['price']/2;
				}
				if ($arr) {
					$id = $arr['id'];
					if ($arr['booking_date'] != $post['booking_date']) {
						$post['prev_booking_date'] = $arr['booking_date'];
					}
					if ($arr['passengers'] != $post['passengers']) {
						$post['prev_passengers'] = $arr['passengers'];
					}
					if (date('Y-m-d', strtotime($arr['booking_date'])) != date('Y-m-d', strtotime($post['booking_date']))) {
						$post['vehicle_id'] = "0";
					}
					$pjBookingModel->reset()->set('id', $id)->modify($post);
					$resp = array('status' => 'OK', 'text' => 'Booking update.');	
				} else {
					$id = $pjBookingModel->setAttributes($post)->insert()->getInsertId();
					if ($id !== false && (int)$id > 0) {
						$resp = array('status' => 'OK', 'text' => 'Booking added.');
					} else {
						$resp = array('status' => 'ERR', 'text' => 'Failed to add booking.');
					}
				}
				if (isset($id) && (int)$id > 0) {
					$pjBookingExtraModel->where('booking_id', $id)->eraseAll();
					$pjBookingPaymentModel->where('booking_id', $id)->eraseAll();
					if (isset($post['booking_extra_arr']) && count($post['booking_extra_arr']) > 0) {
						foreach ($post['booking_extra_arr'] as $be) {
							$be_arr = $be;
							unset($be_arr['id']);
							$be_arr['booking_id'] = $id;
							$be_arr['extra_id'] = @$extra_arr[$be['extra_id']]['id'];
							$pjBookingExtraModel->reset()->setAttributes($be_arr)->insert();
						}
					}
					if (isset($post['booking_payment_arr']) && count($post['booking_payment_arr']) > 0) {
						foreach ($post['booking_payment_arr'] as $bp) {
							$bp_arr = $bp;
							unset($bp_arr['id']);
							$bp_arr['booking_id'] = $id;
							$pjBookingPaymentModel->reset()->setAttributes($bp_arr)->insert();
						}
					}
					
					if ((int)$post['passengers'] > 8) {
						$additional_booking_arr = $pjBookingModel->reset()
							->where('t1.ref_id', $id)
							->where('t1.domain', $post['domain'])
							->limit(1)->findAll()->getDataIndex(0);
						$additional_booking_id = 0;
						if (!$additional_booking_arr) {
						    $post['uuid'] = $provider_prefix.time();
							$post['ref_id'] = $id;
							$additional_booking_id = $pjBookingModel->reset()->setAttributes($post)->insert()->getInsertId();
						} else {
						    $post['uuid'] = $provider_prefix.time();
							$pjBookingModel->reset()->set('id', $additional_booking_arr['id'])->modify($post);
							$additional_booking_id = $additional_booking_arr['id'];
						}
						if ($additional_booking_id > 0) {
							$pjBookingExtraModel->reset()->where('booking_id', $additional_booking_id)->eraseAll();
							$pjBookingPaymentModel->reset()->where('booking_id', $additional_booking_id)->eraseAll();
							if (isset($post['booking_extra_arr']) && count($post['booking_extra_arr']) > 0) {
								foreach ($post['booking_extra_arr'] as $be) {
									$be_arr = $be;
									unset($be_arr['id']);
									$be_arr['booking_id'] = $additional_booking_id;
									$be_arr['extra_id'] = @$extra_arr[$be['extra_id']]['id'];
									$pjBookingExtraModel->reset()->setAttributes($be_arr)->insert();
								}
							}
							if (isset($post['booking_payment_arr']) && count($post['booking_payment_arr']) > 0) {
								foreach ($post['booking_payment_arr'] as $bp) {
									$bp_arr = $bp;
									unset($bp_arr['id']);
									$bp_arr['booking_id'] = $additional_booking_id;
									$pjBookingPaymentModel->reset()->setAttributes($bp_arr)->insert();
								}
							}
						}
					} else {
						$additional_ids_arr = $pjBookingModel->reset()->where('t1.domain', $post['domain'])->whereIn('t1.ref_id', $id)->findAll()->getDataPair(null, 'id');
						if ($additional_ids_arr) {
							$pjBookingModel->reset()->whereIn('id', $additional_ids_arr)->eraseAll();
							$pjBookingExtraModel->reset()->whereIn('booking_id', $additional_ids_arr)->eraseAll();
							$pjBookingPaymentModel->reset()->whereIn('booking_id', $additional_ids_arr)->eraseAll();
						}
					}
					
					if (isset($post['name_sign']) && !empty($post['name_sign'])) {
					    $source = $post['domain'].$post['name_sign'];
					    $destination = $post['name_sign'];
					    if (copy($source, $destination)) {
					        //echo "Image copied successfully to $destination";
					    } else {
					        //echo "Failed to copy the image.";
					    }
					}
				}
			break;
			case 'update_latlng':	
			    $arr = $pjBookingModel
			    ->where('t1.external_id', $post['id'])
			    ->where('t1.domain', $post['domain'])
			    //->where('(t1.ref_id IS NULL OR t1.ref_id="")')
			    ->findAll()->getData();
			    if ($arr) {
			        foreach ($arr as $val) {
			            unset($post['id']);
			            $pjBookingModel->reset()->set('id', $val['id'])->modify($post);
			        }
			    }
			    $resp = array('status' => 'OK', 'text' => 'Booking updated.');
			    break;
			case 'cancel':
				if (isset($post['booking_ids']) && count($post['booking_ids']) > 0) {
					$ids_arr = $pjBookingModel->where('t1.domain', $post['domain'])->whereIn('t1.external_id', $post['booking_ids'])->findAll()->getDataPair('uuid', 'id');
					if ($ids_arr) {
						$pjBookingModel->reset()->whereIn('id', array_values($ids_arr))->modifyAll(array('status' => 'cancelled'));
						$resp = array('status' => 'OK', 'text' => 'Booking cancelled.');
					}
				}
				break;
			case 'delete':
				if (isset($post['booking_ids']) && count($post['booking_ids']) > 0) {
					$ids_arr = $pjBookingModel->where('t1.domain', $post['domain'])->whereIn('t1.external_id', $post['booking_ids'])->findAll()->getDataPair('uuid', 'id');
					if ($ids_arr) {
						$pjBookingModel->reset()->whereIn('id', array_values($ids_arr))->eraseAll();
						$pjBookingExtraModel->reset()->whereIn('booking_id', array_values($ids_arr))->eraseAll();
						$pjBookingPaymentModel->reset()->whereIn('booking_id', array_values($ids_arr))->eraseAll();
						
						$additional_ids_arr = $pjBookingModel->reset()->where('t1.domain', $post['domain'])->whereIn('t1.ref_id', array_values($ids_arr))->findAll()->getDataPair(null, 'id');
						if ($additional_ids_arr) {
							$pjBookingModel->reset()->whereIn('id', $additional_ids_arr)->eraseAll();
							$pjBookingExtraModel->reset()->whereIn('booking_id', $additional_ids_arr)->eraseAll();
							$pjBookingPaymentModel->reset()->whereIn('booking_id', $additional_ids_arr)->eraseAll();
						}
						$resp = array('status' => 'OK', 'text' => 'Booking deleted.');
					}
				}
				break;
			case 'update_flag_synchronized':
			    $cnt = $pjBookingModel
			    ->where('t1.external_id', $post['id'])
			    ->where('t1.domain', $post['domain'])
			    ->findCount()->getData();
			    if ($cnt > 0) {
			        $resp = array('status' => 'OK', 'text' => 'Synchonized.');
			    } else {
			        $resp = array('status' => 'ERR', 'text' => 'Not Synchonized');
			    }
			    break;
		}
		pjOptionModel::factory()->where('`key`', 'o_last_update_data')->limit(1)->modifyAll(array('value' => date('Y-m-d H:i:s')));
		return pjAppController::jsonResponse($resp);
	}
	
	static public function pjActionPullAllData($params)
	{
		set_time_limit(0);
		$resp = array('status' => 'OK', 'code' => 200, 'error_msg' => 'Data updated');
		$type = $params['type'];
		$page = isset($params['page']) && (int)$params['page'] > 0 ? (int)$params['page'] : 1;
		$row_count = isset($params['row_count']) && (int)$params['row_count'] > 0 ? (int)$params['row_count'] : 1000;
		$pjProviderModel = pjProviderModel::factory();
		if (isset($params['provider_id']) && (int)$params['provider_id'] > 0) {
		    $pjProviderModel->where('t1.id', $params['provider_id']);
		}
		$provider_arr = $pjProviderModel->where('t1.status', 'T')->findAll()->getData();
		foreach ($provider_arr as $provider) {
			$pjHttp = new pjHttp();
			$pjHttp->setMethod('GET');
			if (isset($params['is_count_page']) && $params['is_count_page'] == 1) {
			    $result = $pjHttp->curlRequest($provider['url'].'/index.php?controller=pjApiSync&action=pjActionInitData&type='.$type.'&is_count_page=1&row_count='.$row_count);
			    $resp = $result->getResponse();
			    $resp = json_decode($resp, true);
			    return $resp;
			} else {
			    $result = $pjHttp->curlRequest($provider['url'].'/index.php?controller=pjApiSync&action=pjActionInitData&type='.$type.'&page='.$page.'&row_count='.$row_count);
			    $resp = $result->getResponse();
			    $resp = json_decode($resp, true);
			}
			if ($resp['status'] == 'OK') {
				switch ($type) {
					case 'client':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjClientModel = pjClientModel::factory();
							foreach ($resp['data'] as $client) {
								$c_data = $client;
								unset($c_data['id']);
								$c_data['domain'] = $resp['domain'];
								$c_data['external_id'] = $client['id'];
								$arr = $pjClientModel->reset()->where('t1.external_id', $client['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjClientModel->reset()->set('id', $arr['id'])->modify($c_data);
								} else {
									$pjClientModel->reset()->setAttributes($c_data)->insert();
								}
							}
							$cnt = $pjClientModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
							$resp = array('status' => 'ERROR', 'code' => 101, 'error_msg' => 'No clients found');
						}
					break;
					case 'driver':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjDriverModel = pjDriverModel::factory();
							foreach ($resp['data'] as $driver) {
								$d_data = $driver;
								unset($d_data['id']);
								$d_data['domain'] = $resp['domain'];
								$d_data['external_id'] = $driver['id'];
								$arr = $pjDriverModel->reset()->where('t1.external_id', $driver['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjDriverModel->reset()->set('id', $arr['id'])->modify($d_data);
								} else {
									$pjDriverModel->reset()->setAttributes($d_data)->insert();
								}
							}
							$cnt = $pjDriverModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 102, 'error_msg' => 'No drivers found');
						}
					break;
					case 'voucher':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjVoucherModel = pjVoucherModel::factory();
							foreach ($resp['data'] as $voucher) {
								$vc_data = $voucher;
								unset($vc_data['id']);
								$vc_data['domain'] = $resp['domain'];
								$vc_data['external_id'] = $voucher['id'];
								$arr = $pjVoucherModel->reset()->where('t1.external_id', $voucher['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjVoucherModel->reset()->set('id', $arr['id'])->modify($vc_data);
								} else {
									$pjVoucherModel->reset()->setAttributes($vc_data)->insert();
								}
							}
							$cnt = $pjVoucherModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 103, 'error_msg' => 'No vouchers found');
						}
					break;
					case 'extra':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjExtraModel = pjExtraModel::factory();
							$pjExtraLimitationModel = pjExtraLimitationModel::factory();
							$locale_arr = pjLocaleModel::factory()->findAll()->getDataPair('language_iso', 'id');
							$fleet_arr = pjFleetModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id', 'id');
							foreach ($resp['data'] as $ex) {
								$ex_data = $ex;
								unset($ex_data['id']);
								$ex_data['domain'] = $resp['domain'];
								$ex_data['external_id'] = $ex['id'];
								$i18n_arr = array();
								if (isset($ex['i18n']) && count($ex['i18n']) > 0) {
									foreach ($locale_arr as $iso => $locale_id) {
										if (isset($ex['i18n'][$iso])) {
											$i18n_arr[$locale_id] = $ex['i18n'][$iso];
										} else {
											$i18n_arr[$locale_id] = reset($ex['i18n']);
										}
									}
								}
								$arr = $pjExtraModel->reset()->where('t1.external_id', $ex['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjExtraModel->reset()->set('id', $arr['id'])->modify($ex_data);
									$extra_arr[$ex['id']] = $arr['id'];
									if ($i18n_arr) {
										pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjExtra');
									}
									$extra_id = $arr['id'];
								} else {
									$extra_id = $pjExtraModel->reset()->setAttributes($ex_data)->insert()->getInsertId();
									if ($extra_id !== false && (int)$extra_id > 0) {
										$extra_arr[$ex['id']] = $extra_id;
										if ($i18n_arr) {
											pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $extra_id, 'pjExtra');
										}
									}
								}
								if (isset($extra_id) && (int)$extra_id > 0 && isset($ex['limitations']) && count($ex['limitations']) > 0) {
									foreach ($ex['limitations'] as $ex_limit) {
										unset($ex_limit['id']);
										$ex_limit['fleet_id'] = @$fleet_arr[$ex_limit['fleet_id']];
										$ex_limit['extra_id'] = $extra_id;
										$pjExtraLimitationModel->reset()->setAttributes($ex_limit)->insert();
									}
								}
							}
							$cnt = $pjExtraModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 104, 'error_msg' => 'No extras found');
						}
					break;		
					case 'fleet':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjFleetModel = pjFleetModel::factory();
							$pjFleetDiscountModel = pjFleetDiscountModel::factory();
							$pjFleetDiscountPeriodModel = pjFleetDiscountPeriodModel::factory();
							$locale_arr = pjLocaleModel::factory()->findAll()->getDataPair('language_iso', 'id');
							foreach ($resp['data'] as $fleet) {
								$fleet_data = $fleet;
								unset($fleet_data['id']);
								$fleet_data['domain'] = $resp['domain'];
								$fleet_data['external_id'] = $fleet['id'];
								$i18n_arr = array();
								if (isset($fleet['i18n']) && count($fleet['i18n']) > 0) {
									foreach ($locale_arr as $iso => $locale_id) {
										if (isset($fleet['i18n'][$iso])) {
											$i18n_arr[$locale_id] = $fleet['i18n'][$iso];
										} else {
											$i18n_arr[$locale_id] = reset($fleet['i18n']);
										}
									}
								}
								$arr = $pjFleetModel->reset()->where('t1.external_id', $fleet['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjFleetModel->reset()->set('id', $arr['id'])->modify($fleet_data);
									$fleet_arr[$fleet['id']] = $arr['id'];
									if ($i18n_arr) {
										pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjFleet');
									}
									$fleet_id = $arr['id'];
								} else {
									$fleet_id = $pjFleetModel->reset()->setAttributes($fleet_data)->insert()->getInsertId();
									if ($fleet_id !== false && (int)$fleet_id > 0) {
										$fleet_arr[$fleet['id']] = $fleet_id;
										if ($i18n_arr) {
											pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $fleet_id, 'pjFleet');
										}
									}
								}
								if (isset($fleet_id) && (int)$fleet_id > 0 && isset($fleet['discount_arr']) && count($fleet['discount_arr']) > 0) {
									foreach ($fleet['discount_arr'] as $discount) {
										unset($discount['id']);
										$discount['fleet_id'] = $fleet_id;
										$discount_id = $pjFleetDiscountModel->reset()->setAttributes($discount)->insert()->getInsertId();
										if ($discount_id !== false && (int)$discount_id > 0) {
											if (isset($discount['period']) && count($discount['period']) > 0) {
												foreach ($discount['period'] as $dp) {
													unset($dp['id']);
													$dp['fleet_discount_id'] = $discount_id;
													$pjFleetDiscountPeriodModel->reset()->setAttributes($dp)->insert();
												}
											}
										}
									}
								}
							}
							$cnt = $pjFleetModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 105, 'error_msg' => 'No fleets found');
						}
					break;
					case 'location':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjLocationModel = pjLocationModel::factory();
							$pjDropoffModel = pjDropoffModel::factory();
							$pjDropoffAreaModel = pjDropoffAreaModel::factory();
							
							$locale_arr = pjLocaleModel::factory()->findAll()->getDataPair('language_iso', 'id');
							foreach ($resp['data'] as $loc) {
								$loc_data = $loc;
								unset($loc_data['id']);
								$loc_data['domain'] = $resp['domain'];
								$loc_data['external_id'] = $loc['id'];
								$i18n_arr = array();
								if (isset($loc['i18n']) && count($loc['i18n']) > 0) {
									foreach ($locale_arr as $iso => $locale_id) {
										if (isset($loc['i18n'][$iso])) {
											$i18n_arr[$locale_id] = $loc['i18n'][$iso];
										} else {
											$i18n_arr[$locale_id] = reset($loc['i18n']);
										}
									}
								}
								$arr = $pjLocationModel->reset()->where('t1.external_id', $loc['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjLocationModel->reset()->set('id', $arr['id'])->modify($loc_data);
									$location_arr[$loc['id']] = $arr['id'];
									if ($i18n_arr) {
										pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjLocation');
									}
									$location_id = $arr['id'];
								} else {
									$location_id = $pjLocationModel->reset()->setAttributes($loc_data)->insert()->getInsertId();
									if ($location_id !== false && (int)$location_id > 0) {
										$location_arr[$loc['id']] = $location_id;
										if ($i18n_arr) {
											pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $location_id, 'pjLocation');
										}
									}
								}
								if (isset($location_id) && (int)$location_id > 0 && isset($loc['dropoff_arr']) && count($loc['dropoff_arr']) > 0) {
								    $area_arr = pjAreaModel::factory()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id', 'id');
									foreach ($loc['dropoff_arr'] as $dropoff) {
										$dropoff_data = $dropoff;
										unset($dropoff_data['id']);
										$dropoff_data['domain'] = $resp['domain'];
										$dropoff_data['external_id'] = $dropoff['id'];
										$dropoff_data['location_id'] = $location_arr[$dropoff['location_id']];
										$i18n_arr = array();
										if (isset($dropoff['i18n']) && count($dropoff['i18n']) > 0) {
											foreach ($locale_arr as $iso => $locale_id) {
												if (isset($loc['i18n'][$iso])) {
													$i18n_arr[$locale_id] = $dropoff['i18n'][$iso];
												} else {
													$i18n_arr[$locale_id] = reset($dropoff['i18n']);
												}
											}
										}
										$arr = $pjDropoffModel->reset()->where('t1.external_id', $dropoff['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
										if ($arr) {
											$pjDropoffModel->reset()->set('id', $arr['id'])->modify($dropoff_data);
											$dropoff_arr[$dropoff['id']] = $arr['id'];
											if ($i18n_arr) {
												pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjDropoff');
											}
											$dropoff_id = $arr['id'];
										} else {
											$dropoff_id = $pjDropoffModel->reset()->setAttributes($dropoff_data)->insert()->getInsertId();
											if ($dropoff_id !== false && (int)$dropoff_id > 0) {
												$dropoff_arr[$dropoff['id']] = $dropoff_id;
												if ($i18n_arr) {
													pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $dropoff_id, 'pjDropoff');
												}
											}
										}										
										if (isset($dropoff_id) && (int)$dropoff_id > 0) {
										    $pjDropoffAreaModel->reset()->where('dropoff_id', $dropoff_id)->eraseAll();
										    if (isset($dropoff['areas']) && count($dropoff['areas']) > 0) {
										        foreach ($dropoff['areas'] as $d_area) {
										            $d_area['dropoff_id'] = $dropoff_id;
										            $d_area['area_id'] = $area_arr[$d_area['area_id']];
										            $pjDropoffAreaModel->reset()->setAttributes($d_area)->insert()->getInsertId();
										        }
										    }
										}
										
									}	
								}
							}
							$cnt = $pjLocationModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 106, 'error_msg' => 'No locations found');
						}
					break;		
					case 'area':
					    if (isset($resp['data']) && count($resp['data']) > 0) {
					        $pjAreaModel = pjAreaModel::factory();
					        $pjAreaCoordModel = pjAreaCoordModel::factory();
					        
					        $area_arr = $place_arr = array();
					        foreach ($resp['data'] as $area) {
					            $area_data = $area;
					            unset($area_data['id']);
					            $area_data['domain'] = $resp['domain'];
					            $area_data['external_id'] = $area['id'];
					            $i18n_arr = array();
					            if (isset($area['i18n']) && count($area['i18n']) > 0) {
					                foreach ($locale_arr as $iso => $locale_id) {
					                    if (isset($area['i18n'][$iso])) {
					                        $i18n_arr[$locale_id] = $area['i18n'][$iso];
					                    } else {
					                        $i18n_arr[$locale_id] = reset($area['i18n']);
					                    }
					                }
					            }
					            $arr = $pjAreaModel->reset()->where('t1.external_id', $area['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
					            if ($arr) {
					                $pjAreaModel->reset()->set('id', $arr['id'])->modify($area_data);
					                $area_arr[$area['id']] = $arr['id'];
					                if ($i18n_arr) {
					                    pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjArea');
					                }
					                $area_id = $arr['id'];
					            } else {
					                $area_id = $pjAreaModel->reset()->setAttributes($area_data)->insert()->getInsertId();
					                if ($area_id !== false && (int)$area_id > 0) {
					                    $area_arr[$area['id']] = $area_id;
					                    if ($i18n_arr) {
					                        pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $area_id, 'pjArea');
					                    }
					                }
					            }
					            
					            if (isset($area_id) && (int)$area_id > 0 && isset($area['coords']) && count($area['coords']) > 0) {
					                foreach ($area['coords'] as $coords) {
					                    $coords_data = $coords;
					                    unset($coords_data['id']);
					                    $coords_data['domain'] = $resp['domain'];
					                    $coords_data['external_id'] = $coords['id'];
					                    $coords_data['area_id'] = $area_arr[$coords['area_id']];
					                    $i18n_arr = array();
					                    if (isset($coords['i18n']) && count($coords['i18n']) > 0) {
					                        foreach ($locale_arr as $iso => $locale_id) {
					                            if (isset($coords['i18n'][$iso])) {
					                                $i18n_arr[$locale_id] = $coords['i18n'][$iso];
					                            } else {
					                                $i18n_arr[$locale_id] = reset($coords['i18n']);
					                            }
					                        }
					                    }
					                    $arr = $pjAreaCoordModel->reset()->where('t1.external_id', $coords['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
					                    if ($arr) {
					                        $pjAreaCoordModel->reset()->set('id', $arr['id'])->modify($coords_data);
					                        $place_arr[$coords['id']] = $arr['id'];
					                        if ($i18n_arr) {
					                            pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjAreaCoord');
					                        }
					                    } else {
					                        $place_id = $pjAreaCoordModel->reset()->setAttributes($coords_data)->insert()->getInsertId();
					                        if ($place_id !== false && (int)$place_id > 0) {
					                            $place_arr[$coords['id']] = $place_id;
					                            if ($i18n_arr) {
					                                pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $place_id, 'pjAreaCoord');
					                            }
					                        }
					                    }
					                }
					            }
					        }
					        
					        $cnt = $pjAreaModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
					        $resp['total_records_updated'] = $cnt;
					    } else {
					        $resp = array('status' => 'ERROR', 'code' => 106, 'error_msg' => 'No areas found');
					    }
					    break;
					case 'station':
					    if (isset($resp['data']) && count($resp['data']) > 0) {
					        $pjStationModel = pjStationModel::factory();
					        $pjStationFeeModel = pjStationFeeModel::factory();
					        
					        $station_arr = array();
					        foreach ($resp['data'] as $station) {
					            $station_data = $station;
					            unset($station_data['id']);
					            $station_data['domain'] = $resp['domain'];
					            $station_data['external_id'] = $station['id'];
					            $i18n_arr = array();
					            if (isset($station['i18n']) && count($station['i18n']) > 0) {
					                foreach ($locale_arr as $iso => $locale_id) {
					                    if (isset($station['i18n'][$iso])) {
					                        $i18n_arr[$locale_id] = $station['i18n'][$iso];
					                    } else {
					                        $i18n_arr[$locale_id] = reset($station['i18n']);
					                    }
					                }
					            }
					            $arr = $pjStationModel->reset()->where('t1.external_id', $station['id'])->where('t1.domain', $resp['domain'])->limit(1)->findAll()->getDataIndex(0);
					            if ($arr) {
					                $pjStationModel->reset()->set('id', $arr['id'])->modify($station_data);
					                $station_arr[$station['id']] = $arr['id'];
					                if ($i18n_arr) {
					                    pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $arr['id'], 'pjStation');
					                }
					                $station_id = $arr['id'];
					            } else {
					                $station_id = $pjStationModel->reset()->setAttributes($station_data)->insert()->getInsertId();
					                if ($station_id !== false && (int)$station_id > 0) {
					                    $station_arr[$station['id']] = $station_id;
					                    if ($i18n_arr) {
					                        pjMultiLangModel::factory()->saveMultiLang($i18n_arr, $station_id, 'pjStation');
					                    }
					                }
					            }
					            
					            if (isset($station_id) && (int)$station_id > 0 && isset($station['fee_arr']) && count($station['fee_arr']) > 0) {
					                $pjStationFeeModel->reset()->where('station_id', $station_id)->eraseAll();
					                $pjStationFeeModel = pjStationFeeModel::factory()->reset()->setBatchFields(array('station_id', 'start', 'end', 'price'));
					                foreach ($station['fee_arr'] as $fee) {
					                    $pjStationFeeModel->addBatchRow(array(
					                        $station_id,
					                        $fee['start'],
					                        $fee['end'],
					                        $fee['price']
					                    ));
					                }
					                $pjStationFeeModel->insertBatch();
					            }
					        }
					    } else {
					        $resp = array('status' => 'ERROR', 'code' => 107, 'error_msg' => 'No stations found');
					    }
					    break;
					case 'price':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$fleet_arr = pjFleetModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$dropoff_arr = pjDropoffModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$pjPriceModel = pjPriceModel::factory()->reset()->setBatchFields(array('fleet_id', 'dropoff_id', 'price_1', 'price_2', 'price_3', 'price_4', 'price_5', 'price_6', 'price_7'));
							foreach ($resp['data'] as $price) {
								$pjPriceModel->addBatchRow(array(
									$fleet_arr[$price['fleet_id']]['id'],
									$dropoff_arr[$price['dropoff_id']]['id'],
									$price['price_1'],
									$price['price_2'],
									$price['price_3'],
									$price['price_4'],
									$price['price_5'],
									$price['price_6'],
									$price['price_7']
								));
							}
							$pjPriceModel->insertBatch();
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 107, 'error_msg' => 'No prices found');
						}
					   break;
					case 'booking':
						if (isset($resp['data']) && count($resp['data']) > 0) {
							$pjBookingModel = pjBookingModel::factory();
							$pjBookingExtraModel = pjBookingExtraModel::factory();
							$pjBookingPaymentModel = pjBookingPaymentModel::factory();
							
							$client_arr = pjClientModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$driver_arr = pjDriverModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$fleet_arr = pjFleetModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$location_arr = pjLocationModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$dropoff_arr = pjDropoffModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$place_arr = pjAreaCoordModel::factory()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$station_arr = pjStationModel::factory()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							$extra_arr = pjExtraModel::factory()->reset()->where('t1.domain', $resp['domain'])->findAll()->getDataPair('external_id');
							
							$provider_prefix = strtoupper(substr($provider['name'], 0, 1));
							$booking_arr = array();
							foreach ($resp['data'] as $booking) {
								$booking_data = $booking;
								unset($booking_data['id']);
								$booking_data['client_id'] = @$client_arr[$booking['client_id']]['id'];
								$booking_data['driver_id'] = @$driver_arr[$booking['driver_id']]['id'];
								$booking_data['fleet_id'] = @$fleet_arr[$booking['fleet_id']]['id'];
								$booking_data['location_id'] = @$location_arr[$booking['location_id']]['id'];
								$booking_data['dropoff_id'] = @$dropoff_arr[$booking['dropoff_id']]['id'];								
								$booking_data['dropoff_place_id'] = @$place_arr[$booking['dropoff_place_id']]['id'];
								$booking_data['station_id'] = @$station_arr[$booking['station_id']]['id'];								
								$booking_data['return_id'] = @$booking_arr[$booking['return_id']];
								$booking_data['domain'] = $resp['domain'];
								$booking_data['external_id'] = $booking['id'];
								$booking_data['last_update'] = date('Y-m-d H:i:s');				
								
								if (!empty($booking['uuid'])) {
								    $booking_data['uuid'] = $provider_prefix.$booking['uuid'];
								}
								
								$arr = $pjBookingModel->reset()
									->where('t1.external_id', $booking_data['external_id'])
									->where('t1.domain', $booking_data['domain'])
									->where('(t1.ref_id IS NULL OR t1.ref_id="")')
									->limit(1)->findAll()->getDataIndex(0);
								if ((int)$booking['passengers'] > 8) {
									$booking_data['price'] = $booking_data['price']/2;
								}
								if ($arr) {
									$booking_id = $arr['id'];
									if ($arr['booking_date'] != $booking['booking_date']) {
										$booking_data['prev_booking_date'] = $arr['booking_date'];
									}
									if ($arr['passengers'] != $booking['passengers']) {
										$booking_data['prev_passengers'] = $arr['passengers'];
									}
									if (date('Y-m-d', strtotime($arr['booking_date'])) != date('Y-m-d', strtotime($booking['booking_date']))) {
										$post['vehicle_id'] = "0";
									}
									$pjBookingModel->reset()->set('id', $booking_id)->modify($booking_data);
								} else {
									$booking_id = $pjBookingModel->reset()->setAttributes($booking_data)->insert()->getInsertId();
								}
								$booking_arr[$booking['id']] = $booking_id;
								if (isset($booking_id) && (int)$booking_id > 0) {
									$pjBookingExtraModel->reset()->where('booking_id', $booking_id)->eraseAll();
									$pjBookingPaymentModel->reset()->where('booking_id', $booking_id)->eraseAll();					
									if (isset($booking['booking_extra_arr']) && count($booking['booking_extra_arr']) > 0) {
										foreach ($booking['booking_extra_arr'] as $be) {
											$be_arr = $be;
											unset($be_arr['id']);
											$be_arr['booking_id'] = $booking_id;
											$be_arr['extra_id'] = @$extra_arr[$be['extra_id']]['id'];
											$pjBookingExtraModel->reset()->setAttributes($be_arr)->insert();
										}
									}
									if (isset($booking['booking_payment_arr']) && count($booking['booking_payment_arr']) > 0) {
										foreach ($booking['booking_payment_arr'] as $bp) {
											$bp_arr = $bp;
											unset($bp_arr['id']);
											$bp_arr['booking_id'] = $booking_id;
											$pjBookingPaymentModel->reset()->setAttributes($bp_arr)->insert();
										}
									}
									
									if ((int)$booking['passengers'] > 8) {
										$additional_booking_arr = $pjBookingModel->reset()
											->where('t1.ref_id', $booking_id)
											->where('t1.domain', $booking_data['domain'])
											->limit(1)->findAll()->getDataIndex(0);
										$additional_booking_id = 0;
										$uuid = pjAppController::createRandomBookingId();
										if (!$additional_booking_arr) {
											$booking_data['ref_id'] = $booking_id;
											$booking_data['uuid'] = $provider_prefix.$uuid;
											$additional_booking_id = $pjBookingModel->reset()->setAttributes($booking_data)->insert()->getInsertId();
										} else {
										    $booking_data['uuid'] = $provider_prefix.$uuid;
											$pjBookingModel->reset()->set('id', $additional_booking_arr['id'])->modify($booking_data);
											$additional_booking_id = $additional_booking_arr['id'];
										}
										if ($additional_booking_id > 0) {
											$pjBookingExtraModel->reset()->where('booking_id', $additional_booking_id)->eraseAll();
											$pjBookingPaymentModel->reset()->where('booking_id', $additional_booking_id)->eraseAll();
											if (isset($booking['booking_extra_arr']) && count($booking['booking_extra_arr']) > 0) {
												foreach ($booking['booking_extra_arr'] as $be) {
													$be_arr = $be;
													unset($be_arr['id']);
													$be_arr['booking_id'] = $additional_booking_id;
													$be_arr['extra_id'] = @$extra_arr[$be['extra_id']]['id'];
													$pjBookingExtraModel->reset()->setAttributes($be_arr)->insert();
												}
											}
											if (isset($booking['booking_payment_arr']) && count($booking['booking_payment_arr']) > 0) {
												foreach ($booking['booking_payment_arr'] as $bp) {
													$bp_arr = $bp;
													unset($bp_arr['id']);
													$bp_arr['booking_id'] = $additional_booking_id;
													$pjBookingPaymentModel->reset()->setAttributes($bp_arr)->insert();
												}
											}
										}
									} else {
										$additional_ids_arr = $pjBookingModel->reset()->where('t1.domain', $booking_data['domain'])->whereIn('t1.ref_id', $booking_id)->findAll()->getDataPair(null, 'id');
										if ($additional_ids_arr) {
											$pjBookingModel->reset()->whereIn('id', $additional_ids_arr)->eraseAll();
											$pjBookingExtraModel->reset()->whereIn('booking_id', $additional_ids_arr)->eraseAll();
											$pjBookingPaymentModel->reset()->whereIn('booking_id', $additional_ids_arr)->eraseAll();
										}
									}
								}
							}
							$cnt = $pjBookingModel->reset()->where('t1.domain', $resp['domain'])->findCount()->getData();
							$resp['total_records_updated'] = $cnt;
						} else {
						    $resp = array('status' => 'ERROR', 'code' => 108, 'error_msg' => 'No bookings found');
						}
					break;
					default:
					    $resp = array('status' => 'ERROR', 'code' => 109, 'error_msg' => 'No Data found');
					break;
				} 
			} else {
			    $resp = array('status' => 'ERROR', 'code' => 110, 'error_msg' => 'No Data found');
			}
		}
		if (isset($params['provider_id']) && (int)$params['provider_id'] <= 0) {
            pjOptionModel::factory()->where('`key`', 'o_last_update_data')->limit(1)->modifyAll(array('value' => date('Y-m-d H:i:s')));
		}
		return $resp;
	}
	
	static public function pjActionPullAllGeneralData()
	{
	    set_time_limit(0);
	    $provider_arr = pjProviderModel::factory()->where('t1.status', 'T')->findAll()->getData();
	    $booking_arr = array();
	    foreach ($provider_arr as $provider) {
	        $general_data = pjApiSync::syncGeneralData($provider['url'].'/index.php?controller=pjApiSync&action=pjActionPushGeneralData');
	    }
	    return ;
	}
}
?>