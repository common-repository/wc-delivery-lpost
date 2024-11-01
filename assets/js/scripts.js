jQuery(document).ready(function () {
	
	//Управление интервалами в зависимости от выбранной даты	
	jQuery('body').on('change', 'select#_lp_delivery_date', function() {
		function inArray(needle, haystack)
		{
			var length = haystack.length;
			for(var i = 0; i < length; i++) {
				if(haystack[i] == needle) return true;
			}
			return false;
		}
		let intervalsMap = JSON.parse(jQuery('input#_lp_intervalsMap').val());
		
		if(intervalsMap == 'error'){
			//jQuery("p#_lp_delivery_interval_field").hide();
			console.log('выбран не верный адрес, управление календарём отключено');
			return;
		}
		
		
		
		let thisVal = jQuery(this).val();
		let thisMap = intervalsMap[thisVal];
		//сменить выбор интервала если выбранный не входит в перечень
		if( !inArray(jQuery('select#_lp_delivery_interval').val(), thisMap) )
		{
			jQuery("select#_lp_delivery_interval option[value=" + thisMap[0] + "]").prop('selected', 'true');
		}
		//Скрыть неактуальные option
		jQuery("select#_lp_delivery_interval option").each(function( index ) {
			//console.log('обработка опций');
		  if(!inArray(jQuery( this ).attr('value') , thisMap)){
			  jQuery( this ).hide();
		  }
		  else{
			  jQuery( this ).show();
		  }
		});
	});
	
	
	
	
	
	let inpCity = (jQuery('[name="shipping_city"]').is(':visible') ? jQuery('[name="shipping_city"]') : jQuery('[name="billing_city"]')),
		inpAddress = (jQuery('[name="shipping_address_1"]').is(':visible') ? jQuery('[name="shipping_address_1"]') : jQuery('[name="billing_address_1"]')),
		boxCourierFields = jQuery('#lp_courier_checkout_field'),
		inpCourierFields = boxCourierFields.find('[name]'),
		curShippingMethodID = 0,
		pickupMap,
		zoneGeoObjects = [];
		
	let inpCoordsVal = function(v) {
		let inp = jQuery('[name="_lp_courier_coords"]');
		if (inp.length)
		{
			if (typeof v != 'undefined') {
				inp.val(v);
			}
			return inp.val();
		}
		return '';
	}
	let inpPickupVal = function(v) {
		let inp = jQuery('[name="_lp_pickup_point_id"]');
		if (inp.length)
		{
			if (typeof v != 'undefined') {
				inp.val(v);
			}
			return inp.val();
		}
		return '';
	}
	
	jQuery('body').on('updated_checkout', function(event, data) {
		var isShowBoxCourierFields = false;
		if (jQuery('#shipping_method [data-deliv_type]').length > 0) {
			if ('courier' == jQuery('#shipping_method [data-deliv_type]').data('deliv_type')){
				isShowBoxCourierFields = true;
			}
		}
		boxCourierFields.css({'display': (isShowBoxCourierFields?'block':'none')});
		inpCourierFields.prop('disabled', !isShowBoxCourierFields);

		
		if (jQuery('[name="ID_PickupPoint"]').length > 0) {
			inpAddress.attr('readonly', 'readonly');
		} else {
			inpAddress.removeAttr('readonly');
		}
		
		if (!jQuery('#colorbox').is(':visible')) 
		{
			let method = jQuery('#shipping_method input.shipping_method:checked');
			if (method.length > 0) {
				let selShippingMethodID = method.val();
				if (curShippingMethodID && curShippingMethodID != selShippingMethodID) {
					openBoxMap();
				}
				curShippingMethodID = selShippingMethodID;
			} else {
				curShippingMethodID = 0;
			}
		}

	});
	
	// При изменении города, очищать адрес, координаты и склад
	jQuery('[name*="_city"]').on('change', function (event) {
		if (jQuery(event.target).attr('name') === inpCity.attr('name'))
		{
			inpAddress.val('');
			inpCoordsVal('');
			inpPickupVal('');
		}
		// перерасчет при заполнении города
		jQuery('body').trigger('update_checkout');
	});





	// При изменении региона, очищать адрес, координаты и склад
	jQuery('[name="billing_state"]').on('change', '', function (e) {
	jQuery('body').trigger('update_checkout');
	});
	


	// При изменении режима Дешево/Быстро запускать расчёт
	jQuery('body').on('change', '[name="_lp_delivery_mode"]', function (e) {
	jQuery('body').trigger('update_checkout');
	});


	
	// При изменении адреса, удалять координаты и склад
	jQuery('[name*="address_1"]').on('change', function (event) {
		if (jQuery(event.target).attr('name') === inpAddress.attr('name'))
		{
			inpCoordsVal('');	
			inpPickupVal('');
		}
	});

	jQuery('body').on('click', '.ch-pickup-pont', function () {
		openBoxMap();
	});
	
	function openBoxMap() {
		
		
		let inpCity = (jQuery('[name="shipping_city"]').is(':visible') ? jQuery('[name="shipping_city"]') : jQuery('[name="billing_city"]'));
		
		let deliv_type 	= jQuery('.ch-pickup-pont').data('deliv_type'),
			mapContainerTitle = "map-container-" + deliv_type,
			mapPoints 	= window['pickupPoints' + deliv_type],
			dateMode = window['dateMode'],
			iconPickup 	= {iconLayout:'default#image', iconImageHref:WPLPURLS.images+'L-Post-logo-yandex-map-pin.png', iconImageSize:[44, 51], iconImageOffset:[-15, -40]},
			iconCourier	= {iconLayout:'default#image', iconImageHref:WPLPURLS.images+'L-Post-logo-yandex-map-pin.png', iconImageSize:[44, 51], iconImageOffset:[-15, -40]},
			searchControl = new ymaps.control.SearchControl({options: {size:'large',provider:'yandex#map', noPlacemark:true, placeholderContent: 'Введите адрес доставки', kind:'house'}});
			
		ymaps.ready(function(){
			// Указывается идентификатор HTML-элемента.
			pickupMap = new ymaps.Map(jQuery('#' + mapContainerTitle).find('.map-element').get(0), {
				behaviors: ["default", "scrollZoom"],
				center: [55.76, 37.64],
				type: "yandex#map",
				zoom: 10,
				controls: []
			},
			{
				minZoom: 1,
				maxZoom: 18
			});
			pickupMap.controls.add('zoomControl', {
				float: 'none',
				position: {
					top: '20px',
					right: '20px'
				}
			});
			if (inpCity.val())
			{
				ymaps.geocode(inpCity.val(), {
					results: 1
				}).then(function (res) {
					
					

					pickupMap.setCenter(res.geoObjects.get(0).geometry.getCoordinates());
				});
			}

			if (deliv_type === 'pickup') {
				jQuery('#' + mapContainerTitle).append('<div class="b-yamap-search"><div class="b-yamap-search-form"><input type="text" name="pickup-search" placeholder="Найти по улице или станции метро"/></div><div class="b-yamap-search-result"></div></div>')
				if (jQuery(window).width() > 767) pickupMap.margin.setDefaultMargin([20, 20, 20, 330]);
			}
			jQuery(mapPoints).each(function (index, el) {
				
				let textAvia = '';
				if ( typeof el.isFastDelivery !== "undefined" && el.isFastDelivery === 1 ) {
					textAvia = ' (Авиа)';
				}
				else{
					textAvia = '';
				}
				//console.log('туууууууууууууууууут');
				//console.log(dateMode);
				
				
				let balloon_date = '';
				let sidebar_date = '';
				if (dateMode === 'no'){
				balloon_date = '<div class="b-yamap-balloon-e-when">Доставка '+ el.DeliveryDate + '</div>';
				sidebar_date =  '<div class="b-yamap-search-when">Доставка '+ el.DeliveryDate + '</div>';
				}
			
				
				
				if (el.IsCourier == 0 && deliv_type === 'pickup') {
					// самовывоз
					let paymentInfo = '<span class="icon-payment-prepaid"></span> Только предоплата';
						workHours = '<div>Время работы:</div>',
						metroString = (el.Metro !== undefined) ? el.Metro : ''
						startHours = el.SimpleWorkHours[0].From + ' - ' + el.SimpleWorkHours[0].To,
						startDay = 'пн';

					if (el.IsCard === 1 && el.IsCash === 1) {
						paymentInfo = '<span class="icon-payment-acquire"></span> Оплата картой или наличными';
					} 
					if (el.IsCard !== 1 && el.IsCash === 1) {
						paymentInfo = '<span class="icon-payment-prepaid"></span> Оплата только наличными';
					} 
					if (el.IsCard === 1 && el.IsCash !== 1) {
						paymentInfo = '<span class="icon-payment-acquire"></span> Оплата только картой';
					} 
					
					jQuery.each(el.SimpleWorkHours, function (indexInfo, dayInfo) {
						let dayStart = dayInfo.From,
							dayEnd = dayInfo.To,
							currHours = dayInfo.From + ' - ' + dayInfo.To,
							currDay = dayInfo.shortTitle,
							actDay = currDay;

						if (currHours !== startHours || indexInfo == 6) {
							if (currDay !== startDay) actDay = startDay + '-' + currDay

							workHours += '<span class="day">' + actDay + ': ' + currHours + '</span>';
							startDay = currDay;
							startHours = currHours;
						}
					}); //время работы

					var myPlacemark = new ymaps.Placemark([el.Latitude, el.Longitude], {
						hintContent: el.Address,
						balloonContentBody: 
							`<div class="b-yamap-balloon">
								<div class="b-yamap-balloon-e-metro b-yamap-stl">`+metroString+`</div>
								<div class="b-yamap-balloon-e-address">` + el.CityName + ', ' + el.Address + textAvia + `</div>
								<div class="b-yamap-balloon-row">
									<div class="b-yamap-balloon-col">
										<div class="b-yamap-balloon-e-howto">`+ el.PickupDop + `</div>
										<div class="b-yamap-balloon-e-howto">` + paymentInfo + `</div>
									</div>
									<div class="b-yamap-balloon-col">
										`+ balloon_date + `
										<div class="b-yamap-balloon-e-work">` + workHours + `</div>
										<div class="b-yamap-balloon-e-button"><span class="button" data-pickuppoint="` + el.ID_PickupPoint + `" data-address="` + el.Address + `">Заберу отсюда</span></div>
									</div>
								</div>
							</div>`
					}, iconPickup);
					
					jQuery('.b-yamap-search-result').append(
						`<div class="b-yamap-search-item" data-id="` + el.ID_PickupPoint + `">
							<div class="b-yamap-search-metro">`+metroString+`</div>
							` + el.CityName + ', ' + el.Address + textAvia + `
							`+ sidebar_date + `
							<div class="b-yamap-balloon-e-howto">` + paymentInfo + `</div>
							
							<div class="b-yamap-search-item-info" style="display: none">
								<div class="b-yamap-balloon-e-work">` + workHours + `</div>
								<div class="b-yamap-balloon-e-button">
									<span class="button" data-pickuppoint="` + el.ID_PickupPoint + `" data-address="` + el.Address + `">Заберу отсюда</span>
								</div>
								<div class="b-yamap-balloon-e-howto">`+ el.PickupDop + `</div>
							</div>
						</div>`
					);

					myPlacemark.events.add('click', function (e) {
						jQuery('.b-yamap-search .b-yamap-search-item[data-id="'+el.ID_PickupPoint+'"]').trigger('click');
					})
					pickupMap.geoObjects.add(myPlacemark, el.ID_PickupPoint); // самовывоз
				}
				if (el.IsCourier == 1 && deliv_type === 'courier') {
					// курьер
					//zoneGeoObjects = [];
					jQuery(el.Zone).each(function (index, zonearea) {
						
						//console.log('индекс добавления в зону ' + index);
						
						
						// Зона возможной доставки 
						if (zonearea.WKT.GeometryType == 'Polygon' || zonearea.WKT.GeometryType == 'MultiPolygon') 
						{
							let zoneGeoObject = new ymaps.Polygon(
								zonearea.WKT.Coordinates, {}, {
									fillColor: "#00000010",
									strokeWidth: 1,
									strokeColor: "#00000010"
								}
							);
							
							
							zoneGeoObject.events.add('click', function (e) {
								showCourierBalloon(e.get('coords'));
							});
							

							zoneGeoObjects.push(zoneGeoObject);
							pickupMap.geoObjects.add(zoneGeoObject);
						}
					});

				}
			});

			if (deliv_type === 'courier') { 
				// действия для курьера
				pickupMap.controls.add(searchControl);
				searchControl.events.add('resultselect', function (e) {
					let index = searchControl.getSelectedIndex(e);
						coords = searchControl.getResultsArray()[index].geometry.getCoordinates();
					showCourierBalloon(coords);
				});
				
				pickupMap.events.add('click', function (e) {
					showCourierBalloon(e.get('coords'));
				});


				let inpCity = (jQuery('[name="shipping_city"]').is(':visible') ? jQuery('[name="shipping_city"]') : jQuery('[name="billing_city"]'));
				if (inpCoordsVal()) {
					showCourierBalloon(JSON.parse('['+inpCoordsVal()+']'));
				} else {
					if (inpCity.val() && inpAddress.val()){
						ymaps.geocode(inpCity.val()+ ' ' +inpAddress.val(), {
							results: 1
						}).then(function (res) {
							showCourierBalloon(res.geoObjects.get(0).geometry.getCoordinates());
						})
					}
				}
			}

			jQuery.colorbox({
				'inline': true,
				'href': jQuery('#' + mapContainerTitle),
				'closeButton': false
			});

			jQuery(document).bind('cbox_closed', function(){
				
				zoneGeoObjects = [];
				//console.log('уничтожение');
				pickupMap.destroy();
				
			});
		});
	}

	// Нажатие на пункт доставки в списке
	jQuery('body').on('click', '.b-yamap-search .b-yamap-search-item', function (event) {
		let el = jQuery(this),
			parent = el.parent(),
			id = el.data('id'),
			placeMark = pickupMap.geoObjects.get(id);

		parent.find('.b-yamap-search-item').not('[data-id="'+id+'"]').find('.b-yamap-search-item-info').slideUp(100);
		
		if (event.isTrigger) 
			setTimeout(function() {
				let p =  parent.scrollTop() + el.position().top;
				parent.animate({scrollTop: p}, 200);
			}, 300);

		el.find('.b-yamap-search-item-info').slideDown(400);
		
		if (event.isTrigger) return true;

		placeMark.balloon.open();
		pickupMap.setCenter(placeMark.geometry.getCoordinates(), pickupMap.getZoom(), {
			duration: 300,
			useMapMargin: true
		});
	});
	
	// Нажатие на "Доставить сюда"
	jQuery('body').on('click', '#map-container-pickup .b-yamap-balloon-e-button .button', function () {
		inpAddress.val(this.dataset.address);
		//console.log('Выбрана точка');
		jQuery('[name="_lp_pickup_point_id"]').val(this.dataset.pickuppoint);
		jQuery('[name="_lp_pickup_point_id"]').attr('value', this.dataset.pickuppoint);
		window.noFboxOpen = true;
		jQuery('body').trigger('update_checkout');
		jQuery.colorbox.close();
	});

	function showCourierBalloon(coords) {
		
		//console.log(zoneGeoObjects);
		if (!coords) 
			return false;

		if (pickupMap.balloon.isOpen()) {
			pickupMap.balloon.close();
		} 
		pickupMap.balloon.open(coords, {content: 'Загрузка...'}, {closeButton: false});
		ymaps.geocode(coords, {
			results: 1
		}).then(function (res) {
			
			
			
			let isSet = false;
			let address = res.geoObjects.get(0).getAddressLine();
			let number = res.geoObjects.get(0).getPremiseNumber();
			let contentBody = 'Мы не можем доставить заказ по адресу: <strong>'+address+'</strong>. Выберите другой адрес.';
			//console.log(zoneGeoObjects);
			
			jQuery(zoneGeoObjects).each(function (index, zonePolygon) {
				//console.log(index);
				
				if (zonePolygon.geometry.contains(coords) === true && !isSet) {
					isSet = true;
					if (typeof number != 'undefined') {
						contentBody = address+'<div class="b-yamap-balloon-e-button"><span class="button" data-coords="' + coords + '">Привезти сюда</span></div>';
					} else {
						contentBody = 'Мы не можем доставить заказ по адресу: <strong>'+address+'</strong>. Выберите конкретный адрес с номером дома.';
					}
					
				} 
			});
			
			pickupMap.balloon.setData({content: contentBody});
			
		},
		function (err) {
			// Обработка ошибки.
			console.log('Ошибка');
		}
		
		);
	}
	//Нажатие на привезти сюда Курьер
	jQuery('body').on('click', '#map-container-courier .b-yamap-balloon-e-button .button', function () {
		let coords = this.dataset.coords;
		window.noFboxOpen = true;
		inpCoordsVal('');
		if (coords) {
			inpCoordsVal(coords);
			ymaps.geocode(JSON.parse('['+coords+']')).then(function (res) {

				let addressObj = res.geoObjects.get(0),
					addressCountry = addressObj.getCountry(),
					addressCity = addressObj.getLocalities()[0],
					addressText = addressObj.getAddressLine(),
					addressArea = addressObj.getAdministrativeAreas()[0];
					
					
					//Переворачиваем республико из стиля яндекс в стиль сайта
					let isreplacer = 0;
					let newString = '';
					newString = addressArea.replace('Республика ', replacer);
					if(isreplacer > 0){
						newString = newString + ' республика';
					}
					function replacer() {
					  isreplacer = isreplacer + 1;
					  return '';
					}
					addressArea = newString;
					
				addressText = addressText.replace(addressCountry+', ', '');
				addressText = addressText.replace(addressCity+', ', '');

				
				//Вписываем город и адрес
				let inpAddress = (jQuery('[name="shipping_address_1"]').is(':visible') ? jQuery('[name="shipping_address_1"]') : jQuery('[name="billing_address_1"]'));
				let inpCity = (jQuery('[name="shipping_city"]').is(':visible') ? jQuery('[name="shipping_city"]') : jQuery('[name="billing_city"]'));
				inpCity.val(addressCity);
				inpAddress.val(addressText);
		
		
				//Выбираем регион из выпадающего списка
				if(jQuery('select#billing_state option[value="'+addressArea+'"]').length > 0){
				jQuery('select#billing_state option[value="'+addressArea+'"]').prop('selected', true);
				jQuery('span#select2-billing_state-container').text(addressArea);
				jQuery('span#select2-billing_state-container').attr('title',addressArea);
				}
			});
		}

		jQuery('body').trigger('update_checkout')
		jQuery.colorbox.close();
	});

	// Поиск по улицам
	jQuery('body').on('keyup', '[name="pickup-search"]', function (event) {
		var element = jQuery(this);
		if (this.value === '') {
			jQuery('.b-yamap-search-result .b-yamap-search-item').slideDown(200);
			return true
		}

		jQuery('.b-yamap-search-result .b-yamap-search-item').hide().each(function (index, el) {
			let itElement = jQuery(this);
			if (itElement.text().toUpperCase().includes(element.val().toUpperCase()))
				itElement.slideDown(200);
		})
	});

});