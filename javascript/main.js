// nuPhoto
// UI for user page
	
// Path to go up folder
var lib_path = "/";	

window.onload = function() {
	lib_refresh_popup();

	$('.ui.dropdown').dropdown({
		action: 'hide'
	});
}

function lib_toggle_sidebar() {
	$('.sidebar').sidebar('toggle');
}

function lib_refresh_popup() {
	$(".right.floated.plus.icon").popup('destroy');
	$(".right.floated.checkmark.icon").popup('destroy');
	$(".popup").remove();
	$(".right.floated.plus.icon").popup();
	$(".right.floated.checkmark.icon").popup();
}

function lib_folder_watch(parent, path) {

	$.get("lib-db.php?set_watch=" + path, function( data ) {
		content = data;
		
		content = decodeURI(content);

		if ($(parent).attr('data-content') == "Remove folder from watchlist") {
			$(parent).removeClass('checkmark');
			$(parent).addClass('plus');

			$(parent).attr('data-content', "Add folder to watchlist");
			$(".popup.visible").find(".content").html("Add folder to watchlist");
		} else {
			$(parent).removeClass('plus');
			$(parent).addClass('checkmark');

			$(parent).attr('data-content', "Remove folder from watchlist");
			$(".popup.visible").find(".content").html("Remove folder from watchlist");
		}
	});

	return true;
}
	
function lib_folder_ls(path) {
	
	if (path != null) {
		var content = null;
		
		path = encodeURI(path);
		
		$("#loader").dimmer('toggle');
		
		$.get("templates/user.php?json=" + path, function( data ) {
			content = data;
		
			content = decodeURI(content);
		
			$("#content").html(content);
			
			$("#loader").dimmer('toggle');
			
			if (path != "/") {
				$("#back-button").show();
			} else {
				$("#back-button").hide();
			}
			
			lib_refresh_popup();
		});
		
		lib_path = path;
		
		return true;
	} 
	
	return false;
}

function lib_folder_back() {
	var tmp = lib_path.slice(0, lib_path.lastIndexOf("/"));
	
	tmp.replace(' ', '');
	
	if (tmp == "") {
		lib_folder_ls("/");
	} else {
		lib_folder_ls(decodeURI(tmp));
	}
}

function lib_get_file(path) {
	
	if (path != null) {
	
		path = encodeURI(path);
		
		$("#loader").dimmer('toggle');
		
		$.get("lib-dropbox.php?get_file=" + path, function( data ) {
			content = data;
		
			content = decodeURI(content);

			$("#loader").dimmer('toggle');
			
			// Fix for a modal to able to be scrolled on first appearance
			$('body').addClass('scrollable');
			
			$('#viewer').modal('toggle');
			$("#viewer").find(".image").attr("src", content);
			$("#viewer_link").attr("href", content);
			$("#viewer").find("img").css('height', '400px');
		});

		return true;
		
	}
	
	return false;
}