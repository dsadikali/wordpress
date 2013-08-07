
function onAddTypeChange(obj) {
	document.getElementById('srcURL_addnew').value = "";
	if (obj.value == 'SWF' || obj.value == 'ADS'){
		document.getElementById("addLinkToURLDesc").style.display = "none";
		document.getElementById("addLinkToURText").style.display = "none";
		document.getElementById("addLinkToTargetDesc").style.display = "none";
		document.getElementById("addLinkToTargetText").style.display = "none";
		document.getElementById("shoeMediaLibraryList").style.display = "none";
		document.getElementById("imagePreviewWindow").style.display = "none";
		document.getElementById("shoeMediaLibraryListOnlySWF").style.display = "block";
		if (obj.value == 'ADS'){
			document.getElementById("shoeMediaLibraryList").style.display = "none";
			document.getElementById("shoeMediaLibraryListOnlySWF").style.display = "none";
		}
	}else if (obj.value == 'IMG'){
		document.getElementById("addLinkToURLDesc").style.display = "block";
		document.getElementById("addLinkToURText").style.display = "block";	
		document.getElementById("addLinkToTargetDesc").style.display = "block";
		document.getElementById("addLinkToTargetText").style.display = "block";
		document.getElementById("shoeMediaLibraryList").style.display = "block";
		document.getElementById("imagePreviewWindow").style.display = "block";
		document.getElementById("shoeMediaLibraryListOnlySWF").style.display = "none";
		
	}
}
function deleteSubmit(obj) {
	alert(obj.value);
}