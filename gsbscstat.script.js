jQuery(document).ready(function($) {
	var cur_path = window.location.href;
	jQuery.ajax({
	  type : "post",
	  context: this,
	  dataType : "html",
	  url : gsbscstatajx.ajaxurl,
	  data : {action: "gsbscstat_add",path:cur_path, checkReq:gsbscstatajx.checkReq},
	  success: function(response) {		 
		},
		complete : function(){
		}
	});///
});