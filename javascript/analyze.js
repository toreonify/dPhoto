// nuPhoto
// Frontend for analyze.php

var active_watch = -1;

function lib_watch_changed(id) {
	
	var result = false;
	
	$.ajax({
        url: "lib-dropbox.php?watch_changes=" + id,
        type: 'get',
        async: false,
        success: function(data) {
		
			console.log(data);
		
			if (data == "0") {
				result = false;
			} else {
				result = true;
			}
        } 
    });

	return result;
	
}

function lib_select_photo(photo, path) {
	$(photo).dimmer('toggle');
	
	if ($(photo).hasClass("toimport")) {
		$(photo).removeClass("toimport");
	} else {
		$(photo).addClass("toimport");
	}
	
	if ($(".toimport").length === 0) {
		$("#import_button").addClass("disabled");
	} else {
		$("#import_button").removeClass("disabled");
	}
}

function lib_import_photos() {
	var photos_path = [];
		
	$(".toimport").each(function(index, value){
		photos_path.push($(this).attr("path"));
	});
	
	console.log(photos_path);
		
	$.get("analyze.php?set_metadata=" + parseInt(active_watch) + "&set_photos=" + encodeURIComponent($.toJSON(photos_path)), function( data ) {
		$('#back-button').click();
	});
	
}

function lib_show_changes(id) {
	
	$("#back-button").show();
	$("#loader").dimmer('toggle');
	
	active_watch = id;
	
	$.get("analyze.php?get_list=" + id, function( data ) {
		var content = decodeURI(data);

		console.log(content);

		content = $.parseJSON(content);
		
		lib_set_menu_title("Select photos to import");
		
		$("#album_content").html('');
		
		$("#album_content").append('<div class="ui six doubling cards" id="import_content">');
		
		json = content;

		$.each(json, function(index, value) {
			var path = value.path.split('/');
			
			$("#import_content").append('<div class="ui card" path="' + value.path + '"><span class="image"><img src="data:image/png;base64,' + value.thumbnail + '"></img></span><div class="content" style="font-size: 11px;"><a class="header" onclick="lib_select_photo($(this).parent().parent(), \'' + value.path + '\');">' + path[path.length - 1] + '</a><div class="meta"></div></div><div class="ui dimmer" id="selector"><div class="content" onclick="lib_select_photo($(this).parent().parent(), \'' + value.path + '\');"><div class="center"><h2 class="ui inverted icon header"><i class="checkmark icon"></i></h2></div></div></div></div></div>');
			});

			$("#album_content").append('</div>');

			$("#album_content").append('<div class="ui buttons import-buttons"><div class="ui button" onclick="$(\'#back-button\').click();">Cancel</div><div class="or"></div><div class="ui blue button disabled" id="import_button" onclick="lib_import_photos()">Import photos</div></div>');

			$("#loader").dimmer('toggle');

	});

	
}
