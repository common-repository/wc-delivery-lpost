<?php


class LPost_WC_Helper extends LPost_WC
{
	private $url = '';
	public $token = false;
	public $errors = array();

	public function __construct()
	{
		$testMode = get_option('lpost_wc_api_test', 'no');
		$this->url = ($testMode === 'yes') ? 'https://apitest.l-post.ru/' : 'https://api.l-post.ru/';
		//$this->url = 'https://api.l-post.ru/';
		
		$this->token = $this->getAuthToken();
	}
	
	
	//По значениям интервала от и до возвращает id интервала
	public function getIntervalId( $timeFrom, $timeTo )
	{
		if($timeFrom == '09:00:00' && $timeTo == '21:00:00'){
			return 0;
		}
		elseif($timeFrom == '09:00:00'){
			return 1;
		}
		elseif($timeFrom == '12:00:00'){
			return 2;
		}
		elseif($timeFrom == '15:00:00'){
			return 3;
		}
		elseif($timeFrom == '18:00:00'){
			return 4;
		}		
	}

				
	//По коду интервала вовзращает массив ['TimeFrom'=> ..,  'TimeTo'=> ..]
	public function getFromTo( $intervalID = 0 )
	{
		if($intervalID == '1'){
			return ['TimeFrom' => '09:00:00',  'TimeTo'=> '12:00:00'];
		}
		elseif($intervalID == '2'){
			return ['TimeFrom' => '12:00:00',  'TimeTo'=> '15:00:00'];
		}
		elseif($intervalID == '3'){
			return ['TimeFrom' => '15:00:00',  'TimeTo'=> '18:00:00'];
		}
		elseif($intervalID == '4'){
			return ['TimeFrom' => '18:00:00',  'TimeTo'=> '21:00:00'];
		}
		else{
			return ['TimeFrom' => '09:00:00',  'TimeTo'=> '21:00:00'];
		}		
	}


	
	public function getRegions()
	{
		$regionsArr = array(		
		'Адыгея республика'                         =>	'01',
		'Алтай республика'                          =>	'04',
		'Алтайский край'                            =>	'22',
		'Амурская область'                          =>	'28',
		'Архангельская область'                     =>	'29',
		'Астраханская область'                      =>	'30',
		'Башкортостан республика'                   =>	'02',
		'Белгородская область'                      =>	'31',
		'Брянская область'                          =>	'32',
		'Бурятия республика'                        =>	'03',
		'Владимирская область'                      =>	'33',
		'Волгоградская область'                     =>	'34',
		'Вологодская область'                       =>	'35',
		'Воронежская область'                       =>	'36',
		'Дагестан республика'                       =>	'05',
		'Еврейская автономная область'              =>	'79',
		'Забайкальский край'                        =>	'75',
		'Ивановская область'                        =>	'37',
		'Ингушетия республика'                      =>	'06',
		'Иркутская область'                         =>	'38',
		'Кабардино-Балкарская республика'           =>	'07',
		'Карачаево-Черкесская республика'           =>	'09',
		'Калининградская область'                   =>	'39',
		'Калмыкия республика'                       =>	'08',
		'Калужская область'                         =>	'40',
		'Камчатский край'                           =>	'41',
		'Карелия республика'                        =>	'10',
		'Кемеровская область'                       =>	'42',
		'Кировская область'                         =>	'43',
		'Коми республика'                           =>	'11',
		'Костромская область'                       =>	'44',
		'Краснодарский край'                        =>	'23',
		'Красноярский край'                         =>	'24',
		'Крым республика'                           =>	'91',
		'Курганская область'                        =>	'45',
		'Курская область'                           =>	'46',
		'Ленинградская область'                     =>	'78',//код Санкт-Петербурга
		'Липецкая область'                          =>	'48',
		'Магаданская область'                       =>	'49',
		'Марий Эл республика'                       =>	'12',
		'Московская область'                        =>	'77',//код региона берётся из Москвы
		'Москва'                                    =>	'77',
		'Мордовия республика'                       =>	'13',
		'Мурманская область'                        =>	'51',
		'Ненецкий автономный округ'                 =>	'83',
		'Нижегородская область'                     =>	'52',
		'Новгородская область'                      =>	'53',
		'Новосибирская область'                     =>	'54',
		'Омская область'                            =>	'55',
		'Оренбургская область'                      =>	'56',
		'Орловская область'                         =>	'57',
		'Пензенская область'                        =>	'58',
		'Пермский край'                             =>	'59',
		'Приморский край'                           =>	'25',
		'Псковская область'                         =>	'60',
		'Ростовская область'                        =>	'61',
		'Рязанская область'                         =>	'62',
		'Самарская область'                         =>	'63',
		'Санкт-Петербург'                           =>	'78',
		'Саратовская область'                       =>	'64',
		'Саха (Якутия) республика'                  =>	'14',
		'Сахалинская область'                       =>	'65',
		'Свердловская область'                      =>	'66',
		'Севастополь'                               =>	'92',
		'Северная Осетия-Алания'                    =>	'15',
		'Смоленская область'                        =>	'67',
		'Ставропольский край'                       =>	'26',
		'Тамбовская область'                        =>	'68',
		'Татарстан республика'                      =>	'16',
		'Тверская область'                          =>	'69',
		'Томская область'                           =>	'70',
		'Тульская область'                          =>	'71',
		'Тюменская область'                         =>	'72',
		'Тыва республика'                           =>	'17',
		'Удмуртская республика'                     =>	'18',
		'Ульяновская область'                       =>	'73',
		'Хабаровский край'                          =>	'27',
		'Хакасия республика'                        =>	'19',
		'Ханты-Мансийский-Югра автономный округ'    =>	'86',
		'Челябинская область'                       =>	'74',
		'Чеченская республика'                      =>	'20',
		'Чувашская республика'                      =>	'21',
		'Чукотский автономный округ'                =>	'87',
		'Ямало-Ненецкий автономный округ'           =>	'89',
		'Ярославская область'                       =>	'76',
		);
		return $regionsArr;
	}
	

	//Вовращет лог если файл есть, удаляет файл если лог выключен
	public function getLog()
	{
		$pathLog = __DIR__.'/logs/log.txt';
		if(get_option('lpost_wc_logging', 'no') == 'no'){
			if(file_exists($pathLog)){
				unlink($pathLog);
			}
			return false;
		}
		else{
			if(file_exists($pathLog)){
				return  str_replace(PHP_EOL , '<br>',file_get_contents($pathLog));
			}
			else{
				file_put_contents($pathLog, '' , FILE_APPEND);
			}
		}
	}
	
	
	// Логирование запросов
	public function logging($data = '')
	{
		$pathLog = __DIR__.'/logs/log.txt';
		if(get_option('lpost_wc_logging', 'no') == 'no'){
			return;
		}
		
		if(is_array($data)){
			$data = print_r($data,1);
		}
		$log = "[" . date('Y-m-d H:i:s') . "]" . '    ' . $data;
		file_put_contents($pathLog, $log . PHP_EOL . PHP_EOL, FILE_APPEND);
	}
	
	
	// Получение токена
	public function getAuthToken() 
	{
		$secret 	= get_option('lpost_wc_api_secret', '');
		$tokenEx 	= get_option('lpost_wc_api_token', '');
		$tokenDate 	= get_option('lpost_wc_api_token_date', '');
		$hash 		= get_option('lpost_wc_api_hash', '');
		
		if (!empty($this->errors['token']))
			unset($this->errors['token']);
		
		if (!empty($tokenEx) && !empty($tokenDate) && $hash == md5($this->url.$secret)) 
		{
			if (((time() - strtotime($tokenDate)) / 60) < 55) return $tokenEx;
		}
		try {
			
			$this->logging('Auth');
			$this->logging($secret);
			
			$response = wp_remote_get($this->url, array(
				'body' => array(
					'method' => 'Auth',
					'secret' => $secret,
				)
			));
			
			$this->logging($response['body']);

			if (!empty($response['body'])) 
			{
				$respMessage = json_decode($response['body']);
				if (!empty($respMessage->token)) 
				{
					$token = $respMessage->token;
					$valid_till = $respMessage->valid_till;
					$hash = md5($this->url.$secret);
					
					update_option('lpost_wc_api_token', $token);
					update_option('lpost_wc_api_token_date', date('d-m-Y H:i'));
					update_option('lpost_wc_api_hash', $hash);
					
					return $token;
				}
			}
			if (!empty($respMessage->errorMessage)) 
			{
				$this->errors['token'] = $respMessage->errorMessage;
			} 
		} catch (Throwable $e) {
			//echo "Ошибка запроса: " . $e->getMessage() . PHP_EOL;
		}
		return false;
	}
	
	// Отправка запроса на создание отправления в Л-Пост
	public function createOrders($orderIDs) 
	{
		if (!$orderIDs) return false;
		if (!$this->token) return false;
		$arOrdersInfo = array();
		foreach ($orderIDs as $orderID) 
		{
			$arOrdersInfo = array_merge($arOrdersInfo, $this->prepareCreateOrder($orderID));
		}
		$arParams = array(
			'Order' => $arOrdersInfo
		);
		//if(!empty($this->GetAddressPoints())){	
		//	$arParams['ID_PartnerWarehouse']= 
		//}
		$this->logging('CreateOrders');
		$this->logging(json_encode($arParams));
		$response = wp_remote_post($this->url, array(
			'timeout' => 30,
			'body' => array(
				'method' => 'CreateOrders',
				'token' => $this->token,
				'ver' => '1',
				'json' => json_encode($arParams)
			)
		));
		
		$this->logging($response['body']);
		
		if (isset($response->errors)) return $response->errors;

		$responseDecoded = json_decode($response['body']);
		$finalResponse = (isset($responseDecoded->JSON_TXT)) ? json_decode($responseDecoded->JSON_TXT) : $responseDecoded;

		return $finalResponse;
	}
	
	// Отправка запроса на обновление отправления в Л-Пост
	public function updateOrder($order_id) 
	{
		$shipment_id = get_post_meta($order_id, '_lp_shipment_id', true);
		if (!$order_id or !$shipment_id) return false;

		if (!$this->token) return false;

		$arOrdersInfo = $this->prepareCreateOrder($order_id, $shipment_id);
		$arParams = array(
			'Order' => $arOrdersInfo
		);

		$this->logging('UpdateOrders');
		$this->logging(json_encode($arParams));

		$response = wp_remote_request($this->url, array(
			'method' => 'PUT',
			'timeout' => 30,
			'body' => array(
				'method' => 'UpdateOrders',
				'token' => $this->token,
				'ver' => '1',
				'json' => json_encode($arParams)
			)
		));

		$this->logging($response['body']);

		if(!empty($response->errors)){
			return $response->errors;
		}

		$responseDecoded = json_decode($response['body']);
		$finalResponse = ($responseDecoded->JSON_TXT) ? json_decode($responseDecoded->JSON_TXT) : $responseDecoded;

		return $finalResponse;
	}

	// Подготовка данных для создания/обновления отправления
	// Возвращается массив отправлений 
	public function prepareCreateOrder($order_id, $shipment_id = 0) 
	{
		$arResult = array();
		
		$shipping_class_names = WC()->shipping->get_shipping_method_class_names();
		if (empty($shipping_class_names['lpost_wc_shipping'])) 
		{
			return array();
		}
		
		$WC_Order = wc_get_order($order_id);
		
		
		
		$raw_address = $WC_Order->get_address('shipping');
		$arAddress = array();
		if (!empty($raw_address['city']))
			$arAddress[] = $raw_address['city'];
		if (!empty($raw_address['address_1']))
			$arAddress[] = $raw_address['address_1'];
		if (!empty($raw_address['address_2']))
			$arAddress[] = $raw_address['address_2'];
	
	
	
	
		// Перебор доставок // Предполагаем только одну доставку для заказа
		$arShippingMethods = $WC_Order->get_shipping_methods();
		foreach ($arShippingMethods as $method) 
		{
			if ($method->get_method_id() != 'lpost_wc_shipping') 
				continue;
			
			if (!$method->get_instance_id())
				continue;
			
			// Получить данные из настроек доставки
			$methodInstance = new $shipping_class_names['lpost_wc_shipping']($method->get_instance_id());

			// Тип доставки
			$shipmentDelivType = $methodInstance->get_option('deliv_type', 'pickup');
			
			// Габариты отправления по умолчанию
			$shipmentLength = $methodInstance->get_option('dimensions_shipment_length', 38); // Длина (см.) 
			$shipmentWidth  = $methodInstance->get_option('dimensions_shipment_width',  31); // Ширина (см.) 
			$shipmentHeight = $methodInstance->get_option('dimensions_shipment_height', 29); // Высота (см.) 
			$shipmentVolume = floatval($shipmentLength) * floatval($shipmentWidth) * floatval($shipmentHeight); // Рассчитанный объем отправления
			
			// Габариты товара по умолчанию
			$productWeightDefault = floatval($methodInstance->get_option('dimensions_product_weight', 100)); // Вес (г.)

			$phone = preg_replace("/[^0-9]/", '', $WC_Order->get_billing_phone());
			if (strlen($phone) > 10) $phone = preg_replace('/^7|^8/m', '', $phone);

			$arData = array(
				// Номер отправления в системе партнера
				'PartnerNumber' => strval($WC_Order->get_id()), 
				// Тип выдачи отправлений
				'IssueType' => intval($methodInstance->get_option('issue_type', 0)),
				// Адрес доставки
				'Address' => (($shipmentDelivType == 'courier') ? implode(', ', $arAddress) : ''),
				// Объявленная стоимость отправления
				'Value' => floatval($WC_Order->get_total()), 
				// Сумма, которую требуется взять с получателя
				'SumPayment' => 0, 
				// Сумма предоплаты
				'SumPrePayment' => floatval($WC_Order->get_total()), 
				// Сумма, которая объявлена получателю, как стоимость за доставку
				'SumDelivery' => floatval($WC_Order->get_shipping_total()), 
				// Сумма прочих услуг(смс и т.п.)
				'SumServices' => 0, 
				// Номер отправления, который известен получателю
				'CustomerNumber' => strval($WC_Order->get_order_number()), 
				// Тип получателя отправления: 0 – физ.лицо, 1 – юр.лицо.
				'isEntity' => 0, 
				// ФИО получателя или название организации
				'CustomerName' => $WC_Order->get_formatted_shipping_full_name(), 
				// Номер телефона для связи с получателем и SMS информирования. Указывается без кода страны в формате 9999999999
				'Phone' => $phone,
				// Email получателя
				'Email' => $WC_Order->get_billing_email(),
			); 
			
			
			// Идентификатор склада перевозчика - Точка приёма отправлений
			if(empty($this->GetAddressPoints()) || empty($methodInstance->get_option('receive_id_warehouse', ''))){
				$arData['ID_Sklad'] = intval($methodInstance->get_option('receive_id_warehouse', 3));
			} else {
				$arData['ID_Sklad'] = 3;
				$arParams['ID_PartnerWarehouse'] = intval($methodInstance->get_option('receive_id_warehouse', ''));
			}
			
			// Дополнительное время на комплектацию в днях
			$daysForPicking = intval($methodInstance->get_option('days_for_picking', 0));
			if (!empty($daysForPicking))
			{
				$arData['DateShipment'] = wp_date('Y-m-d', strtotime("+ $daysForPicking day"));
			}
				
			// Точка приёма отправлений - переопределение
			$warehouseID = $WC_Order->get_meta('_lp_receive_id_warehouse');
			if (!empty($warehouseID))
			{
				if(empty($this->GetAddressPoints())){
					$arData['ID_Sklad'] = intval($warehouseID);
				} else {
					$arData['ID_Sklad'] = 3;
					$arParams['ID_PartnerWarehouse'] = intval($warehouseID);
				}
			}

			if (!empty($shipment_id))
			{
				// Номер отправления
				$arData['ID_Order'] = $shipment_id;
			}
			
			if ('pickup' == $shipmentDelivType)
			{
				// Идентификатор Пункта доставки
				$arData['ID_PickupPoint'] = intval($WC_Order->get_meta('_lp_pickup_point_id'));
			}
			
			if ('courier' == $shipmentDelivType)
			{
				// Заполняется только для курьерской доставки
				
				if (1 == $arData['IssueType'] || 2 == $arData['IssueType'])
				{
					// Ожидание курьером примерки. Если Fitting=1, курьер, при доставке, будет ожидать 15 минут, пока получатель примерит позиции из отправления. Fitting=1 указывается только при IssueType 1,2. 
					// $arData['Fitting'] = 1;
				}
				
				if (empty($arData['Address']))
				{
					$courier_coords = ($WC_Order->get_meta('_lp_courier_coords')) ? : null;
					$explodedCoords = ($courier_coords ? explode(',', preg_replace("/[^,.0-9]/", '', $courier_coords)) : array());
					// Широта
					$arData['Latitude'] = (2 == count($explodedCoords)) ? trim($explodedCoords[0]) : null;
					// Долгота
					$arData['Longitude'] = (2 == count($explodedCoords)) ? trim($explodedCoords[1]) : null;
				}
				
				// Номер Подъезда/Входа
				$porch = $WC_Order->get_meta('_lp_courier_porch');
				$arData['Porch'] = (!empty($porch) ? intval($porch) : null);
				// Номер Этажа
				$floor = $WC_Order->get_meta('_lp_courier_floor');
				$arData['Floor'] = (!empty($floor) ? intval($floor) : null);
				// Квартира/офис
				$flat = $WC_Order->get_meta('_lp_courier_flat');
				$arData['Flat'] = (!empty($flat) ? intval($flat) : null);
				// Код домофона
				$arData['Code'] = ($WC_Order->get_meta('_lp_courier_code')) ? : null;
				// Комментарий для курьера
				$arData['Comment'] = $WC_Order->get_customer_note();
				// Требуемая дата доставки
				

				if($methodInstance->get_option('not_show_date' , "no") == "no"){
					$arData['DateDeliv'] = ($WC_Order->get_meta('_lp_delivery_date')) ? : null;
				}
				else{
					//$arData['DateDeliv'] = "";
				}
				
				// Тип интервала времени доставки
				$TypeIntervalDeliv = ($WC_Order->get_meta('_lp_delivery_interval') >= 0 ) ? $WC_Order->get_meta('_lp_delivery_interval') : null;	
				$arData = $arData + $this->getFromTo($TypeIntervalDeliv);
				
			}

			// Получить товары
			$arProducts = array();
			$total_weight = 0;
			$order_items = $WC_Order->get_items();
			
			if (!$order_items)
				continue;
			
			foreach( $order_items as $item_id => $item )
			{
				// данные элемента заказа в виде массива
				$arProduct = $item->get_data();
				
				$product = wc_get_product($arProduct['product_id']);
				$weight = wc_get_weight($product->get_weight(), 'g');
		
				$arProducts[] = array(
					// Идентификатор товара в системе партнера
					'IDProductPartner' => $arProduct['product_id'],	
					// Наименование товара. Обязательно для всех типов вложения кроме документов
					'NameShort' => $arProduct['name'],	
					// Цена за единицу товара. Обязательно для всех типов вложения кроме документов
					'Price' => $arProduct['total'] / $arProduct['quantity'], 
					// Значение НДС для товара. Обязательно для всех типов вложения кроме документов
					'NDS' => 10,	
					// Количество единиц товара в ящике. Единица измерения – штуки. 
					'Quantity' => $arProduct['quantity'], 
				);
				$total_weight += ((!empty($weight) ? $weight : $productWeightDefault) * $arProduct['quantity']);
			}
			
			// Массив ящиков в отправлении
			$arData['Cargoes'] = array(
				array(
					// Масса ящика. Единица измерения - грамм
					'Weight' => $total_weight,
					// Длина ящика. Единица измерения - миллиметр
					'Length' => $shipmentLength * 10,
					// Ширина ящика. Единица измерения - миллиметр
					'Width' => $shipmentWidth * 10,
					// 	Высота ящика. Единица измерения - миллиметр
					'Height' => $shipmentHeight * 10,
					// Массив товаров в ящике
					'Product' => $arProducts,
				)
			);
			$arResult[] = $arData;
		}

		return $arResult;
	}

	// Запрос на получение информации об отправлении
	public function getInfoForLPostOrders($shipmentIDs) 
	{
		if (!$this->token) return array();
		
		foreach ($shipmentIDs as $shipment_id) 
		{
			$json[] = array('ID_Order' => $shipment_id);
		}

		$this->logging('GetStateOrders');
		$this->logging(json_encode($json));

		$response = wp_remote_get($this->url, array(
			'body' => array(
				'method' => 'GetStateOrders',
				'token' => $this->token,
				'ver' => '1',
				'json' => json_encode($json)
			)
		));

		$this->logging($response['body']);
		
		if (!empty($response['body']))
		{
			$responseDecoded = json_decode($response['body']);
			$finalResponse = json_decode($responseDecoded->JSON_TXT);
			return $finalResponse;
		}
		else 
		{
			return array();
		}
	}

	// Отправка запроса на создание акта
	public function createInvoice($shipmentIDs) 
	{
		if (!$shipmentIDs) return false;

		if (!$this->token) return array();

		$json = array('Act' => array());
		
		if (!is_array($shipmentIDs))
			$shipmentIDs = array($shipmentIDs);
		
		foreach ($shipmentIDs as $shipmentID) 
		{
			$json['Act'][] = array('ID_Order' => $shipmentID);
		}

		$this->logging('CreateAct');
		$this->logging(json_encode($json));

		$response = wp_remote_post($this->url, array(
			'body' => array(
				'method' => 'CreateAct',
				'token' => $this->token,
				'ver' => '1',
				'json' => json_encode($json)
			)
		));
		$this->logging($response['body']);
		
		$responseDecoded = json_decode($response['body']);
		$finalResponse = json_decode($responseDecoded->JSON_TXT);

		return $finalResponse;
	}


	//Поиск точки Авиа доставки по Городу
	public function getPickupAvia($city)
	{
		$optionsRawPickup = $this->GetPickupPoints('courier');
		foreach ($optionsRawPickup->PickupPoint as $item){
			if($item->CityName == $city && $item->isFastDelivery === 1 ){
			return $item->ID_PickupPoint;
			}
		}
	}




	// Получение всех пунктов для доставки
	public function getPickupPointsOptions() 
	{
		$optionsRawPickup = $this->GetPickupPoints('pickup');
		$optionsRawCourier = $this->GetPickupPoints('courier');
		
		$optionsRaw = array();
		if (!empty($optionsRawPickup->PickupPoint)) $optionsRaw = array_merge($optionsRaw, $optionsRawPickup->PickupPoint);
		if (!empty($optionsRawCourier->PickupPoint)) $optionsRaw = array_merge($optionsRaw, $optionsRawCourier->PickupPoint);

		$options = array();
		foreach ($optionsRaw as $item) 
		{
			$options[$item->ID_PickupPoint] = $item->CityName.', '.$item->Address;
		}

		asort($options);
		return $options;
	}

	// Запрос на получение списка пунктов доставки
	public function GetPickupPoints($delivType = 'pickup', $forceUpdate = false) 
	{
		if (!$this->token) return array();
		
		$points = false;

		$pickupFileName = __DIR__.'/pickup-points-'. $delivType .'.json';
		if (!file_exists($pickupFileName)) $pickupFile = fopen($pickupFileName, 'a+');

		if (filesize($pickupFileName) === 0 or date('d', filemtime($pickupFileName)) !== date('d') or $forceUpdate) {
			$arParams = array(
				'isCourier' => ($delivType === 'pickup') ? '0' : '1'
			);
			
			$this->logging('GetPickupPoints');
			$this->logging(json_encode($arParams));
			
			$response = wp_remote_get($this->url.'?method=GetPickupPoints', array(
				'timeout' => 30,
				'body' => array(
					'ver' => 1,
					'token' => $this->token,
					'json' => json_encode($arParams)
				)
			)); //запрос на получение
			$this->logging($response['body']);
			
			
			
			if (!empty($response['body']))
			{
				$body = json_decode($response['body']);

				if (isset($body->errorMessage)) return array();

				//fclose($pickupFile);
				$pickupFile = fopen($pickupFileName, 'w+');
				fwrite($pickupFile, $response['body']);
				$points = $body;
			}
		} 
		else 
		{
			$points = json_decode(file_get_contents($pickupFileName));
		}
		
		$res = ($points) ? json_decode($points->JSON_TXT) : array();
		return $res;
	}


	// Запрос на получение списка активных складов партнера и их адреса
	public function GetAddressPoints()
	{
		if (!$this->token){
			return array();		
		}
		$warehousesFileName = __DIR__.'/active-warehouses.json';
		if (!file_exists($warehousesFileName)){
			$warehousesFile = fopen($warehousesFileName, 'a+');		
		}
			
		if (filesize($warehousesFileName) === 0 || date('d', filemtime($warehousesFileName)) !== date('d')){
			
			$this->logging('GetAddressPoints');			
			$response = wp_remote_get($this->url.'?method=GetAddressPoints', array(
				'timeout' => 10,
				'body' => array(
					'token' => $this->token,
					'ver' => '1',
					'json' => '{}'
				)
			));
			$this->logging($response['body']);
			
			
			$body = json_decode($response['body']);
			if (isset($body->errorMessage)){
				return false;
			}
		
			$warehousesFile = fopen($warehousesFileName, 'w+');
			fwrite($warehousesFile, $response['body']);
			fclose ($warehousesFile);
			$points = $body;
		} else {
			$points = json_decode(file_get_contents($warehousesFileName));
		}	
		
		if(empty($points->Message)){
			return json_decode($points->JSON_TXT,true)['Points'];
		} else {
			return false;
		}
	}


	// Запрос на получение списка точек приема отправлений.
	public function GetReceivePoints() 
	{
		if (!$this->token) return array();

		$pickupFileName = __DIR__.'/receive-points.json';
		if (!file_exists($pickupFileName)) $pickupFile = fopen($pickupFileName, 'a+');

		if (filesize($pickupFileName) === 0 or date('d', filemtime($pickupFileName)) !== date('d')) {
			
			
			$this->logging('GetReceivePoints');
			$response = wp_remote_get($this->url.'?method=GetReceivePoints', array(
				'timeout' => 10,
				'body' => array(
					'token' => $this->token,
					'ver' => '1',
					'json' => '{}'
				)
			));
			$this->logging($response['body']);


			$body = json_decode($response['body']);
			if (isset($body->errorMessage)) return false;

			$pickupFile = fopen($pickupFileName, 'w+');
			fwrite($pickupFile, $response['body']);
			$points = $body;
		} else {
			$points = json_decode(file_get_contents($pickupFileName));
		}

		return json_decode($points->JSON_TXT);
	}
	
	// Получение сортированного списка точек приема отправлений
	public function getSortedReceivePointsOptions() 
	{
		$receiveFieldOptions = array();
		$receivePoints = $this->GetReceivePoints();
		if (!empty($receivePoints)) 
		{
			if(empty($this->GetAddressPoints())){
				$idCurier = '';
				foreach ($receivePoints->ReceivePoints as $point) 
				{	
					if( ($point->City.', '.$point->Address) == 'Родники, ул.Трудовая, д.10'){
						$idCurier = $point->ID_Sklad;
						continue;
					}
					$receiveFieldOptions[$point->ID_Sklad] = $point->City.', '.$point->Address;
				}
				asort($receiveFieldOptions);
				//Замена 'Родники, ул.Трудовая, д.10' на 'Курьер Л-Пост' с добавлением в начало
				if($idCurier !== ''){
					$receiveFieldOptions = [$idCurier => 'Курьер Л-Пост'] + $receiveFieldOptions;
				}		
			} else {
				foreach ($this->GetAddressPoints() as $point){
					$receiveFieldOptions[$point['ID_PartnerWarehouse']] = $point['Address'];
				}
				asort($receiveFieldOptions);
			}
		}
		return $receiveFieldOptions;
	}
	
	// Запрос на расчет стоимости услуг и срока доставки
	public function makeCalcRequest($json)
	{

	    error_log(print_r($json,1 ));

		if (!$this->token) return array();
		$this->logging('GetServicesCalc');
		$this->logging(json_encode($json));
		$response = wp_remote_get($this->url.'?method=GetServicesCalc', array(
			'timeout' => 10,
			'body' => array(
				'token' => $this->token,
				'ver' => '1',
				'json' => json_encode($json)
			)
		));
		$this->logging($response['body']);
		return $response;
	}
	
	// Преобразование ответа на запрос в массив
	public function resDecode($response, $associative = false) 
	{
		if (empty($response['body']))
			return array();
		
		$arData = array();
		
		$body = json_decode($response['body']);
		if (!empty($body->Message)) 
		{
			$arData['message'] = $body->Message;
		}
		if (!empty($body->errorMessage)) 
		{
			$arData['errorMessage'] = $body->errorMessage;
		}
		if (!empty($body->JSON_TXT)) 
		{
			$obJSON = json_decode($body->JSON_TXT, $associative);
			$arData['result'] = $obJSON['JSON_TXT'][0];
		}
		return $arData;
	}
	
	// Получение название статуса для указанного кода
    public function convertDeliveryStates($state) 
	{

		$stateArrs = array(
			'CREATED' => 'Создано',
			'ACT_CREATED' => 'Создан акт',
			'READY_FOR_RETURN' => 'Готово к отгрузке на склад Л-Пост',
			'SENT_TO_WAREHOUSE' => 'Отправлено на склад Л-Пост',
			'ARRIVED_AT_THE_WAREHOUSE' => 'Прибыло на склад',
			'ACCEPTED_BY_PLACES' => 'Принято по местам',
			'SENT_TO_PICKUP_POINT' => 'Отправлено в Пункт доставки',
			'PLACED_IN_PICKUP_POINT' => 'Размещено в пункте доставки',
			'RECEIVED' => 'Выдано получателю',
			'DONE' => 'Выполнено',
			'CANCELLED' => 'Аннулировано',
			'ARCHIVE' => 'Архив',
		);

		return ($stateArrs[$state]) ? : $state;
    }
	
	// Разбирает строку с параметрами на массив
	public function proper_parse_str($queryString, $argSeparator = '&', $decType = PHP_QUERY_RFC1738) 
	{
		if (empty($queryString)) return array();
		$result = array();
		$parts = explode($argSeparator, $queryString);
		foreach ($parts as $part) 
		{
			list($paramName, $paramValue) = explode('=', $part, 2);
			switch ($decType) 
			{
				case PHP_QUERY_RFC3986:
					$paramName = rawurldecode($paramName);
					$paramValue = rawurldecode($paramValue);
				break;
				case PHP_QUERY_RFC1738:
				default:
					$paramName = urldecode($paramName);
					$paramValue = urldecode($paramValue);
				break;
			}
			if (preg_match_all('/\[([^\]]*)\]/m', $paramName, $matches)) 
			{
				$paramName = substr($paramName, 0, strpos($paramName, '['));
				$keys = array_merge(array($paramName), $matches[1]);
			} 
			else 
			{
				$keys = array($paramName);
			}
			$target = &$result;
			foreach ($keys as $index) 
			{
				if ($index === '') 
				{
					if (isset($target)) 
					{
						if (is_array($target)) 
						{
							$intKeys = array_filter(array_keys($target), 'is_int');
							$index  = count($intKeys) ? max($intKeys)+1 : 0;
						} 
						else 
						{
							$target = array($target);
							$index  = 1;
						}
					} 
					else 
					{
						$target = array();
						$index = 0;
					}
				} 
				elseif (isset($target[$index]) && !is_array($target[$index])) 
				{
					$target[$index] = array($target[$index]);
				}
				$target = &$target[$index];
			}
			
			if (is_array($target)) 
			{
				$target[] = $paramValue;
			} 
			else 
			{
				$target = $paramValue;
			}
		}
		
		return $result;
	}
}