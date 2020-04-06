// nuPhoto
// JS for index.php

var albums_count = 0;
var active_album = -1;
var visible_edit_menu = -1;
var active_rename_menu = -1;
var active_delete_menu = -1;
var active_callback = null;
var active_mode = 0;
var active_merge = -1;
var sort = [];

var album_item_edit_dropdown = ' class="chevron down icon" style="margin-right: 0px; width: 23px;"></i><div class="menu album-edit-dropdown"><div class="item" style="padding: 12px !important;" id="watches">Папки наблюдения</div><div class="item" style="padding: 12px !important;" id="rename">Переименовать</div><div class="item" style="padding: 12px !important;" id="delete">Удалить</div><div class="item" style="text-align: center;background: #fff;cursor: default; visibility: hidden; display: none;" id="sure">Вы уверены?</div><div class="item" style="padding: 12px !important;line-height: 22px;width: 130px;margin: 0px;float: left;text-align: center;background: rgba(0, 128, 0, 0.2); visibility: hidden; display: none;" id="yes">Да</div><div class="item" style="padding: 12px !important;line-height: 22px;width: 130px;margin: 0px;float: left;text-align: center;background: rgba(255, 0, 0, 0.2); visibility: hidden; display: none;" id="cancel">Отмена</div></div></div>';

var album_no_watches = '<div id="content-no-watches"><h2 class="ui icon header"><i class="folder icon"></i><div class="content">Нет папок для наблюдения<div class="sub header">Добавьте новые папки в боковом меню, чтобы импортировать новые фото.</div></div></h2></div>';

var no_albums = '<div id="content-no-album"><h2 class="ui icon header"><i class="photo icon"></i><div class="content">Нет альбомов<div class="sub header">Создайте новый альбом нажав <i class="plus icon" style="font-size: 1em; display: inline;"></i> в боковом меню.</div></div></h2></div>';

function lib_load() {
	document.title = lib_get_text("title_index");
	
	$.get("lib-db.php?get_albums_count", function( data ) {
		var content = decodeURI(data);
		
		albums_count = content;
	});
	
	$.get("lib-db.php?get_albums", function( data ) {
		var content = decodeURI(data);
		
		content = $.parseJSON(content);
		
		$.each(content, function(index, value) {
			if (index == 0) {
				active_album = value.id;
			}
			$("#albums_list").append('<div class="item album_item" id="album_item_' + value.id + '"><div class="ui transparent large input"><div id="album_label_' + value.id + '" class="album_label"  onclick="lib_show_album(this.id.split(\'_\')[2]);">' + value.nu_name + '</div></div>' + '<div class="ui dropdown menu-button-item"><i id="album_edit_icon_' + value.id + '"' + album_item_edit_dropdown + '</div>');

    	}); 
    	
    	lib_show_album(active_album);
		
		lib_set_dropdown($(".menu-button-item"));
		
		lib_get_sort();
	});
	
}

function lib_get_sort() {
	if (active_album != -1) {
	
		$.get("analyze.php?get_sort=" + active_album, function( data ) {
			var content = $.parseJSON(decodeURI(data));
				
			sort = content;
			
			$("#to_date").datepicker();
			$("#from_date").datepicker();
			
			if (sort.nu_sdate != -1) {
				$("#from_date").val(sort.nu_sdate);
			}
			if (sort.nu_edate != -1) {
				$("#to_date").val(sort.nu_edate);
			}
			
			if (sort.nu_sort == 0) {
				$("#sort_icon").addClass("descending");
				$("#sort_icon").removeClass("ascending");
			} else {
				$("#sort_icon").addClass("ascending");
				$("#sort_icon").removeClass("descending");			
			}
			
			//lib_update_sort();
		});
	
	}
}

function lib_show_watches_select() {

	$("#loader").dimmer('toggle');
	
	lib_set_menu_title("Выберите папки из облака");
			
	$.get("templates/user.php?json=/&album=" + active_album, function( data ) {
		var content = decodeURI(data);
	
		$("#album_content").html(content);
		
		$("#loader").dimmer('toggle');
		
		$("#back-button").show();
		
		lib_refresh_popup();	
	});	
}

function lib_watch_restore_callback(data, id) {
	data = encodeURIComponent(data);
	
	console.log(data);
	
	if (data != "") {
		$("#album_watch_" + id).attr("id", "album_watch_" + data);
	}
}

function lib_watch_list_cleanup(id) {
	$("#album_watch_" + id).remove();
	
	$.get("lib-db.php?delete_watch=" + id, function( data ) {
		var content = decodeURI(data);
		console.log(data);	
	});
}

function lib_watch_list_delete(id) {
	var msg = '<div class="ui black message" style="margin: 0px;"><div class="header">Папка наблюдения удалена.</div><p>Вернуть?</p><p><div class="ui inverted blue basic button" onclick="lib_watch_restore(' + id + ');">Да</div><div class="ui inverted basic button" onclick="lib_watch_list_cleanup(' + id + ');">Удалить</div></p></div>';
	
	$("#album_watch_" + id).find('.watchlist.delete.icon').hide();
	$("#album_watch_" + id).find('.header').hide();
	
	lib_folder_watch_query($("#album_watch_" + id).find('.header').text(), active_album, function(data) {lib_watch_restore_callback(data, id)});
	
	$("#album_watch_" + id).find('.content').append(msg);
}

function lib_watch_restore(id) {
	$("#album_watch_" + id).find('.watchlist.delete.icon').show();
	$("#album_watch_" + id).find('.header').show();
	
	$("#album_watch_" + id).find('.black.message').remove();
	
	lib_folder_watch_query($("#album_watch_" + id).find('.header').text(), active_album, function(data){		lib_watch_restore_callback(data, id)}, id);
}

function lib_show_watches_list(list) {
	
	$("#album_content").html('<div class="ui basic segment" style="padding: 0px !important;"><h2 class="ui left floated header"><i class="unhide icon"></i><div class="content">Папки наблюдения</div></h2><h2 class="ui right floated header" style="margin-right: 0px;"><div class="content"><div class="ui blue large basic button" style="margin-right: 0px;" onclick="lib_show_watches_select();">Добавить</div></div></h2></div><div class="ui divided items" id="album_watches">');
	
	var changed = false;
	
	
	$.each(list, function(index, value) {
			
		changed = lib_watch_changed(value.id);
		
		if (changed) {
			$("#album_watches").append('<div class="item" id="album_watch_'+ value.id +'"><div class="middle aligned content"><a class="ui teal ribbon label" onclick="lib_show_changes(' + value.id + ');">Новые фотографии</a><i class="watchlist delete icon" onclick="lib_watch_list_delete(' + value.id + ');" style="float: right; right: 20px; position: absolute;" data-content="Delete watch" data-variation="inverted" data-position="left center"></i><span class="header nu-link" onclick="lib_show_changes(' + value.id + ');"><i class="folder icon"></i>' + value.nu_path.replace("+", " ") + '</span></div></div>');
		} else {			
			$("#album_watches").append('<div class="item" id="album_watch_'+ value.id +'"><div class="middle aligned content"><i class="watchlist delete icon" onclick="lib_watch_list_delete(' + value.id + ');" style="float: right; right: 20px; position: absolute;" data-content="Delete watch" data-variation="inverted" data-position="left center"></i><span class="header nu-link" onclick="lib_show_changes(' + value.id + ');"><i class="folder icon"></i>' + value.nu_path.replace("+", " ") + '</span></div></div>');
		}
			
	});
	
	$("#album_content").append('</div>');
}

function lib_show_watches(album_id) {
	$("#back-button").hide();
	active_album = album_id;
	$("#loader").dimmer('toggle');
	$("#albums_list").sidebar("hide");
		
	$.get("lib-db.php?get_album_watchlist=" + album_id, function( data ) {
		var content = decodeURI(data);
		
		content = $.parseJSON(content);
	
		console.log(content);
		
		lib_show_watches_list(content);
		lib_refresh_popup();
		
		active_callback = lib_show_watches;
		$("#loader").dimmer('toggle');
	});	
	

	lib_set_menu_title();
}

function lib_show_no_watches() {
	$("#back-button").hide();
	$("#album_content").html(album_no_watches);
}

function lib_set_menu_title(text) {
	if (typeof text != "string") {
		$(".title").html($("#album_label_" + active_album).text());
	} else {
		$(".title").html(text);
	}
}

function lib_rename_face(id) {
	var name = $("#face_" + id).find(".content").find("span").text();
	
	$("#face_" + id).find(".content").find("span").replaceWith('<div class="ui transparent input" style="border: 0;"><input type="text" placeholder="Введите имя" onkeyup="lib_pressenter();" onblur="lib_rename_face_finish(' + id + ');"></div>');
	
	$("#face_" + id).find(".content").find("div.input").find('input').val(name);
	$("#face_" + id).find(".content").find("div.input").find('input').focus();
}

function lib_rename_face_finish(id) {
	var name = $("#face_" + id).find("div.input").find('input').val();
	
	$("#face_" + id).find("div.input").replaceWith('<span class="header face-header" onclick="lib_merge_face(' + id + ');">' + name + '</span>');
	
	$.get("lib-db.php?rename_face=" + id + "&name=" + encodeURIComponent(name), function( data ) {
		var content = decodeURI(data);
	
		console.log(content);
	});	
}

function lib_merge_face(id) {
	if (active_merge != -1) {
		console.log("merging" + active_merge + "with " + id);
		$("#merge_" + active_merge).remove();
			
		$.get("analyze.php?merge=" + id + "&merge_with=" + active_merge, function( data ) {
			var content = decodeURI(data);
		
			console.log(content);
			lib_show_faces();
			active_merge = -1;
		});
		
	}
}

function lib_cancel_merge(message) {
	active_merge = -1;
	
	$(message).parent().remove();
	$('.right.floated.compact.small.green.basic.ui.button:contains("Объединить")').show()
}

function lib_merge_start(id) {
	active_merge = id;
	var name = $("#face_" + id).find("span").text();
	$('.right.floated.compact.small.green.basic.ui.button:contains("Объеденить")').hide()
	
	$('<div class="ui message" id="merge_' + id + '"><div class="header">Объеденение</div><p>Нажмите на имя человека, которого вы хотите добавить к ' + name + '.</p><div class="ui black basic button" onclick="lib_cancel_merge(this);">Отмена</div></div>').insertAfter('#face_' + id);
}

function lib_remove_photo(face_id, photo_id) {
	$("#face_list_" + face_id).find(".ui.card[path='" + active_viewer + "']").remove();
	console.log("delete " + active_viewer);
	
	$('<div class="ui info message removed"><div class="header">Фотография успешно удалена</div><ul class="list"><li>Вы можете вернуть отметки лиц только импортировав её вновь.</li></ul></div>').insertAfter(".ui.secondary.menu");
    
	setTimeout(function() {$(".ui.message.removed").remove();}, 3000);
	
	$.get("lib-db.php?delete_face=" + photo_id, function( data ) {
		var content = decodeURI(data);
		console.log(content);
		
		$('#viewer').modal('hide');
	});
}

function lib_show_faces() {
	
	$("#back-button").hide();
	$("#album_content").html('');
	
	$("#loader").dimmer('toggle');
	active_mode = 2;
	
	$.get("lib-db.php?get_album_faces=" + active_album, function( data ) {
		var content = decodeURI(data);
	
		content = $.parseJSON(content);
		console.log(content);
	
		$("#album_content").html('<div class="ui secondary menu"><a class="item" onclick="lib_change_mode(0);"><i id="sort_icon" class="sort content descending icon"></i>Фотографии</a><a class="active item" onclick="lib_change_mode(2);">Лица</a></div>');
		$("#album_content").append('<div class="ui list" id="faces_list">');
				
		lib_update_sort();
				
		$.each(content, function(index, value) {
			
			$("#faces_list").append('<div class="item" id="face_' + index + '"><div class="right floated compact small blue basic ui button" onclick="lib_rename_face(' + index + ');">Переименовать</div><div class="right floated compact small green basic ui button" onclick="lib_merge_start(' + index + ');">Объеденить</div><img class="ui tiny image" style="border-radius: 100px;" src="data:image/png;base64,' + value.image + '"><div class="content"><span class="header face-header" onclick="lib_merge_face(' + index + ');" >' + value.name + '</span><div id="face_list_' + index + '" class="ui cards" style="margin-top: 10px;"></div>');
			
			$.each(value.photos, function(ind, value_photo) {
				value_photo.path = value_photo.path;
				
				$("#face_list_" + index).append('<div class="ui card face-card" style="margin-top: 10px; margin-bottom: 10px; width: 150px;" path="' + value_photo.path + '"><a class="image" onclick="lib_get_file(\'' + value_photo.path + '\', 1, ' + index + ', ' + value_photo.id + ');"><img id="face_list_photo_' + value_photo.id +'" src="images/image.png"></a></div></div></a></div>');
				
				$.get("analyze.php?get_thumbnail=" + value_photo.path, function( data ) {
					$('#face_list_photo_' + value_photo.id).attr("src", "data:image/png;base64," + data);	
				});
			});
			
			
		});	
		

		$("#album_content").append('</div>');
		$("#loader").dimmer('toggle');

	});	
}

function lib_change_mode(mode) {

		switch (mode) {
			case 0:
				$(".ui.secondary.menu").find("a:contains('All')").addClass("active");
				$(".ui.secondary.menu").find("a:contains('Filter')").removeClass("active");
				$(".ui.secondary.menu").find("a:contains('Faces')").removeClass("active");
		
				if (active_mode == mode) {
					sort.nu_sort = (sort.nu_sort == 0) ? 1 : 0;
					
					sort.nu_sdate = $("#from_date").val().replace(/\./g, "-");
					sort.nu_edate = $("#to_date").val().replace(/\./g, "-");
					
					$.get("analyze.php?set_sort=" + sort.nu_sort + "&set_sdate=" + sort.nu_sdate + "&set_edate=" + sort.nu_edate + "&album_id=" + active_album, function (data) {
								
						lib_apply_filter();	
						lib_show_photos();	
						
					});
				} else {
					lib_show_photos();
				}
				
			break;
			case 2:
				$(".ui.secondary.menu").find("a:contains('All')").removeClass("active");
				$(".ui.secondary.menu").find("a:contains('Filter')").removeClass("active");
				$(".ui.secondary.menu").find("a:contains('Faces')").addClass("active");
				
				lib_show_faces();
			break;
		}
		
		active_mode = mode;

}

function lib_apply_filter() {
	if (active_album != -1) {
		var sd = $("#from_date").val().replace(/\./g, "-");
		var ed = $("#to_date").val().replace(/\./g, "-");
			
		$.get("analyze.php?set_sort=" + sort.nu_sort + "&set_sdate=" + sd + "&set_edate=" + ed + "&album_id=" + active_album, function (data) { lib_show_photos(); });
	}
}

function lib_update_sort() {
	if (active_album != -1) {
		if (sort.nu_sort == 0) {
			$("#sort_icon").addClass("descending");
			$("#sort_icon").removeClass("ascending");
		} else {
			$("#sort_icon").addClass("ascending");
			$("#sort_icon").removeClass("descending");			
		}
			
		if ($("#from_date").length != 0) {
			sort.nu_sdate = $("#from_date").val().replace(/\./g, "-");
		}
		if ($("#to_date").length != 0) {
			sort.nu_edate = $("#to_date").val().replace(/\./g, "-");
		}
	}
}

function lib_show_photos() {
	$("#back-button").hide();
	$("#album_content").html('');
	
	$("#loader").dimmer('toggle');
	active_mode = 0;
	
	$.get("lib-db.php?get_album_watchlist=" + active_album, function( data ) {
		var content = decodeURI(data);
		
		content = $.parseJSON(content);
		
		$("#album_content").html('<div class="ui secondary menu"><a class="active item" onclick="lib_change_mode(0);"><i id="sort_icon" class="sort content descending icon"></i>Фотографии</a><a class="item" onclick="lib_change_mode(2);">Лица</a><div class="right menu"><div class="item"><div class="ui labeled input"><div class="ui label">Выбрать от</div><input type="text" id="from_date" style="padding-right: 0px !important; width: 120px; border: 1px solid rgba(0,0,0,.15);"></div></div><div class="item"><div class="ui labeled input"><div class="ui label">до</div><input type="text" id="to_date" style="padding-right: 0px !important; width: 120px; border: 1px solid rgba(0,0,0,.15);"></div></div><a class="ui item" onclick="lib_apply_filter();">Применить</a></div></div>');
		$("#album_content").append('<div class="ui six doubling cards" id="album_images">');

		lib_get_sort();

		$.each(content, function(index, value) {
			var result = null;
	
			$.ajax({
		        url: "analyze.php?get_photos=" + value.id + "&album=" + active_album,
		        type: 'get',
		        async: false,
		        success: function(data) {
					var content = decodeURI(data);
					content = $.parseJSON(content);	
				
					result = content;				
				}
			});
		         
			
			$.each(result, function(index, value) {
				$("#album_images").append('<div class="ui card" path="' + value.path + '" time_taken="' + value.time_taken + '"><a class="image" onclick="lib_get_file(\'' + value.path + '\', 1);"><img src="images/image.png"></a></div></div>');
				
				$.get("analyze.php?get_thumbnail=" + value.path, function( data ) {
					$('.ui.card[path="' + value.path + '"]').find('a').find('img').attr("src", "data:image/png;base64," + data);	
				});
			
			});
		
		});
		
		$("#album_content").append('</div>');
		$("#loader").dimmer('toggle');

	});
	
}

function lib_viewer_prev() {
	$('.ui.card[path="' + active_viewer + '"]').prev().find('a').click();
}

function lib_viewer_next() {
	$('.ui.card[path="' + active_viewer + '"]').next().find('a').click();
}

function lib_show_album(album_id) {
	
	if (album_id != -1) {
		$("#loader").dimmer('toggle');
		$("#back-button").hide();
		active_album = 0;
		
		$.get("lib-db.php?get_album_watchlist=" + album_id, function( data ) {
			var content = decodeURI(data);
			
			content = $.parseJSON(content);
		
			console.log(content);
			
			if (content.length == 0) {
				lib_show_no_watches();
			} else {	
				lib_show_photos();
				console.log('render album');
			}
			$("#loader").dimmer('toggle');
			
			$("#albums_list").sidebar("hide");
			
		});
	
		active_album = album_id;
		lib_set_menu_title();
	}	
}

function lib_remove_show() {
	lib_show($("#delete_from_album"));
	lib_hide($("#remove_from_photo"));
	$("#viewer").find('.actions').find('.ui.buttons').removeClass("two");
	$("#viewer").find('.actions').find('.ui.buttons').addClass("three");
}

function lib_remove_photo_show() {
	lib_show($("#remove_from_photo"));
	lib_hide($("#delete_from_album"));
	$("#viewer").find('.actions').find('.ui.buttons').removeClass("two");
	$("#viewer").find('.actions').find('.ui.buttons').addClass("three");
}

function lib_remove_hide() {
	lib_hide($("#delete_from_album"));
	lib_hide($("#remove_from_photo"));	
	$("#viewer").find('.actions').find('.ui.buttons').removeClass("three");
	$("#viewer").find('.actions').find('.ui.buttons').addClass("two");
}

function lib_photo_delete() {
	$(".ui.card[path='" + active_viewer + "']").remove();
	console.log("delete " + active_viewer);
	
	$('<div class="ui info message removed"><div class="header">Фотография успешно удалена</div><ul class="list"><li>Вы можете вернуть фотографию импортировав её из папки наблюдения в которой она была.</li></ul></div>').insertAfter(".ui.secondary.menu");
    
	setTimeout(function() {$(".ui.message.removed").remove();}, 3000);
	
	$.get("lib-db.php?delete_photo=" + active_viewer + "&album=" + active_album, function( data ) {
		var content = decodeURI(data);
		console.log(content);
	});
	
}

function lib_hide(smth) {
	smth.css("display", "none");
	smth.css("visibility", "hidden");
}

function lib_show(smth) {
	smth.css("display", "");
	smth.css("visibility", "");
}

function lib_set_dropdown(dropdown) {
	$(dropdown).dropdown({
			action: function(value, text, $choice) {
				if (value == "Переименовать") {
					lib_change_album_name(visible_edit_menu);
					$("#album_name_" + visible_edit_menu).parent().parent().find(".dropdown").dropdown("hide");
				} else if (value == "Удалить") {
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#watches'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#rename'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#delete'));
					
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#sure'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#yes'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#cancel'));
					
					$("#album_item_" + visible_edit_menu).next().css("margin-top", "80px");
				} else if (value == "Отмена") {
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#watches'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#rename'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#delete'));
					
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#sure'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#yes'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#cancel'));
				} else if (value == "Да") {
					lib_delete_album(visible_edit_menu);
				} else if (value == "Папки наблюдения") {
					lib_show_watches(visible_edit_menu);
				}
			},
			onHide: function() {
				$(this).parent().next().css("margin-top", "0px");	
				
				lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#watches'));
				lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#rename'));
				lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#delete'));
					
				lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#sure'));
				lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#yes'));
				lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#cancel'));

				$("#album_edit_icon_" + visible_edit_menu).addClass("down");
				$("#album_edit_icon_" + visible_edit_menu).removeClass("up");				

				visible_edit_menu = -1;
			},
			onShow: function() {
				$(this).parent().next().css("margin-top", "113px");	
				visible_edit_menu = $(this).parent().attr('id').split('_')[2];
				
				$("#album_edit_icon_" + visible_edit_menu).removeClass("down");
				$("#album_edit_icon_" + visible_edit_menu).addClass("up");
			}
  		});
}

function lib_delete_album(album_id) {
	if ($("#album_item_" + album_id).next().length == 0) {
		if ($("#album_item_" + album_id).prev().length == 0) {
			$("#album_content").html(no_albums);
		} else {
			$("#album_item_" + album_id).prev().find('.input').find('div').click();
		}
	} else {
		$("#album_item_" + album_id).next().find('.input').find('div').click();
	}
	
	$("#album_item_" + album_id).next().css("margin-top", "0px");
	$("#album_item_" + album_id).remove();
	
	albums_count--;
	
	$.get("lib-db.php?delete_album=" + album_id, function( data ) {
		var content = decodeURI(data);

	});
	
	lib_set_dropdown($(".menu-button-item"));

	visible_edit_menu = -1;

}

function lib_add_album() {
	albums_count++;
	
	$.get("lib-db.php?add_album", function( data ) {
		var content = decodeURI(data);
		var album_id = content;
		
		$("#albums_list").append('<div class="item album_item" id="album_item_' + album_id + '" style="display: none;"><div class="ui transparent large input"><input type="text" onkeyup="lib_pressenter()" onblur="lib_set_album_name(this,1);" placeholder="Введите название" id="album_name_' + album_id + '" class="album_input"></div></div>');
		
		$("#album_item_" + album_id).transition('fade down');
	
		if (typeof $("#content-no-album") != undefined) {
			$("#content-no-album").remove();
		}
	});
	
}

function lib_rename_album(id, name) {
	lib_set_menu_title();
	
	$.get("lib-db.php?rename_album=" + encodeURIComponent(name) + "&album_id=" + id, function( data ) {
		var content = decodeURI(data);
		
		console.log(content);				
	});
}

function lib_set_album_name(input, newalbum) {
	var name = $(input).val();
	var id = $(input).attr('id');
		id = id.split('_')[2];	
		
	if ($(input).length) {
		
		$(input).replaceWith('<div id="album_label_' + id + '" class="album_label" onclick="lib_show_album(this.id.split(\'_\')[2]);">' + name + '</div>');
		if (newalbum == 1) {
			$("#album_label_" + id).parent().parent().append('<div class="ui dropdown menu-button-item"><i id="album_edit_icon_' + id + '"' + album_item_edit_dropdown);
		}
		
		lib_set_dropdown($("#album_label_" + id).parent().parent().find(".dropdown"));
		
		lib_rename_album(id, name);
		
		active_rename_menu = -1;
	}
}

function lib_pressenter(e) {
	if (!e) {
		e = window.event; 
	}
	var keyCode = e.keyCode || e.which; 
	
	if (keyCode == '13') {
		$(e.target).blur();
	} 
	
}

function lib_change_album_name(label) {
	var name, id = null;
	
	if (typeof label == "object") {
		name = $(label).text();
		id = $(label).attr('id');
		id = id.split('_')[2];
	} else {
		id = label;
		name = $("#album_label_" + label).text();
		label = $("#album_label_" + id);
	}
	
	active_rename_menu = id;
	
	$(label).replaceWith('<input type="text" onkeyup="lib_pressenter()" onblur="lib_set_album_name(this,0);" placeholder="Введите название" id="album_name_' + id + '" class="album_input">');	

	$("#album_name_" + id).val(name);
	$("#album_name_" + id).focus();

}