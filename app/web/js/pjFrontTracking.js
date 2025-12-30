(function (window, undefined){
	"use strict";
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		dialog = (pjQ.$.fn.dialog !== undefined),
		datepicker = (pjQ.$.fn.datepicker !== undefined),
		mask = (pjQ.$.fn.mask !== undefined),
		tipsy = (pjQ.$.fn.tipsy !== undefined),
		$filterTimer = null, 
		$timerLoadTracking = null,
		$delayTime = 10000,
		$currentlyTrackingId = null,
		map,
        roadmap,
        satellite,
        hybrid,
        terrain,
        baseLayers,
        vehicleMarkersMap = {},
        vehicleMarkers,
        IdleIcon,
        MovingIcon,
		routes = [
          	{pattern: /^#!\/Tracking$/, eventName: "loadTracking"},
          ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadTracking");
		}
	}
	
	function detectIE() {
	    var ua = window.navigator.userAgent;

	    var msie = ua.indexOf('MSIE ');
	    if (msie > 0) {
	        // IE 10 or older => return version number
	        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	    }

	    var trident = ua.indexOf('Trident/');
	    if (trident > 0) {
	        // IE 11 => return version number
	        var rv = ua.indexOf('rv:');
	        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	    }

	    var edge = ua.indexOf('Edge/');
	    if (edge > 0) {
	        // Edge (IE 12+) => return version number
	        return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	    }

	    // other browser
	    return false;
	}
	
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function pjTracking(opts) {
		if (!(this instanceof pjTracking)) {
			return new pjTracking(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	pjTracking.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	pjTracking.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	pjTracking.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.opts = {};
			this.booking_uuid = null;
			return this;
		},
		disableButtons: function () {
			var $el;
			this.$container.find(".pjTrackingSelectorButton").each(function (i, el) {
				$el = pjQ.$(el).attr("disabled", "disabled");
				pjQ.$(el).find('.fa-spinner').show();
			});
		},
		enableButtons: function () {
			var $obj = this.$container.find(".pjTrackingSelectorButton");
			$obj.removeAttr("disabled");
			$obj.find('.fa-spinner').hide();
		},
		
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjTrackingContainer_" + this.opts.index);
			this.$container = pjQ.$(this.container);
			
			pjQ.$(window).on("loadTracking", this.container, function (e) {
				self.loadTracking.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadTracking.call(this);
			} else {
				onHashChange.call(null);
			}
		},
		loadTracking: function () {
			var self = this;
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontTracking&action=pjActionTracking", "&session_id=", self.opts.session_id].join(""), {
				"locale": this.opts.locale,
				"hide": this.opts.hide,
				"index": this.opts.index,
				"hash": this.opts.hash
			}).done(function (data) {
				self.$container.html(data);
				
				if (pjQ.$('#map-tracking').length > 0) {
					if ($timerLoadTracking !== null) {
	        	        clearTimeout($timerLoadTracking);
	        	    }
					self.initMap.call(self);
				} else {
					$timerLoadTracking = setTimeout(function() {
		        		self.loadTracking.call(self);
		            }, $delayTime);
				}
			});
		},
		initMap: function() {
			var self = this;
			if (pjQ.$('#map-tracking').length > 0) {
				map = L.map('map-tracking', {
		            zoomControl: false 
		        }).setView([47.2576489, 11.3513075], 13);
				// Lấy ngôn ngữ ưu tiên của trình duyệt (ví dụ: 'en-US', 'vi-VN')
		        const clientLanguage = navigator.language || navigator.userLanguage || 'en';
		        
		        // Chỉ lấy mã ngôn ngữ cơ bản (ví dụ: 'en', 'vi', 'de')
		        // Dùng slice(0, 2) để cắt lấy 2 ký tự đầu tiên
		        const languageCode = clientLanguage.slice(0, 2).toLowerCase(); 
		        
		        const langParam = `&hl=${languageCode}`;
				// --- 1. ĐỊNH NGHĨA CÁC LỚP BẢN ĐỒ (TILE LAYERS) ---
	
		        // A. Roadmap (Mặc định)
		        roadmap = L.tileLayer('http://{s}.google.com/vt/lyrs=m'+langParam+'&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3'],
		            attribution: 'Map data &copy; Google'
		        }).addTo(map); // Thêm Roadmap làm lớp mặc định
	
		        // B. Satellite
		        satellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3']
		        });
	
		        // C. Hybrid (Kết hợp Roadmap và Satellite)
		        hybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3']
		        });
		        
		        // D. Terrain/Tôp địa hình (Thường dùng lyrs=p hoặc lyrs=t)
		        terrain = L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3']
		        });
		        
		        L.control.zoom({
		            position: 'topright' // Đặt nút thu phóng ở vị trí mong muốn
		        }).addTo(map);
		        
		     // --- 2. THÊM CÔNG CỤ ĐIỀU KHIỂN CHỌN LAYER ---
		        baseLayers = {
		            "Roadmap": roadmap,
		            "Satellite": satellite,
		            "Hybrid": hybrid,
		            "Terrain": terrain
		        };
		        
		        L.control.layers(baseLayers, null, { collapsed: true, position: 'bottomright'}).addTo(map);
		        
		        
		        vehicleMarkersMap = {};
		        vehicleMarkers = L.featureGroup().addTo(map); // Nhóm chứa tất cả các marker
	
		        IdleIcon = L.divIcon({
		        	className: 'custom-vehicle-icon',
		            html: '<i class="fa fa-car"></i>', 
		            iconSize: [34, 34], // Điều chỉnh kích thước lớn hơn một chút để chứa nền
		            iconAnchor: [12, 30], // Căn giữa
		            popupAnchor: [0, -34]
		        });
		        
		        MovingIcon = L.divIcon({
		            className: 'moving-vehicle-icon', // Sử dụng CSS mới (màu xanh lá)
		            html: '<i class="fa fa-car"></i>', 
		            iconSize: [34, 34], 
		            iconAnchor: [12, 30], 
		            popupAnchor: [0, -34] 
		        });
			}
			if (pjQ.$('#vehicle_id_from_api').length > 0) { 
		        var $vehicle_id = pjQ.$('#vehicle_id_from_api').val();
				$currentlyTrackingId = $vehicle_id;
				self.loadVehicle.call(self, $vehicle_id);
			}
		},
		bindHoverPopup: function(marker) {
			marker.on('mouseover', function (e) {
                this.openPopup();
            });
            marker.on('mouseout', function (e) {
                this.closePopup();
            });
		},
		loadVehicle: function($vehicle_id) {
			var self = this;
			pjQ.$.ajax({
                url: self.opts.folder + 'index.php?controller=pjFrontTracking&action=getVehicleFromAPI&vehicle_id=' + $vehicle_id, 
                type: 'GET',
                dataType: 'json',
                success: function(vehicle) {
                	if (pjQ.$('#map-tracking').length > 0) {
	                	// Xóa tất cả marker cũ
	                    vehicleMarkers.clearLayers(); 
	
	                    //var position = vehicle.logLast.lonlat;
	                    const position = vehicle.logLast?.lonlat;
	                    // Đảm bảo có tọa độ để vẽ
	                    if (position && position[0] && position[1]) {
	                        var lat = position[1];
	                        var lng = position[0];
	                        var currentSpeed = vehicle.logLast?.speed;
	                        var isMoving = vehicle.logLast.isMoving !== undefined ? parseInt(vehicle.logLast.isMoving, 10) : 0;
	                        var selectedIcon;
	                        var tooltipClassName;
	                        var vehicleId = vehicle._id;
	                        
	                        if (isMoving == 1 || parseInt(currentSpeed, 10) > 0) {
	                            selectedIcon = MovingIcon;
	                            tooltipClassName = 'vehicle-label-moving';
	                            pjQ.$('.pjTripInfo').removeClass('text-warning');
	                            pjQ.$('.pjTripInfo').addClass('text-success');
	                            pjQ.$('.pjVehicleSpeed').html(`<strong>${self.opts.labels.label_speed}: ${currentSpeed} km/h</strong>`);
	                        } else {
	                            selectedIcon = IdleIcon;
	                            tooltipClassName = 'vehicle-label';
	                            pjQ.$('.pjTripInfo').removeClass('text-success');
	                            pjQ.$('.pjTripInfo').addClass('text-warning');
	                            pjQ.$('.pjVehicleSpeed').html('');
	                        }
	                        var popupContent = `
	                            <b>${vehicle.name || 'N/A'}</b><br>
	                            Tốc độ: ${currentSpeed} km/h<br>
	                            Cập nhật: ${new Date(position.timestamp * 1000).toLocaleTimeString()}
	                        `;
	                        
	                        var marker = L.marker([lat, lng], {
	                            icon: selectedIcon // Dùng icon đã định nghĩa
	                        })/*.bindPopup(popupContent, { 
	                            closeButton: false, 
	                            autoClose: false 
	                        })*/.bindTooltip(vehicle.name, {
	                        	permanent: true,
	                            direction: 'top',   // <--- ĐÃ THAY ĐỔI TẠI ĐÂY
	                            offset: [0, -25],   // Điều chỉnh vị trí (0, -25) để nhãn cao hơn icon
	                            className: tooltipClassName
	                        });
	                        
	                        self.bindHoverPopup.call(self, marker);
	                        
	                        // 🔑 LƯU TRỮ MARKER VÀ ID
	                        vehicleMarkersMap[vehicleId] = marker;
	                        marker.vehicleId = vehicleId;
	                        
	                        vehicleMarkers.addLayer(marker);
	                    }
	                    
	                    // --- LOGIC TRACKING REALTIME ---
	                    if ($currentlyTrackingId) {
	                        const trackedMarker = vehicleMarkersMap[$currentlyTrackingId];
	                        if (trackedMarker) {
	                            const newLatlng = trackedMarker.getLatLng();
	                            
	                            // Sử dụng panTo để di chuyển bản đồ đến vị trí mới MƯỢT MÀ
	                            map.panTo(newLatlng, { animate: true, duration: 1 }); 
	                            
	                            // Cập nhật lại highlight trên danh sách (đề phòng)
	                            const trackingItem = document.querySelector(`.vehicle-item[data-vehicle-id="${$currentlyTrackingId}"]`);
	                            if (trackingItem) {
	                                document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));
	                                trackingItem.classList.add('is-tracking');
	                            }
	                        } else {
	                            // Nếu xe đang tracking không còn dữ liệu (mất kết nối), dừng tracking
	                            $currentlyTrackingId = null;
	                            document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));
	                        }
	                    } else if (vehicleMarkers.getLayers().length > 0) {
	                         // Nếu KHÔNG có xe nào đang được tracking, fitbounds để bao quát tất cả
	                    	if (map !== null) {
	                         map.invalidateSize(); 
		                         map.fitBounds(vehicleMarkers.getBounds(), { 
		                             padding: [50, 50, 50, 380] // Đã sửa padding
		                         }); 
	                    	}
	                    }
                	}
                },
                error: function(xhr, status, error) {
                    console.error("Lỗi tải dữ liệu phương tiện: " + error);
                }
            });
        	
        	// TỰ ĐỘNG CẬP NHẬT (LIVE TRACKING): Cứ sau 15 giây sẽ tải lại dữ liệu
        	$filterTimer = setTimeout(function() {
        		self.loadVehicle.call(self, $vehicle_id);
            }, $delayTime);
		}
	};
	
	window.pjTracking = pjTracking;	
})(window);