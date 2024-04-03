define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,
  	
  	options: {},

  	treeHtml: '',
  	items: {},

		opens: [],

		activeItem: 0,

		render: function()
		{
			let html = ''

			$.each(this.opts.itemsGroup, (i, group) =>
			{
				group.data.sort(function(a, b) {
					let _a = a.Name.toLowerCase(),
							_b = b.Name.toLowerCase()

					if (_a > _b)
						return 1
					if (_a < _b)
						return -1

					return 0
				});
			})

			html =
				'<div id="'+this.key+'" class="sk-tree-c">'+
					this.renderTree(0, this.opts.itemsGroup)+
				'</div>'

			return html
		},

		renderTree: function(groupId, groups, lvl, open)
		{
			let self = this,
					group = groups[groupId],
					ulStyle = open ? 'display:block' : ''

			lvl = !lvl ? 1 : lvl+1
			self.treeHtml += '<ul class="level'+lvl+'" style="'+ulStyle+'">'

			$.each(group.data, function(i, row)
			{
				let isChildren = groups[row.id] ? true : false,
						className = '',
						styleLink = 'margin-left: '+((20*lvl)+5)+'px;',
						open = self.opens.includes(parseInt(row.id))

				className += isChildren ? 'isChildren' : ''
				className += open ? ' open' : ''

				self.treeHtml += '<li key="'+row.id+'" style="position:relative;" class="'+className+'">'
				self.treeHtml += '<div class="inner">'

				if (get(self.opts, true, 'isedit'))
					self.treeHtml += '<i class="fas fa-pencil-alt edit"></i>'

				self.treeHtml +=
					'<div class="label">'+
						'<a href="#" class="item" style="'+styleLink+'">'+
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
					self.renderTree(row.id, groups, lvl, open)

				self.treeHtml += '</li>'

				self.items[row.id] = row
			})

			self.treeHtml += '</ul>'

			return self.treeHtml
		},
		
		onAfterRender: function()
		{
			this.menuControl()
			this.itemControl()

			this.node.css({
				height: ($(window).height() - 190)+'px'
			})
		},

		menuControl: function()
		{
			let self = this,
					menu = $('.sk-tree-c'),
					li = menu.find('li')

			li.each(function()
			{
				let isChildren = $(this).hasClass('isChildren'),
						li = this,
						link = $(this).find('>.inner>.label>a'),
						key = $(this).attr('key')

				link.click(function(e)
				{
					e.preventDefault()

					menu.find('li.active').removeClass('active')
					$(li).addClass('active')
					self.activeItem = key

					if (isChildren)
					{
						let open = !$(li).hasClass('open'),
								childUl = $(li).find('>ul')

						$(li)[open ? 'addClass' : 'removeClass']('open')
						childUl[open ? 'slideDown' : 'slideUp'](200)

						if (open)
						{
							self.opens.push(parseInt(key))
						}
						else
						{
							self.opens.map((k, i) => {
								if (key == k)
									delete self.opens[i]
							})
						}
					}
				})
			})
		},

		itemControl: function()
		{
			let self = this,
					items = $('.sk-tree-c .item')

			items.each(function()
			{
				let li = $(this).parents('li:first'),
						itemId = li.attr('key'),
						edit = li.find('>.inner .edit')

				edit.click(function()
				{
					self.editCategory(itemId)
				})

				$(this).click(function(e)
				{
					e.preventDefault()
					self.fireEvent('changeCategory', [itemId])
				})
			})
		},

		Implements: [Events],

		updateRender: function(data)
		{
			this.treeHtml = ''
			this.parent(data)
		},

		updateTree: function(_item, isnew)
		{
			let groupData

			if (!this.opts.itemsGroup[_item.CategoryId])
				this.opts.itemsGroup[_item.CategoryId] = {data: []}

			groupData = this.opts.itemsGroup[_item.CategoryId].data

			if (isnew)
			{
				groupData.push(_item)
			}
			else
			{
				groupData.map(item => 
				{
					if (item.id == _item.id)
						item.Name = _item.Name
				})
			}

			this.updateRender()
			this.onAfterRender()
		},

		editCategory: function(rowid)
		{
			let self = this,
					popup

			popup = this.getActor({
				group: 'builder',
				name: 'popup',
				branch: 'fabrik',
				opts: {
					label: 'Редактировать запись',
					entityid: this.opts.treeEntityId,
					rowId: get(rowid),
					afterOpen: function(form)
					{
						form.addEvent('afterProcess', function(data) 
						{
							self.updateTree(data.data, data.isnewrecord)
						})
					}
				}
			})
	
			popup.open()
		}
  })
})