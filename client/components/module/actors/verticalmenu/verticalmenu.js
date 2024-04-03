define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,
  	
  	options: {},

  	treeHtml: '',
  	items: {},

		render: function()
		{
			let classes = App.ismobile ? '' : 'mini',
					html =
						'<div class="vm '+classes+'">'+
							'<div class="button-open">'+
							  '<i class="fa fa-bars icon"></i>'+
							  '<div class="label">'+
							  	'МЕНЮ'+
								'</div>'+
							'</div>'+
							'<div class="cont">'+
								'<div class="header">'+
									'<div class="btn-expand"><i class="fa fa-solid fa-chevron-right"></i></div>'+
									'<div class="user_fw">'+this.opts.userName[0].toUpperCase()+'</div>'+
									'<div class="product">'+this.opts.productName+'</div>'+
									'<div class="user"><div class="layer">'+this.opts.userName+'</div></div>'+
								'</div>'+
								'<div class="tree">'+
									this.renderTree(0, this.opts.itemsGroup)+
								'</div>'+
							'</div>'+
						'</div>'

			return html
		},

		renderTree: function(groupId, groups)
		{
			let self = this,
					group = groups[groupId]

			self.treeHtml += '<ul class="level'+group.level+'">'

			$.each(group.data, function(i, row)
			{
				let isChildren = groups[row.id] ? true : false,
						link = isChildren ? '#' : row.path,
						className = '',
						styleLink = 'padding-left: '+(20*group.level)+'px;'

				className += isChildren ? 'isChildren' : ''
				className += self.opts.activeItem.id == row.id ? ' active' : ''

				self.treeHtml += '<li class="'+className+'">'
				self.treeHtml += '<div class="inner">'
				self.treeHtml +=
					'<div class="label">'+
						'<a key="'+row.id+'" class="item" href="'+link+'" style="'+styleLink+'">'+
							'<i class="'+row.Icon+'"></i>'+
							'<span class="text">'+row.Name+'</span>'+
						'</a>'

				if (isChildren)
				{
					self.treeHtml +=
						'<div class="control">'+
							'<i class="fa fa-sort-up arrow"></i>'+
						'</div>'
				}

				self.treeHtml +=
					'</div>'

				self.treeHtml +='</div>'

				if (isChildren)
					self.renderTree(row.id, groups)

				self.treeHtml += '</li>'

				self.items[row.id] = row
			})

			self.treeHtml += '</ul>'

			return self.treeHtml
		},

		isleave: false,

		onAfterRender: function()
		{
			this.menuControl()
			this.itemControl()
			this.btnExpand()
		},

		btnExpand: function()
		{
			let self = this,
					vm = $('.vm')

			vm.find('.btn-expand').click(function()
			{
				if (!App.ismobile)
					vm.toggleClass('mini')

				self.open()
			})
		},

		menuControl: function()
		{
			let self = this,
					content = $('.builder > .cont > .content'),
					menu = $('.vm'),
					btnOpen = menu.find('.button-open'),
					cont = menu.find('.cont'),
					li = cont.find('li')

			li.each(function()
			{
				let isChildren = $(this).hasClass('isChildren'),
						li = this,
						link = $(this).find('>.inner>.label>a')

				if (isChildren)
				{
					link.click(function()
					{
						let active = !$(li).hasClass('active'),
								childUl = $(li).find('>ul')

						$(li)[active ? 'addClass' : 'removeClass']('active')
						childUl[active ? 'slideDown' : 'slideUp'](200)
					})
				}
			})

			btnOpen.click(function() {
				self.open()
			})

			content.click(function()
			{
				let isActive = menu.hasClass('active')

				if (isActive)
					self.open()
			})

			cont.mouseleave(function()
			{
				if (self.isleave)
				{
					if (!App.ismobile && !menu.hasClass('mini'))
						self.open()
				}
			})
		},

		leave: function()
		{
			let body = $('body')

			this.isleave = true
			body.addClass('vm-leave')
		},

		setActive: function(id)
		{
			let li = $('.vm .tree li:not(.isChildren)')
					activeItem = $('.vm .tree a.item[key='+id+']')

			li.removeClass('active')
			activeItem.parents('li:first').addClass('active')
		},

		open: function(_opt)
		{
			let self = this,
					opt = _opt ? _opt : {},
					body = $('body'),
					menu = $('.vm'),
					active = !menu.hasClass('active'),
					cont = menu.find('.cont'),
					btnOpen = menu.find('.button-open'),
					btnOpenIcont = btnOpen.find('.fa'),
					duration = opt.duration ? opt.duration : 200,

					bContent = $('.builder > .cont > .content')

			self.isleave = false
			body.removeClass('vm-leave')

			bContent.css('transition', duration+'ms')
			cont.css('transition', duration+'ms')
			btnOpen.css('transition', 'margin '+duration+'ms')

			cont.height($(window).height())
			menu[active ? 'addClass' : 'removeClass']('active')

			if (!App.ismobile)
				menu[active ? 'removeClass' : 'addClass']('mini') // temp!!!

			body[active ? 'addClass' : 'removeClass']('vm-active')

			if (active)
			{
				body.addClass('vm-overflow')
			}
			else
			{
				setTimeout(function()
				{
					body.removeClass('vm-overflow')
				}, duration)
			}

			btnOpenIcont[active ? 'addClass' : 'removeClass']('fa-times')
			btnOpenIcont[!active ? 'addClass' : 'removeClass']('fa-bars')
		},

		itemControl: function()
		{
			let self = this,
					items = $('.vm .tree .item')

			items.each(function()
			{
				let itemId = $(this).attr('key'),
						itemData = self.items[itemId]

				$(this).click(function(e)
				{
					e.preventDefault()

					if (itemData.Type == 'component')
					{
						$(this).parents('li:first').addClass('active')

						App.window.open(itemId, { 
							callback: ()=>
							{
								self.setActive(itemId)

								if (App.ismobile)
									self.open()
								else
									self.leave()
							}
						})
					}
					else if (itemData.Type == 'logout')
					{
						App.ajax({
							data: {
								option: 'system',
								task: 'user.logout'
							},
							success: function(data) 
							{
								if (data.redirect)
									window.location.replace(data.redirect)
							}
						})
					}
				})
			})
		}
  })
})