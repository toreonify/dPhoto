// nuPhoto
// JS with all functions
	
// Path to go up folder
var lib_path = "/";	
var go_back = true;
var active_viewer = -1;

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
		
		console.log(content);

		if ($(parent).attr('data-content') == "Убрать папку из наблюдаемых") {
			$(parent).removeClass('checkmark');
			$(parent).addClass('plus');

			$(parent).attr('data-content', "Добавить папку в наблюдаемые");
			$(".popup.visible").find(".content").html("Добавить папку в наблюдаемые");
		} else {
			$(parent).removeClass('plus');
			$(parent).addClass('checkmark');

			$(parent).attr('data-content', "Убрать папку из наблюдаемых");
			$(".popup.visible").find(".content").html("Убрать папку из наблюдаемых");
		}
	});

	return true;
}
	
function lib_folder_ls(path) {
	
	if (path != null) {
		var content = null;
				
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
			lib_folder_ls(tmp);
			go_back = false;
		}
	}
}

function lib_onover(id) {
	var x = 0, y = 0, w = 0, h = 0;
	
	x = $("#viewer_face_" + id).attr("x") * $("#viewer-img").width();	
	y = $("#viewer_face_" + id).attr("y") * $("#viewer-img").height();	
	w = $("#viewer_face_" + id).attr("w") * $("#viewer-img").width();	
	h = $("#viewer_face_" + id).attr("h") * $("#viewer-img").height();	
	
	$("#viewer_highlight").css("left", x + parseInt($("#viewer-img").css('margin-left').split('px')[0]) + $("#viewer-img").position().left);
	$("#viewer_highlight").css("top", y + $("#viewer-img").position().top);
	$("#viewer_highlight").css("width", w);
	$("#viewer_highlight").css("height", h);
	
	$("#viewer_highlight").css("visibility", "");
}

function lib_onout(id) {
	$("#viewer_highlight").css("visibility", "hidden");
}

function lib_get_file(path, mode, face_id, photo_id) {
	
	if (path != null) {
	
		path = path;
		
		$("#loader").dimmer('show');
		
		$.get("lib-dropbox.php?get_file=" + path, function( data ) {
			content = data;
		
			content = decodeURI(content);
			
			if (!$("#viewer").is(':visible')) {

				$("#loader").dimmer('hide');
				// Fix for a modal to able to be scrolled on first appearance
				//$('body').addClass('scrollable');
			
				$('#viewer').modal('show');
				
			}
		
			
			$("#viewer").find(".image").attr("src", content);
			$("#viewer_link").attr("href", content);
			$("#viewer").find("img").css('height', '400px');
		
			if (mode == 0) {
				lib_remove_hide();
			} else {
				active_viewer = path;
				
				if (active_mode == 2) {
					lib_remove_photo_show();
					$("#remove_from_photo").html('<i class="trash icon"></i>');
					$("#remove_from_photo").unbind("click");
					$("#remove_from_photo").click(function() {lib_remove_photo(face_id, photo_id);});
					
					$("#remove_from_photo").append('Удалить лицо ' + $("#face_list_" + face_id).parent().find("span").text() + ' с фото');
				} else {
					lib_remove_show();
				}
				
				$("#viewer_faces").html('<div class="header">Лица на этой фотографии:</div>');
				
				$.get("lib-db.php?get_photo_faces=" + path, function( data ) {
					var content = decodeURI(data);
	
					content = $.parseJSON(content);
					console.log(content);
					
					$.each(content, function(index, value) {
						$("#viewer_faces").append('<div class="item" id="viewer_face_' + value.id + '" x="' + value.nu_x + '" y="' + value.nu_y + '" w="' + value.nu_w + '" h="' + value.nu_h + '" onmouseover="lib_onover(' + value.id + ');" onmouseout="lib_onout(' + value.id + ');"><img class="ui avatar image" src="data:image/png;base64,' + value.image + '"><div class="content">' + value.name + '</div></div>');	
					});
						
					
				});
				
				//$("#viewer").append('<div class="item"><img class="ui avatar image" src=""><div class="content"><a class="header"></a></div></div>');
				
			}

			if (active_mode == 0) {
				if ($('.ui.card[path="' + active_viewer + '"]').prev().length == 0) {
					lib_hide($("#viewer_prev"));
				} else {
					lib_show($("#viewer_prev"));
				}
				
				if ($('.ui.card[path="' + active_viewer + '"]').next().length == 0) {
					lib_hide($("#viewer_next"));
				} else {
					lib_show($("#viewer_next"));
				}
			} else {			
				if ($("#face_list_" + face_id).find('.ui.card[path="' + active_viewer + '"]').prev().length == 0) {
					lib_hide($("#viewer_prev"));
				} else {
					lib_show($("#viewer_prev"));
				}
				
				if ($("#face_list_" + face_id).find('.ui.card[path="' + active_viewer + '"]').next().length == 0) {
					lib_hide($("#viewer_next"));
				} else {
					lib_show($("#viewer_next"));
				}
			}
		});

		return true;
		
	}
	
	return false;
}