var CsGWOConvLink = {
	init: function(){},
	
	insert: function() {

		// Insert the contents from the input into the document
		var target=document.forms[0].target.value;
		if(target){
			target='target="'+target+'" ';
		}
		var title=document.forms[0].title.value;
		if(title){
			title='title="'+document.forms[0].title.value+'" ';
		}
			
		var embedCode = '<a href="'+document.forms[0].url.value+'" '+target+title+'onclick="ConversionLink();">{$selection}</a>';
		alert(embedCode);
		tinyMCEPopup.editor.execCommand('mceReplaceContent', false, embedCode);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CsGWOConvLink.init, CsGWOConvLink);