(function($) {

	$.fn.pageslider = function(options){

		// default configuration properties
		var defaults = {
			prevId: 		'prevBtn',
			prevText: 		'Previous',
			nextId: 		'nextBtn',
			nextText: 		'Next',
			controlsShow:	true,
			controlsBefore:	'',
			controlsAfter:	'',
			controlsFade:	true,
			firstId: 		'firstBtn',
			firstText: 		'First',
			firstShow:		false,
			lastId: 		'lastBtn',
			lastText: 		'Last',
			lastShow:		false,
			vertical:		false,
			auto:			false,
			pause:			2000,
			nb_pages:     0,
			images_uri:	  {},
			continuous:		false
		};

		var options = $.extend(defaults, options);

		this.each(function() {
			var obj = $(this);
			var s = options.nb_pages;
			var ts = s-1;
			var t = 0;

			if(options.controlsShow) {
			  var html = '<div id="pageslider-wrapper"></div>';
			  $(obj).wrap(html);
			  $('#pageslider-wrapper').css('width', $(obj).width());
			  $('#pageslider-wrapper').css('height', $(obj).height());

				var html = options.controlsBefore;
				if(options.firstShow) html += '<span id="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
				html += ' <span id="'+ options.prevId +'"><a href=\"javascript:void(0);\">'+ options.prevText +'</a></span>';
				html += ' <span id="'+ options.nextId +'"><a href=\"javascript:void(0);\">'+ options.nextText +'</a></span>';
				if(options.lastShow) html += ' <span id="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';
				html += options.controlsAfter;
				$(obj).after(html);
			};

			$("a","#"+options.nextId).click(function(){
				animate("next",true);
				return false;
			});
			$("a","#"+options.prevId).click(function(){
				animate("prev",true);
				return false;
			});
			$("a","#"+options.firstId).click(function(){
				animate("first",true);
				return false;
			});
			$("a","#"+options.lastId).click(function(){
				animate("last",true);
				return false;
			});

			function animate(dir,clicked){
				var ot = t;
				switch(dir){
					case "next":
						t = (ot>=ts) ? (options.continuous ? 0 : ts) : t+1;
						break;
					case "prev":
						t = (t<=0) ? (options.continuous ? ts : 0) : t-1;
						break;
					case "first":
						t = 0;
						break;
					case "last":
						t = ts;
						break;
					default:
						break;
				};

				var diff = Math.abs(ot-t);
				var speed = diff*options.speed;

				obj.attr('src', options.images_uri[t]);

				if(!options.continuous && options.controlsFade){
					if(t==ts){
						$("a","#"+options.nextId).hide();
						$("a","#"+options.lastId).hide();
					} else {
						$("a","#"+options.nextId).show();
						$("a","#"+options.lastId).show();
					};
					if(t==0){
						$("a","#"+options.prevId).hide();
						$("a","#"+options.firstId).hide();
					} else {
						$("a","#"+options.prevId).show();
						$("a","#"+options.firstId).show();
					};
				};

				if(clicked) clearTimeout(timeout);
				if(options.auto && dir=="next" && !clicked){;
					timeout = setTimeout(function(){
						animate("next",false);
					},diff*options.speed+options.pause);
				};
			};
			// init
			var timeout;
			if(options.auto){;
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);
			};

			if(!options.continuous && options.controlsFade){
				$("a","#"+options.prevId).hide();
				$("a","#"+options.firstId).hide();
			};
		});
	};
})(jQuery);