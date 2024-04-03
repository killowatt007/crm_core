define(function(require) 
{
	let View = require('view')

	// require('components/field/actors/html/tabs')
	require('components/field/actors/list')

  return new Class(
  {
  	Extends: View,

  	tabs: null,
  	modules: null,

		render: function()
		{
			let html = ''

			html +=
				'<div id="'+this.key+'" class="fabrik history">'+
					this.renderFilter()+
					'<div class="container-data">'+this.renderEmpty()+'</div>'+
				'</div>'

			return html
		},

		renderEmpty: function()
		{
			return '<div class="empty"></div>'
		},

		renderFilter: function()
		{
			let html = '',
		      entities = this.getActor({
		        group: 'field',
		        name: 'list',
		        // value: this.opts.display,
		        opts: {
		          options: this.opts.options,
		          name: 'entityid',
		          isedit: true,
		          isps: true,
		          minimumResultsForSearch: -1
		        }
		      })

			html =
				'<div style="margin-bottom:20px;">'+
					'<div class="row">'+
						'<div class="col-sm-24 col-xs-24">'+
							'<div class="fabrik filter">'+
								'<div class="field fabrik">'+
									'<div class="label">Таблица</div>'+
									'<div class="control">'+
										App.render(entities)+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>'+
				'</div>'

			return html
		},

		// tabs: function()
		// {
  // 		let html = '',
  // 				items = [
		//         {label: 'Список', content: 'Пусто'}
		//      	]

		// 	this.tabs = this.getActor({
  //       group: 'field',
  //       name: 'tabs',
  //       branch: 'html',
  //       opts: {tabs: items}
  //     })

  // 		html = '<div class="addon-tabs">'+App.render(this.tabs)+'</div>'
  					
  // 		return html	
		// },

		onAfterRender: function()
		{
			let self = this

			this.node.find('.entityid').change(function()
			{
				let moduleid = $(this).val(),
						container = self.node.find('.container-data'),
						empty = container.find('.empty')

				if (moduleid)
				{
					self.ajax({
						data: {
		        	task: 'history.getModule',
		        	branch: 'fabrik',
		          moduleid: moduleid
						},
		        success: function(data)
		        {
		        	let isfirst =	self.modules ? false : true

		        	empty.hide(0)

		        	if (isfirst)
		        	{
								let list = new App.dep['components/fabrik/actors/list'](data.module.rows)

								self.modules = { list: list }
		        		container.append(App.render(self.modules.list))
		        	}
		        	else
		        	{
		        		self.modules.list.updateRender(data.module.rows)
		        		self.node.find('.fabrik.list').show(0)
		        	}
		        }
					})
				}
				else
				{
					empty.show(0)
					self.node.find('.fabrik.list').hide(0)
				}
			})
		}
  })
})
