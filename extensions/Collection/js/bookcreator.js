/*
 * Collection Extension for MediaWiki
 *
 * Copyright (C) PediaPress GmbH
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

(function($) {

$(function() {

	var script_url = wgServer + ((wgScript == null) ? (wgScriptPath + "/index.php") : wgScript);

	function save_collection(collection) {
		$.jStorage.set('collection', collection);
	}

	window.wfCollectionSave = save_collection;

	function refreshBookCreatorBox(hint, oldid, bookid) {
		$.getJSON(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollectionGetBookCreatorBoxContent',
			'rsargs[]': [hint, oldid, bookid, wgPageName]
		}, function(result) {
			$('#coll-book_creator_box').html(result.html);
		});
	}

	function collectionCall(func, args) {
	  var hint = args.shift();
		$.post(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollection' + func,
			'rsargs[]': args
		}, function(result) {
			var oldid = null;
            var bookid = 0; 
			if (args.length == 4) { // 3
				oldid  = args[2]; // 2
                bookid = args[3];
			}
            //alert("TEST:" +args.length+ " oldid:" +oldid+" bookid:" +bookid);
			refreshBookCreatorBox(hint, oldid, bookid);
			save_collection(result.collection);
		}, 'json');
	}
    window.collectionCall = collectionCall; // public
    
    function removeBookCall( func, args ){
      if( confirm( 'Are you sure you want to delete this book?' ) ) {
        
         var hint = args.shift();
         var m = $('#mw-content-text').children('#user-page-left');
         alert("TEST.."+ $(m).children().is('.user-books-container'));
         /*$.post(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollection' + func,
			'rsargs[]': args
		}, function(result) {
		    var content = $('#mw-content-text').children('#user-page-left').children('.user-books-container');
			if(result.collection.book_id > 0){                           
			   goto_bookset( result.collection.book_id );                                
			} else {			 
			 $('.mw-body').children('#siteNotice').remove();
                 if($('#user-page-left').children().is('.user-books-container')){
                    $('#user-page-left').children('#user-books-block').html("");
                 }
            }
                                    
		}, 'json');*/
        
       } 
    }
    
    function goto_bookset( bookid ){
    var hint  = "";
    var oldid = "0";    
    $.getJSON(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollectionGetRenderBookCreatorBox',
			'rsargs[]': [hint, oldid, bookid, wgPageName]
		}, function(result) { 
		   if($('.mw-body').children().is('#siteNotice')){
		      $('.mw-body').children('#siteNotice').html(result.html); 
		   } //else {$('.mw-body').prepend('<div id="siteNotice">' + result.html + '</div>');}		    
           
		}); 
    }
    window.removeBookCall = removeBookCall; //public
    
            
	var mouse_pos = {};
	var popup_div = null;
	var addremove_link = null;
	var visible = false;
	var show_soon_timeout = null;
	var get_data_xhr = null;
	var current_link = null;
	var title = null;

	function createDiv() {
		addremove_link = $('<a href="javascript:void(0)" />');
		popup_div = $('<div id="collectionpopup" />');
		popup_div.append(addremove_link);
		$('body').append(popup_div);
		popup_div.hide();
	}

	function addremove_article(action, title) {
		$.post(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollection' + action.charAt(0).toUpperCase() + action.slice(1) + 'Article',
			'rsargs[]': [0, title, '']
		}, function(result) {
			hide();
			refreshBookCreatorBox(null, null);
			save_collection(result.collection);
		}, 'json');
	}

	function show(link) {
		if (visible) {
			return;
		}
		current_link = link;
		title = link.attr('title');
		if (!title){
			return;
		}
		link.attr('title', ''); // disable default browser tooltip
		show_soon_timeout = setTimeout(function() {
			get_data_xhr = $.post(script_url, {
				'action': 'ajax',
				'rs': 'wfAjaxCollectionGetPopupData',
				'rsargs[]': [title]
			}, function(result) {
				visible = true;
				var img = $('<img />').attr({src: result.img, alt: ''});
				addremove_link
					.text('\u00a0' + result.text)
					.prepend(img)
					.unbind('click')
					.click(function(e) { addremove_article(result.action, result.title); });
				popup_div
					.css({left: mouse_pos.x + 2 + 'px',
								top: mouse_pos.y + 2 + 'px'})
					.show();
			}, 'json');
		}, 300);
	}

	function cancel() {
		if (current_link && title) {
			current_link.attr('title', title);
		}
		if (show_soon_timeout) {
			clearTimeout(show_soon_timeout);
			show_soon_timeout = null;
		}
		if (get_data_xhr) {
			get_data_xhr.abort();
			get_data_xhr = null;
		}
	}

	function hide() {
		cancel();
		if (!visible) {
			return;
		}
		visible = false;
		popup_div.hide();
	}

	function is_inside(x, y, left, top, width, height) {
		var fuzz = 5;
		return x + fuzz >= left && x - fuzz <= left + width
			&& y + fuzz >= top && y - fuzz <= top + height;
	}

	function check_popup_hide() {
		if (!visible) {
			return;
		}
		var pos = popup_div.offset();
		if (!is_inside(mouse_pos.x, mouse_pos.y,
							 pos.left, pos.top, popup_div.width(), popup_div.height())) {
			hide();
		}
	}

	$(document).mousemove(function(e) {
		mouse_pos.x = e.pageX;
		mouse_pos.y = e.pageY;
	});
	setInterval(check_popup_hide, 300);
	createDiv();
	var prefix = wgArticlePath.replace(/\$1/, '');
	$("#bodyContent "
		+ "a[href^='" + prefix + "']" // URL starts with prefix of wgArticlePath
		+ ":not(a[href~='index.php'])" // URL doesn't contain index.php (simplification!)
		+ "[title!=]" // title attribute is not empty
		+ "[rel!=nofollow]"
		+ ":not(.external)"
		+ ":not(.internal)"
		+ ":not(.sortheader)"
		+ ":not([accesskey])"
		+ ":not(.nopopup)"
	).each(function(i, link) {
		if (this.onmousedown) {
			return;
		}
		var $this = $(this);
		if (!$this.attr('title') || $this.attr('title').indexOf(':') != -1) { // title doesn't contain ":" (simplification!)
			return;
		}
		if ($this.parents('.nopopups').length) {
			return;
		}
		$this.hover(function() { show($this); }, cancel);
	});
});

})(jQuery);

