(function() {
	tinymce.create('tinymce.plugins.CsGWOPlugin', {

		init : function(ed, url) {
		
			//********* Insert Section Button ****
			ed.addCommand('mceCsGWOSection', function() {
				ed.windowManager.open({
					file : url + '/section.htm',
					width : 260 ,
					height : 220,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
				});
			});
			ed.addButton('CsGWOSection', {
				title : 'Insert GWO section',
				cmd : 'mceCsGWOSection',
				image : url + '/img/section.gif'
			});
			//********** End Button ***********
			//********** Insert Conversion Link ***********
			ed.addCommand('mceCsGWOAddLink', function() {
				var ed = tinyMCE.activeEditor, s = ed.selection;
				var dom =tinyMCE.activeEditor.dom;
				if (s.isCollapsed()){
					var link=s.getNode();					
					if(link.nodeName=='A'){
						var attrib=dom.getAttrib(link,'onclick');
						if(!attrib){
							dom.setAttrib(link,'onclick','return ConversionCount(this);');
						}
					}
				}
			});
			//********** Insert Conversion Link ***********
			ed.addCommand('mceCsGWORemoveLink', function() {
				var ed = tinyMCE.activeEditor, s = ed.selection;
				var dom =tinyMCE.activeEditor.dom;
				if (s.isCollapsed()){
					var link=s.getNode();					
					if(link.nodeName=='A'){
						var attrib=dom.getAttrib(link,'onclick');
						if(attrib){
							dom.setAttrib(link,'onclick',null);
						}
					}
				}
			});
			ed.addButton('CsGWOAddConversionLink', {
				title : 'Insert GWO Conversion Link',
				cmd : 'mceCsGWOAddLink',
				image : url + '/img/conversionlink.gif'
			});
			ed.addButton('CsGWORemoveConversionLink', {
				title : 'Remove GWO Conversion Link',
				cmd : 'mceCsGWORemoveLink',
				image : url + '/img/removeconversionlink.gif'
			});

			ed.onNodeChange.add(function(ed, cm, n) {					
				if(n.nodeName=='A'){
					
					var attrib=ed.dom.getAttrib(n,'onclick');
					if(attrib){
						cm.setDisabled('CsGWOAddConversionLink',true);
						cm.setDisabled('CsGWORemoveConversionLink', false);
					}else{
						cm.setDisabled('CsGWOAddConversionLink',false);
						cm.setDisabled('CsGWORemoveConversionLink', true);
					}
				}
				else{
					cm.setDisabled('CsGWOAddConversionLink', true);
					cm.setDisabled('CsGWORemoveConversionLink', true);
				}
			});
			
			//********** End Link ***************
		},

		createControl : function(n, cm) {
			return null;
		},

		
		getInfo : function() {
			return {
				longname : 'GWO4WP Plugin',
				author   :  'Andreas',
				authorurl : 'http://andreasnurbo.com',
				infourl : 'http://andreasnurbo.com',
				version : "10.12.2"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('GWO4WP', tinymce.plugins.CsGWOPlugin);
})();