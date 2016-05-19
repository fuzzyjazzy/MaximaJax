//----------------------------------------------------------------
//-- MaximaJax.js
//--
//-- Copyright (c) 2016 Junichi Fujimori (fuzzy_jazzy@Wacooky.com)
//-- Released under the MIT license
//-- http://opensource.org/licenses/mit-license.php
//
//-- MaximaJax requires jQuery and MathJax.
//--
//-- Ver. 2.0 2016-05-19 change MaximaJax element div --> script
//-- Ver. 1.0 2016-03-09

//-- query is undefined or string key=value&key=value ...
var $script = $(document.currentScript);
var base_url = $script.attr("src"); //-- url to this script
var query = base_url.split('?')[1]; //-- query part
//-- Now base_url is parent url of this script
base_url = base_url.substring( 0, base_url.lastIndexOf( "/" ) + 1);

var php = 'maximajax.php'; //-- server to which this script access
var ServerAddress = base_url + php;
//-- automatically run
$(function(){
	MaximaJax.prepare();
});

var MaximaJax = function() {
};

//-- Call this when document is ready.
MaximaJax.prepare = function() {
	var style = '<link rel="stylesheet" type="text/css" href="' +
							base_url  + 'maximajax.css">';
	$script.after(style);

//------------------------------------------------------
//-- Deprecated
	$(".MaximaJax").each( function() {
		var $this = $(this);
		var name = $this.attr("name");
		if (name == null)
			name = '';
		$this.prepend('<span onclick="MaximaJax.on_label_click(this);" class="maxima-label">MAXIMA ' + name + '</span>');
	});
	$(".MaximaJax").append('<input type="button" value="Exec" onclick="MaximaJax.show_result(this)">');
	$(".MaximaJax").append('<div class="maxima-result"></div>');

	$(".MaximaJax pre").each( function(){
		var $this = $(this);
		if (!$this.hasClass('no-hide'))
			$this.hide();
	});
	$(".MaximaJax input").css("visibility", "hidden");
	$(".MaximaJax div.maxima-result").hide();
	//-- Deprecated
	//------------------------------------------------------

	$('script').each( function() {
		var $this = $(this);
		var type = $this.attr('type');
		if (typeof type == 'string') {
			var types = type.split(/\s*;\s*/);
			if (types[0] == 'math/maxima') {
				var props = {};
				for (var i = 1; i < types.length; i++) {
					var params = types[i].split('=');
					props[params[0]] = (params[1] == null) ? '' : params[1];
				}

				if (!('name' in props))
					props['name'] = '';
				if (!('no-hide' in props))
					props['no-hide'] = false;
				else
					props['no-hide'] = true;

				var html = $this.html().replace(/(\r\n|\n|\r)/,"");
				var $mjx = $('<div class="MaximaJax"></div>');
				var $button = $('<span onclick="MaximaJax.on_label_click(this);" class="maxima-label">MAXIMA ' + props['name'] + '</span>');
				$mjx.prepend($button);
				var pre = '<pre' + (props['no-hide'] ? ' class="no-hide">' : '>');
				$mjx.append(pre + '<code>' + html + '</code></pre>');
				$mjx.append('<input type="button" value="Exec" onclick="MaximaJax.show_result(this)">');
				$mjx.append('<div class="maxima-result"></div>');
				$this.after($mjx);
				MaximaJax.on_label_click($button);
			}
		}
	});

}

MaximaJax.on_label_click = function(element) {
	var $element = $(element);
	if (!$element.siblings('pre').hasClass('no-hide'))
		$element.siblings('pre').toggle();

	$element.siblings('div.maxima-result').toggle();

	if ($element.siblings('div.maxima-result').css('display') == 'none')
		$element.siblings('input').css("visibility", "hidden");
	else
		$element.siblings('input').css("visibility", "visible");
}
//-- read MAXIMA commands with help of button element
MaximaJax.show_result = function(element) {
	var str = $(element).siblings("pre").children("code").text(); //--decode HTML entities
	var	$out = $(element).siblings("div.maxima-result");
	var cmds = str.split("\n");
	for( var i = 0; i < cmds.length; i++ ) {
		if (cmds[i].endsWith("$"))
			cmds[i] += "\r"; //-- lisp may prefer \r to \n
	}

	MaximaJax.exec( ServerAddress, {cmds: cmds, render_tex: true}, function(lines) {
			var html = lines.join("\n");
			$out.html(html);
			MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
	});
}

MaximaJax.exec = function( url, assoc ) {
	var callBack = null;
	if (arguments.length > 2 )
		callBack = arguments[2];
	var json = JSON.stringify(assoc);
	var thisObject = MaximaJax;

	$.post(
		url,
		{ json: json },
		function(data, status) {
			var lines = data.result;

			if (callBack == null ) {
				$.each(lines, function(i, item) {
					console.log( item );
				});
			} else {
				callBack.call(thisObject, lines);
			}
		},
		"json"
	);
}
