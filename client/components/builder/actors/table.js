define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,

  	selector: null,
  	content: {},

  	html: '',

		execute: function()
		{
			let html = App.render(this)

			if (!this.selector)
			{
				this.selector = App.selector
				html =
					'<div class="builder id-'+this.id+'">'+
						'<div class="cont">'+
							html+
						'</div>'+
					'</div>'
			}

      $(this.selector).html(html)
		},

		render: function()
		{
			let self = this,
					firstKey = Object.keys(this.opts.tmpls)[0]

			this.opts.tmpls.map(function(tmpl)
			{
				let id = tmpl.id

				if (tmpl.render)
				{
					self.html = self.build(tmpl)
					self.content[id] = self.html

					self.getPluginManager().run('tmplAfterBuild', [self, tmpl])
				}
				else
				{
					self.selector = '.id'+id
				}
			})

			return this.html
		},

		build: function(tmpl)
		{
			let self = this,
					html = ''

			tmpl.data.map(function(row)
			{
				// row
				if (row.type == 'row')
				{
					let countAddon = 0,
							rowHtml = '<div class="row">'

					row.columns.map(function(column, i)
					{
						let size = column.size

						// temp!!!
						if (App.ismobile && !get(self.opts, false, 'nomobile'))
						{
							if (!i)
							{
								size = 24
							}
							else if (i == 1)
							{
								size = 24
							}
						}

						rowHtml += 
							'<div class="col col-'+size+'">'

						column.data.map(function(cdata)
						{
							if (cdata.type == 'addon')
							{
								let addonHtml = self.renderAddon(cdata)

								if (addonHtml)
								{
									rowHtml += addonHtml
									countAddon++
								}
							}
							else if (cdata.type == 'row')
							{
								html += self.build({data:[cdata]})
							}
						})

						rowHtml += '</div>'
					})

					html += countAddon ? rowHtml+'</div>' : ''
				}
			})

			return html
		},

		renderAddon: function(data)
		{
			let object = this.getActor(data)
			return App.render(object)
		},

		// renderHeader: function(data)
		// {
		// 	let self = this,
		// 			html = 
		// 				'<div class="navigation">'+
		// 					data.map(function(group)
		// 					{
		// 						let addons = group.data,
		// 								html =
		// 									'<div class="'+group.position+'">'+
		// 										(addons.length ? addons.map(addon => self.renderAddon(addon)).join('') : '&ensp;')+
		// 									'</div>'

		// 						return html
		// 					}).join('')+
		// 				'</div>'

		// 	return html
		// },

		onAfterRender: function()
		{
			this.node = $('#'+this.key)
		}
  })
})