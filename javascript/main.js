// nuPhoto
// JS with all functions
	
// Path to go up folder
var lib_path = "/";	
var go_back = true;

window.onload = function() {	
	$('.ui.dropdown').dropdown({
		action: 'hide'
	});
	
	if (typeof lib_load === "function") { 
		lib_load();
	}
}

function lib_toggle_sidebar() {
	$('.sidebar').sidebar('toggle');
}

function lib_refresh_popup() {
	$(".right.floated.plus.icon, .watchlist.delete.icon").popup('destroy');
	$(".right.floated.checkmark.icon").popup('destroy');
	$(".popup").remove();
	$(".right.floated.plus.icon, .watchlist.delete.icon").popup();
	$(".right.floated.checkmark.icon").popup();
}

function lib_get_text(text) {
	
	var result = null;
	
	$.ajax({
        url: "text/templates.php?text=" + text + "&lang=en",
        type: 'get',
        async: false,
        success: function(data) {
            result = data;
        } 
     });
	
	 return result;
}

function lib_folder_watch_query(path, album, callback, id) {
	if (typeof id == "undefined") {
		$.get("lib-db.php?set_watch=" + path + "&album=" + album, callback);
	} else {
		$.get("lib-db.php?set_watch=" + path + "&album=" + album + "&watch_id=" + id, callback);
	} 
}

function lib_folder_watch(parent, path) {

	lib_folder_watch_query(path, active_album, function(data) {
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
	})

	return true;
}
	
function lib_folder_ls(path) {
	
	if (path != null) {
		var content = null;
		
		path = encodeURI(path);
		
		$("#loader").dimmer('toggle');
		
		$.get("templates/user.php?json=" + path + "&album=" + active_album, function( data ) {
			content = data;
		
			content = decodeURI(content);
		
			$("#content, #album_content").html(content);
			
			$("#loader").dimmer('toggle');
			
			if (path != "/") {
				go_back = false;
			}
						
			lib_refresh_popup();
		});
		
		lib_path = path;
		
		return true;
	} 
	
	return false;
}

function lib_folder_back() {
	if (go_back) {
		active_callback(active_album);
		active_callback = null;
		$("#back-button").hide();
	} else {
		var tmp = lib_path.slice(0, lib_path.lastIndexOf("/"));
		
		tmp.replace(' ', '');
		
		if (tmp == "") {
			lib_folder_ls("/");
			go_back = true;
		} else {
			lib_folder_ls(decodeURI(tmp));
			go_back = false;
		}
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