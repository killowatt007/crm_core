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
			let html = App.render(this, true)

			if (!this.selector)
			{
				this.selector = App.selector
				html =
					'<div class="builder '+this.opts.tablename+'" alias="'+this.opts.tablename+'">'+
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
				// content
				if (row.type == 'content')
				{
					html +=
						'<div class="content '+row.data.class+'">'+
							'<div class="wrapper id'+tmpl.id+'">'+
								self.content[tmpl.childId]+
							'</div>'+
						'</div>'
				}
				// header
				else if (row.type == 'header')
				{
					html += self.renderHeader(row.data)
				}
				// row
				else if (row.type == 'row')
				{
					html += '<div class="section"><div class="row">'

					row.columns.map(function(column)
					{
						html += '<div class="col-sm-'+column.size+' col-xs-24">'

						column.data.map(function(cdata)
						{
							if (cdata.type == 'addon')
							{
								html += self.renderAddon(cdata)
							}
							else if (cdata.type == 'row')
							{
								html += self.build({data:[cdata]})
							}
						})

						html += '</div>'
					})

					html += '</div></div>'
				}
			})

			return html
		},

		renderAddon: function(data)
		{
			let object = this.getActor(data)

			object.model = this
			this.getPluginManager().run('beforeRenderAddon', [this, object])

			return App.render(object)
		},

		renderHeader: function(data)
		{
			let self = this,
					html = 
						'<div class="navigation">'+
							data.map(function(group)
							{
								let addons = group.data,
										html =
											'<div class="'+group.position+'">'+
												(addons.length ? addons.map(addon => self.renderAddon(addon)).join('') : '&ensp;')+
											'</div>'

								return html
							}).join('')+
						'</div>'

			return html
		},

		onAfterRender: function()
		{
			let alias = $('.builder').attr('alias')

			$('.builder').removeClass(alias)
			$('.builder').addClass(App.item.builderalias)
			$('.builder').attr('alias', App.item.builderalias)
		},
  })
})