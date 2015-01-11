// nuPhoto
// JS for index.php

var albums_count = 0;
var active_album = -1;
var visible_edit_menu = -1;
var active_rename_menu = -1;
var active_delete_menu = -1;

var album_item_edit_dropdown = ' class="chevron down icon" style="margin-right: 0px; width: 23px;"></i><div class="menu album-edit-dropdown"><div class="item" style="padding: 12px !important;" id="rename">Rename</div><div class="item" style="padding: 12px !important;" id="delete">Delete</div><div class="item" style="text-align: center;background: #fff;cursor: default; visibility: hidden; display: none;" id="sure">Are you sure?</div><div class="item" style="padding: 12px !important;line-height: 22px;width: 130px;margin: 0px;float: left;text-align: center;background: rgba(0, 128, 0, 0.2); visibility: hidden; display: none;" id="yes">Yes</div><div class="item" style="padding: 12px !important;line-height: 22px;width: 130px;margin: 0px;float: left;text-align: center;background: rgba(255, 0, 0, 0.2); visibility: hidden; display: none;" id="cancel">Cancel</div></div></div>';

var album_no_watches = '<div id="content-no-watches"><h2 class="ui icon header"><i class="folder icon"></i><div class="content">No watches<div class="sub header">Add new folders in side menu to watch photos.</div></div></h2></div>';

function lib_load() {
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
			$("#albums_list").append('<div class="item album_item" id="album_item_' + value.id + '"><div class="ui transparent large input"><div id="album_label_' + value.id + '" class="album_label" ondblclick="lib_change_album_name(this);" onclick="lib_show_album(this.id.split(\'_\')[2]);">' + value.nu_name + '</div></div>' + '<div class="ui dropdown menu-button-item"><i id="album_edit_icon_' + value.id + '"' + album_item_edit_dropdown + '</div>');

    	}); 
    	
    	lib_show_album(active_album);
		
		lib_set_dropdown($(".menu-button-item"));
	});
	
}

function lib_show_no_watches() {
	$("#album_content").html(album_no_watches);
}

function lib_show_album(album_id) {
	
	$.get("lib-db.php?get_album_watchlist=" + album_id, function( data ) {
		var content = decodeURI(data);
		
		content = $.parseJSON(content);
	
		console.log(content);
		
		if (content.length == 0) {
			lib_show_no_watches();
		} else {
			console.log('render album');
		}
		
		$("#albums_list").sidebar("hide");
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
				if (value == "Rename") {
					lib_change_album_name(visible_edit_menu);
					$("#album_name_" + visible_edit_menu).parent().parent().find(".dropdown").dropdown("hide");
				} else if (value == "Delete") {
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#rename'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#delete'));
					
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#sure'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#yes'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#cancel'));
					
					//$("#album_item_" + visible_edit_menu).next().css("margin-top", "78px");
				} else if (value == "Cancel") {
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#rename'));
					lib_show($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#delete'));
					
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#sure'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#yes'));
					lib_hide($("#album_item_" + visible_edit_menu).find(".dropdown").find('.menu').find('#cancel'));
				} else if (value == "Yes") {
					lib_delete_album(visible_edit_menu);
				}
			},
			onHide: function() {
				$(this).parent().next().css("margin-top", "0px");	
				
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
				$(this).parent().next().css("margin-top", "73px");	
				visible_edit_menu = $(this).parent().attr('id').split('_')[2];
				
				$("#album_edit_icon_" + visible_edit_menu).removeClass("down");
				$("#album_edit_icon_" + visible_edit_menu).addClass("up");
			}
  		});
}

function lib_delete_album(album_id) {
	$("#album_item_" + album_id).next().css("margin-top", "0px");
	$("#album_item_" + album_id).remove();
	
	albums_count--;
	
	$.get("lib-db.php?delete_album=" + album_id, function( data ) {
		var content = decodeURI(data);

		console.log(content);
	});
	
	lib_set_dropdown($(".menu-button-item"));

	visible_edit_menu = -1;
}

function lib_add_album() {
	albums_count++;
	
	$.get("lib-db.php?add_album", function( data ) {
		var content = decodeURI(data);
		var album_id = content;
		
		$("#albums_list").append('<div class="item album_item" id="album_item_' + album_id + '" style="display: none;"><div class="ui transparent large input"><input type="text" onkeyup="lib_pressenter()" onblur="lib_set_album_name(this,1);" placeholder="Enter name" id="album_name_' + album_id + '" class="album_input"></div></div>');
		
		$("#album_item_" + album_id).transition('fade down');
	
		if (typeof $("#content-no-album") != undefined) {
			$("#content-no-album").remove();
		}
	});
	
}

function lib_rename_album(id, name) {
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
		
		$(input).replaceWith('<div id="album_label_' + id + '" class="album_label" ondblclick="lib_change_album_name(this);">' + name + '</div>');
		if (newalbum == 1) {
			$("#album_label_" + id).parent().parent().append('<div class="ui dropdown menu-button-item"><i id="album_edit_icon_' + value.id + '"' + album_item_edit_dropdown);
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
	
	$(label).replaceWith('<input type="text" onkeyup="lib_pressenter()" onblur="lib_set_album_name(this,0);" placeholder="Enter name" id="album_name_' + id + '" class="album_input">');	

	$("#album_name_" + id).val(name);
	$("#album_name_" + id).focus();

}