define(function(require) 
{
	let Actor = require('components/fabrik/actor')

	require('components/builder/actors/fabrik/popup')
	require('components/fabrik/actors/list/rows')

  return new Class(
  {
  	Extends: Actor,
		Implements: Events,

  	rows: {},

  	currentKey: null,
  	currentRAlias: null,

		render: function()
		{
			let html = ''

			this.fields = {}
			this.initRowGroup({
				rows: this.opts.rows,
				fieldsgroup: this.opts.fieldsgroup,
				actions: this.opts.actions
			})

			html =
				'<div id="'+this.key+'" class="fabrik list">'+
					this.renderLabel()+
					'<div class="data">'+
						this.renderTable()+
				  '</div>'+
				  this.renderFooter()+
			  '</div>'

			return html
		},

		initRowGroup: function(data, alias)
		{
			alias = get(alias, 'main')

			this.rows[alias] = this.getActor({
				group: 'fabrik',
				name: 'rows',
				branch: 'list',
				opts: {
					alias: alias,
					rows: data.rows,
					fieldsgroup: data.fieldsgroup,
					actions: get(data, [], 'actions'),
					colspan: (this.opts.headers.length+1),
					list: this,

					left_colspan: get(data, 2, 'left_colspan'),
					tr_class: get(data, 'rw data-row', 'tr_class'),
				}
			})

			return this.rows[alias]
		},

		renderTable: function()
		{
			let args = {
						html: ''
					}

			this.getPluginManager().run('renderBody', [args])

			if (!args.html)
			{
				args.html = 
					'<table>'+
						this.renderHeader()+
						this.renderBody()+
					'</table>'
			}

			return args.html
		},

		renderLabel: function()
		{
			let html = ''

			if (this.opts.label)
			{
				html = 
					'<h4 class="lab">'+this.opts.label+'</h4>'
			}

			return html
		},

		renderHeader: function()
		{
			let html = '',
					colspan = this.opts.actions.length ? 'colspan="2"' : ''

			html += 
				'<thead>'+
					'<tr>'+
						this.opts.headers.map(function(header)
						{
							let label = header.isshow ? header.label : '',
									width = header.width ? 'width:'+header.width+';' : '',
									html = '<th style="'+width+'">'+label+'</th>'

							return html;
						}).join('')+
						'<th class="buttons" '+colspan+'>'+
							this.renderButtons()+
						'</th>'
					'</tr>'+
				'</thead>'

			return html
		},

		renderFooter: function()
		{
			let html = '',
					fdisplay,
					fpagination,
					disopts = []
					pagopts = []

			if (this.opts.lenghtrows > 10)
			{
				for (var i = 1; i < 11; i++)
				   disopts.push({value:String(i*10), label:i*10})
				disopts.push({value:'500', label:'500'})

      	fdisplay = this.getActor({
					group: 'field',
					name: 'list',
					value: this.opts.display,
					opts: {
						options: disopts,
						name: 'display',
						isedit: true,
						isps: false,
						minimumResultsForSearch: -1
					}
				})

				pages = Math.ceil(this.opts.lenghtrows/this.opts.display)
				for (var i = 0; i < pages; i++)
				   pagopts.push({value:String(i), label:i+1})

      	fpagination = this.getActor({
					group: 'field',
					name: 'list',
					value: this.opts.pagination,
					opts: {
						options: pagopts,
						name: 'pagination',
						isedit: true,
						isps: false,
						minimumResultsForSearch: -1
					}
				})

				html = 	
					'<div class="list-footer">'+
						'<div class="left">'+
							'<div class="lab">'+
								'Колличество'+
							'</div>'+
							'<div class="control">'+
								App.render(fdisplay)+
							'</div>'+
						'</div>'+
						'<div class="right">'+
							'<div class="lab">'+
								'Страница'+
							'</div>'+
							'<div class="control">'+
								App.render(fpagination)+
							'</div>'+
						'</div>'+
					'</div>'
			}

			return html
		},

		renderButtons: function()
		{
			let hrml = ''

			this.opts.buttons.map(btn =>
			{
				hrml += '<a href="#" class="b b-c b-'+btn.color+' button '+btn.name+'"><i class="'+btn.icon+'"></i></a>'
			})

			return hrml
		},

		renderBody: function()
		{
			let html =
						'<tbody>'+
							App.render(this.rows.main)+
						'</tbody>'

			return html
		},

		onAfterRender: function()
		{
			this.add()
			this.edit()
			this.footer()
		},

		footer: function()
		{
			let self = this

			this.node.find('.list-footer .forminput.display, .list-footer .forminput.pagination').change(function()
			{
				self.updateData()
			})
		},

		updateData: function()
		{
			let self = this

			this.getPluginManager().run('beforeUpdate')

			this.ajax({
				data: {
          task: 'list'
				},
				success: function(data)
				{
					self.updateRender(data)
				}
			})
		},

		add: function()
		{
			let self = this

			this.node.find('.button.add').click(function(e)
			{
				e.preventDefault()
				self.openPopup()
			})
		},

		edit: function()
		{
			let self = this

			if (!this.isupdrender)
			{
				this.node.on('click', '.data a.edit', function(e)
				{
					e.preventDefault()

					let tr = $(this).parents('.data-row:first'),
							rowalias = tr.attr('rowalias'),
							key = tr.attr('key'),
							row = self.rows[rowalias].opts.rows[key]

					self.currentRAlias = rowalias
					self.currentKey = key
					
					self.openPopup(row.id)
				})
			}
		},

		openPopup: function(rowId)
		{
			let popup,
					opts = {
						label: get(rowId) ? 'Редактировать запись' : 'Добавить запись',
						entityid: this.id,
						rowId: get(rowId)
					}

			this.getPluginManager().run('beforeOpenPopup', [opts])

			popup = this.getActor({
				group: 'builder',
				name: 'popup',
				branch: 'fabrik',
				opts: Object.assign(opts, get(this.opts, {}, 'popupOpts'))
			})

			popup.open()
		},

		getElement: function(i, key, alias)
		{
			alias = get(alias, 'main')
			return this.rows[alias].getElement(i, key)
		}
  })
})
