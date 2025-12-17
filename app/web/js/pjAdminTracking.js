var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			validate = ($.fn.validate !== undefined);
		
		$(document).ready(function() {
			let filterTimer = null; 
			const DELAY_TIME = 10000;
			const DEFAULT_ZOOM_LEVEL = 13;
			const ZOOM_LEVEL_HIGHLIGHT = 15; // Hoặc một mức zoom cố định bạn mong muốn
			const ANIMATION_DURATION = 0.5; // Thời gian bay (giây)
			const SHOW_TOOLTIP_ON_HOVER = false;
			let currentlyTrackingId = null;
			var map = L.map('map', {
	            zoomControl: false // <--- QUAN TRỌNG: Ngăn thêm nút mặc định ở topleft
	        }).setView([47.2576489, 11.3513075], DEFAULT_ZOOM_LEVEL);
			// Lấy ngôn ngữ ưu tiên của trình duyệt (ví dụ: 'en-US', 'vi-VN')
	        const clientLanguage = navigator.language || navigator.userLanguage || 'en';
	        
	        // Chỉ lấy mã ngôn ngữ cơ bản (ví dụ: 'en', 'vi', 'de')
	        // Dùng slice(0, 2) để cắt lấy 2 ký tự đầu tiên
	        const languageCode = clientLanguage.slice(0, 2).toLowerCase(); 
	        
	        const langParam = `&hl=${languageCode}`;
			// --- 1. ĐỊNH NGHĨA CÁC LỚP BẢN ĐỒ (TILE LAYERS) ---

	        // A. Roadmap (Mặc định)
	        var roadmap = L.tileLayer('http://{s}.google.com/vt/lyrs=m'+langParam+'&x={x}&y={y}&z={z}',{
	            maxZoom: 20,
	            subdomains:['mt0','mt1','mt2','mt3'],
	            attribution: 'Map data &copy; Google'
	        }).addTo(map); // Thêm Roadmap làm lớp mặc định

	        // B. Satellite
	        var satellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
	            maxZoom: 20,
	            subdomains:['mt0','mt1','mt2','mt3']
	        });

	        // C. Hybrid (Kết hợp Roadmap và Satellite)
	        var hybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',{
	            maxZoom: 20,
	            subdomains:['mt0','mt1','mt2','mt3']
	        });
	        
	        // D. Terrain/Tôp địa hình (Thường dùng lyrs=p hoặc lyrs=t)
	        var terrain = L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{
	            maxZoom: 20,
	            subdomains:['mt0','mt1','mt2','mt3']
	        });
	        
	        L.control.zoom({
	            position: 'topright' // Đặt nút thu phóng ở vị trí mong muốn
	        }).addTo(map);
	        
	     // --- 2. THÊM CÔNG CỤ ĐIỀU KHIỂN CHỌN LAYER ---
	        var baseLayers = {
	            "Roadmap": roadmap,
	            "Satellite": satellite,
	            "Hybrid": hybrid,
	            "Terrain": terrain
	        };
	        
	        L.control.layers(baseLayers, null, { collapsed: true, position: 'bottomright'}).addTo(map);
	        
	        
	        var vehicleMarkersMap = {};
	        var vehicleMarkers = L.featureGroup().addTo(map); // Nhóm chứa tất cả các marker
	        var allVehiclesData = []; // Lưu trữ toàn bộ dữ liệu xe

	        var IdleIcon = L.divIcon({
	        	className: 'custom-vehicle-icon',
	            html: '<i class="fa fa-car"></i>', 
	            iconSize: [34, 34], // Điều chỉnh kích thước lớn hơn một chút để chứa nền
	            iconAnchor: [17, 34], // Căn giữa
	            popupAnchor: [0, -34]
	        });
	        
	        var MovingIcon = L.divIcon({
	            className: 'moving-vehicle-icon', // Sử dụng CSS mới (màu xanh lá)
	            html: '<i class="fa fa-car"></i>', 
	            iconSize: [34, 34], 
	            iconAnchor: [17, 34], 
	            popupAnchor: [0, -34] 
	        });
	        
	     // HÀM XỬ LÝ HIGHLIGHT MARKER TRÊN BẢN ĐỒ
	        function highlightMarker(vehicleId, highlight) {
	            var marker = vehicleMarkersMap[vehicleId];
	            var mapElement = document.getElementById('map');
	            if (marker && marker._icon) {
	                if (highlight) {
	                	if (SHOW_TOOLTIP_ON_HOVER) {
		                	mapElement.classList.add('hide-all-tooltips');
		                	const latLng = marker.getLatLng();
		                    
		                    // Sử dụng flyTo để di chuyển mượt mà đến vị trí Marker
		                    map.flyTo(latLng, ZOOM_LEVEL_HIGHLIGHT, {
		                         duration: ANIMATION_DURATION,
		                         easeLinearity: 0.5 // Tốc độ hoạt hình
		                    });
	                	}
	                    // Thêm class highlight
	                    L.DomUtil.addClass(marker._icon, 'highlight-marker');
	                    marker.openPopup(); // Mở popup khi highlight (như hover)
	                } else {
	                	if (SHOW_TOOLTIP_ON_HOVER) {
		                	mapElement.classList.remove('hide-all-tooltips');
		                	map.invalidateSize();
	                        map.fitBounds(vehicleMarkers.getBounds(), { 
	                            padding: [50, 50, 50, 380] // Đã sửa padding
	                        });
	                	}
	                    // Xóa class highlight
	                    L.DomUtil.removeClass(marker._icon, 'highlight-marker');
	                    marker.closePopup(); // Đóng popup khi hết highlight
	                }
	            }
	        }

	        // HÀM XỬ LÝ TRACKING XE TRÊN BẢN ĐỒ
	        function trackVehicle(vehicleId) {
	        	var marker = vehicleMarkersMap[vehicleId];
	            
	            // 1. Nếu đang tracking chính chiếc xe này, hãy TẮT tracking
	            if (currentlyTrackingId === vehicleId) {
	                currentlyTrackingId = null; 
	                //console.log(`Stop tracking vehicle: ${vehicleId}`);
	                return false; // Trả về false để biết đã tắt
	            } 
	            
	            // 2. Nếu đang tracking xe khác hoặc chưa tracking, hãy BẬT tracking xe mới
	            currentlyTrackingId = vehicleId; 
	            //console.log(`Start tracking vehicle: ${vehicleId}`);

	            if (marker) {
	                // Lần đầu tiên, dùng flyTo để di chuyển mượt mà và zoom vào
	                var newZoom = map.getZoom() < 15 ? 15 : map.getZoom(); 
	                map.flyTo(marker.getLatLng(), newZoom, { duration: 1.5 });
	            }
	            return true; // Trả về true để biết đã bật
	        }

	        // HÀM TẠO VÀ GẮN SỰ KIỆN CHO DANH SÁCH XE
	        function renderVehicleList(vehicles) {
	            var listContainer = document.getElementById('vehicle-list');
	            listContainer.innerHTML = ''; // Xóa danh sách cũ
	            
	            vehicles.forEach(vehicle => {
	                // Giả định vehicle có thuộc tính vehicle_id và address (để hiển thị)
	                var vehicleId = vehicle._id; 
	                var currentSpeed = vehicle.logLast?.speed;
	                var isMoving = vehicle.logLast?.isMoving;
	                var date = vehicle.logLast?.date;
                    var formattedDate = formatDateDDMM(date);
	                var listItem = document.createElement('div');
	                listItem.className = 'vehicle-item';
	                listItem.setAttribute('data-vehicle-id', vehicleId);
	                
	                // Nội dung danh sách
	                if (isMoving == 1) {
	                	listItem.innerHTML = `
		                    <div class="vehicle-icon-list">
		                        <i class="fa fa-car"></i>
		                        <span class="status-dot moving"></span>
		                    </div>
		                    <div class="vehicle-details">
		                        <div class="name"><a href="javascript:void(0);" title="${myLabel.click_to_toggle}">${vehicle.name || 'N/A'}</a></div>
		                        <div class="speed">${currentSpeed} km/h</div>
		                    </div>
		                `;
                    } else {
                    	listItem.innerHTML = `
		                    <div class="vehicle-icon-list">
		                        <i class="fa fa-car"></i>
		                        <span class="status-dot"></span>
		                    </div>
		                    <div class="vehicle-details">
		                        <div class="name"><a href="javascript:void(0);" title="${myLabel.click_to_toggle}">${vehicle.name || 'N/A'}</a></div>
		                        <div class="date">${formattedDate}</div>
		                    </div>
		                `;
                    }

	                // 1. SỰ KIỆN HOVER (HIGHLIGHT)
	                listItem.addEventListener('mouseover', function() {
	                    highlightMarker(vehicleId, true);
	                });
	                listItem.addEventListener('mouseout', function() {
	                    highlightMarker(vehicleId, false);
	                });

	                // 2. SỰ KIỆN CLICK (TRACKING)
	                listItem.addEventListener('click', function() {
	                    var trackingStatus = trackVehicle(vehicleId);
	                    
	                    // Xóa class 'is-tracking' khỏi tất cả các mục khác
	                    document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));

	                    // Nếu tracking được bật, thêm class 'is-tracking' vào mục hiện tại
	                    if (trackingStatus) {
	                        listItem.classList.add('is-tracking');
	                    } else {
	                    	map.invalidateSize(); 
	                    	map.fitBounds(vehicleMarkers.getBounds(), { 
	                            padding: [50, 50, 50, 380] // Đã sửa padding
	                        });
	                        listItem.classList.remove('is-tracking');
	                    }
	                });
	                
	                listContainer.appendChild(listItem);
	            });
	        }
	        
	        function formatDateDDMM(isoString) {
	            if (!isoString) return 'N/A';
	            
	            // 1. Tạo đối tượng Date từ chuỗi ISO
	            const date = new Date(isoString);
	            
	            // Kiểm tra xem đối tượng Date có hợp lệ không
	            if (isNaN(date)) return 'Invalid Date';

	            // 2. Lấy ngày và tháng
	            // date.getDate() trả về ngày (1-31)
	            const day = date.getDate(); 
	            // date.getMonth() trả về tháng (0-11), nên cần +1
	            const month = date.getMonth() + 1; 

	            // 3. Định dạng thêm số 0 ở đầu (Padding)
	            // Ví dụ: 7 -> "07", 12 -> "12"
	            const formattedDay = String(day).padStart(2, '0');
	            const formattedMonth = String(month).padStart(2, '0');

	            // 4. Trả về định dạng DD.MM
	            return `${formattedDay}.${formattedMonth}`;
	        }
	        
	        // Hàm Tải dữ liệu và Cập nhật bản đồ
	        function loadVehicles($showLoader) {
	            var $form = $('.frm-filter'),
	            $q = $form.find('input[name="q"]').val(),
	            $status = $form.find('select[name="status"]').val();
	            if ($showLoader == 1) {
	            	$('.pj-loader').show();
	            }
	        	$.ajax({
	                url: 'index.php?controller=pjAdminTracking&action=pjActionGetVehicles&q=' + $q + '&status=' + $status, 
	                type: 'GET',
	                dataType: 'json',
	                success: function(vehicles) {
	                	if ($showLoader == 1) {
	    	            	$('.pj-loader').hide();
	    	            }
	                    // LƯU Ý: KIỂM TRA CẤU TRÚC JSON
	                    // Nếu API trả về mảng trực tiếp, dùng: var vehiclesData = vehicles;
	                    // Nếu API trả về {data: [...]} hoặc {assets: [...]}, hãy sửa lại: 
	                    var vehiclesData = vehicles; 
	                    allVehiclesData = vehiclesData; // Lưu trữ để lọc

	                    // Xóa tất cả marker cũ
	                    vehicleMarkers.clearLayers(); 

	                    vehiclesData.forEach(vehicle => {
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
	                            if (isMoving == 1) {
	                                selectedIcon = MovingIcon;
	                                tooltipClassName = 'vehicle-label-moving';
	                            } else {
	                                selectedIcon = IdleIcon;
	                                tooltipClassName = 'vehicle-label';
	                            }
	                            var popupContent = `
	                                <b>${vehicle.name || 'N/A'}</b><br>
	                                ${myLabel.make}: ${vehicle.child.make}<br>
	                                ${myLabel.model}: ${vehicle.child.model}<br>
	                                ${myLabel.license_plate}: ${vehicle.child.licensePlate}
	                            `;
	                            
	                            var marker = L.marker([lat, lng], {
	                                icon: selectedIcon // Dùng icon đã định nghĩa
	                            }).bindTooltip(vehicle.name, {
	                            	permanent: true,
	                                direction: 'top',   // <--- ĐÃ THAY ĐỔI TẠI ĐÂY
	                                offset: [0, -25],   // Điều chỉnh vị trí (0, -25) để nhãn cao hơn icon
	                                className: tooltipClassName
	                            });
	                            if (SHOW_TOOLTIP_ON_HOVER) {
	                            	marker.bindPopup(popupContent, { 
		                                closeButton: false, 
		                                autoClose: false 
		                            });
	                            	bindHoverPopup(marker);
	                            }
	                            
	                            // 🔑 LƯU TRỮ MARKER VÀ ID
	                            vehicleMarkersMap[vehicleId] = marker;
	                            marker.vehicleId = vehicleId;
	                            
	                            vehicleMarkers.addLayer(marker);
	                        }
	                    });

	                    // 🔑 GỌI HÀM TẠO DANH SÁCH MỚI
	                    renderVehicleList(vehiclesData);
	                    
	                 // --- LOGIC TRACKING REALTIME ---
	                    if (currentlyTrackingId) {
	                        const trackedMarker = vehicleMarkersMap[currentlyTrackingId];
	                        if (trackedMarker) {
	                            const newLatlng = trackedMarker.getLatLng();
	                            
	                            // Sử dụng panTo để di chuyển bản đồ đến vị trí mới MƯỢT MÀ
	                            map.panTo(newLatlng, { animate: true, duration: 1 }); 
	                            
	                            // Cập nhật lại highlight trên danh sách (đề phòng)
	                            const trackingItem = document.querySelector(`.vehicle-item[data-vehicle-id="${currentlyTrackingId}"]`);
	                            if (trackingItem) {
	                                document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));
	                                trackingItem.classList.add('is-tracking');
	                            }
	                        } else {
	                            // Nếu xe đang tracking không còn dữ liệu (mất kết nối), dừng tracking
	                            currentlyTrackingId = null;
	                            document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));
	                        }
	                    } else if (vehicleMarkers.getLayers().length > 0) {
	                         // Nếu KHÔNG có xe nào đang được tracking, fitbounds để bao quát tất cả
	                         map.invalidateSize();
	                         map.fitBounds(vehicleMarkers.getBounds(), { 
	                             padding: [50, 50, 50, 380] // Đã sửa padding
	                         }); 
	                    }
	                },
	                error: function(xhr, status, error) {
	                    console.error("Lỗi tải dữ liệu phương tiện: " + error);
	                }
	            });
	            
	            // TỰ ĐỘNG CẬP NHẬT (LIVE TRACKING): Cứ sau 15 giây sẽ tải lại dữ liệu
	        	filterTimer = setTimeout(function() {
	        		loadVehicles(0);
	            }, DELAY_TIME);
	        }
	        
	        // Bắt đầu tải dữ liệu lần đầu
	        loadVehicles(1);

	        function bindHoverPopup(marker) {
	            marker.on('mouseover', function (e) {
	                this.openPopup();
	            });
	            marker.on('mouseout', function (e) {
	                this.closePopup();
	            });
	        }
	        
	        $(document).on("keydown", "#specific-search-box", function (e) {
	        	if (e.which === 13) {
	        		e.preventDefault();
	        		if (filterTimer !== null) {
	        	        clearTimeout(filterTimer);
	        	    }
	        		loadVehicles(1);
	        	}
			}).on("change", "#specific-filter-box", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (filterTimer !== null) {
        	        clearTimeout(filterTimer);
        	    }
				loadVehicles(1);
			});
	        
		});
	});
})(jQuery);