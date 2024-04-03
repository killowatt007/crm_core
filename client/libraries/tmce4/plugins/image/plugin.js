/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('image', function(editor) {
	function showDialog() {
		var win, data, dom = editor.dom, imgElm = editor.selection.getNode();
		var width, height;

		function recalcSize(e) {
			var widthCtrl, heightCtrl, newWidth, newHeight;

			widthCtrl = win.find('#width')[0];
			heightCtrl = win.find('#height')[0];

			newWidth = widthCtrl.value();
			newHeight = heightCtrl.value();

			if (win.find('#constrain')[0].checked() && width && height && newWidth && newHeight) {
				if (e.control == widthCtrl) {
					newHeight = Math.round((newWidth / width) * newHeight);
					heightCtrl.value(newHeight);
				} else {
					newWidth = Math.round((newHeight / height) * newWidth);
					widthCtrl.value(newWidth);
				}
			}

			width = newWidth;
			height = newHeight;
		}

		width = dom.getAttrib(imgElm, 'width');
		height = dom.getAttrib(imgElm, 'height');

		if (imgElm.nodeName == "IMG" && !imgElm.getAttribute('data-mce-object')) {
			data = {
				src: dom.getAttrib(imgElm, 'src'),
				alt: dom.getAttrib(imgElm, 'alt'),
				class: dom.getAttrib(imgElm, 'class'),
				align: dom.getAttrib(imgElm, 'align'),
				width: width,
				height: height
			};
		} else {
			imgElm = null;
		}
		win = editor.windowManager.open({
			title: "Edit image",
			data: data,
			body: [
				{name: 'src', type: 'filepicker', filetype: 'image', label: 'Источник', autofocus: true},
				{name: 'alt', type: 'textbox', label: 'Описание'},
				{name: 'class', type: 'textbox', label: 'CSS Класс'},
				{name: 'align', type: 'listbox', label: 'Выравнивание', values: [
					{text: 'Нет', value: ''},
					{text: 'Слева', value: 'left'},
					{text: 'Справа', value: 'right'}
				]},
				{
					type: 'container',
					label: 'Размеры',
					layout: 'flex',
					direction: 'row',
					align: 'center',
					spacing: 5,
					items: [
						{name: 'width', type: 'textbox', maxLength: 3, size: 3, onchange: recalcSize},
						{type: 'label', text: 'x'},
						{name: 'height', type: 'textbox', maxLength: 3, size: 3, onchange: recalcSize},
						{name: 'constrain', type: 'checkbox', checked: true, text: 'Пропорционально'}
					]
				}
			],
			onSubmit: function(e) {
				var data = e.data;

				if (data.width === '') {
					delete data.width;
				}

				if (data.height === '') {
					delete data.height;
				}

				if (data.align === false) {
					delete data.align;
				}

				if (imgElm) {
					dom.setAttribs(imgElm, data);
				} else {
					editor.insertContent(dom.createHTML('img', data));
				}
			}
		});
	}

	editor.addButton('image', {
		icon: 'image',
		tooltip: 'Insert/edit image',
		onclick: showDialog,
		stateSelector: 'img:not([data-mce-object])'
	});

	editor.addMenuItem('image', {
		icon: 'image',
		text: 'Insert image',
		onclick: showDialog,
		context: 'insert',
		prependToContext: true
	});
});
