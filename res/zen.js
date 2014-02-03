function addAttachField (containerName) {
	var elAttachmentsContainer = document.getElementById (containerName);
	var elAttachmentContainer  = document.createElement ('div');

	var elDeleteButton = document.createElement ('button');
	var elDeleteSpan   = document.createElement ('span');
	var elFileField    = document.createElement ('input');

	// elDeleteButton.type      = 'button'; error in IE
	elDeleteButton.className = 'minibutton';
	elDeleteButton.onclick = function () {
		deleteAttachField (this);
	}
	elDeleteSpan.innerText   = 'delete';
	elDeleteButton.appendChild (elDeleteSpan);

	elFileField.name = 'upload[]';
	elFileField.type = 'file';
	elFileField.size = '66';
	elFileField.style.padding = '4px';

	elAttachmentContainer.appendChild (elDeleteButton);
	elAttachmentContainer.appendChild (elFileField);

	elAttachmentsContainer.appendChild (elAttachmentContainer);
	elAttachmentsContainer.style.display = '';
}

function deleteAttachField (baseElement) {
	var elAttachmentContainer  = baseElement.parentNode;
	var elAttachmentsContainer = baseElement.parentNode.parentNode;

	elAttachmentsContainer.removeChild (elAttachmentContainer);
	if (elAttachmentsContainer.children.length == 0) {
		elAttachmentsContainer.style.display = 'none';
	}
}
