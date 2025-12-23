<?php
mt_srand();
$index = mt_rand(1, 9999);
$get = $controller->_get->raw();
?>
<div id="pjWrapperTracking">
	<div id="pjTrackingContainer_<?php echo $index; ?>" class="pjTrackingContainer"></div>
</div>
<script type="text/javascript">
var pjQ = pjQ || {},
	pjTracking_<?php echo $index; ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	isMSIE = function() {
		var ua = window.navigator.userAgent,
        	msie = ua.indexOf("MSIE ");

        if (msie !== -1) {
            return true;
        }

		return false;
	},
	
	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		index: <?php echo $index; ?>,
		hide: <?php echo isset($get['hide']) && (int) $get['hide'] === 1 ? 1 : 0; ?>,
		locale: <?php echo isset($get['locale']) && (int) $get['locale'] > 0 ? (int) $get['locale'] : $controller->pjActionGetLocale(); ?>,
		hash: "<?php echo isset($get['hash']) ? $get['hash'] : ''; ?>",
		labels: {
			label_speed: "<?php __('front_scheduled_speed'); ?>"
		}
	};
	if (isSafari) {
		createSessionId();
		options.session_id = getSessionId();
	}else{
		options.session_id = "";
	}
	<?php
	$dm = new pjDependencyManager(null, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
		window.pjQ.$.browser = {
			msie: isMSIE()
		};
		loadScript("https://unpkg.com/leaflet@1.9.4/dist/leaflet.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjFrontTracking.js", function () {
				pjTracking_<?php echo $index; ?> = new pjTracking(options);
			});
		});
	});
})();
</script>