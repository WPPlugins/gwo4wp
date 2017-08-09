var CsGWOSection = {
	init : function() {
	},

	insert : function() {
		// Insert the contents from the input into the document
		var embedCode = '[section \"'+document.forms[0].sectionName.value+'\"]{$selection}[/section]';
		tinyMCEPopup.editor.execCommand('mceReplaceContent', false, embedCode);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CsGWOSection.init, CsGWOSection);