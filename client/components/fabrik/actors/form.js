define(function(require) 
{
	let Actor = require('components/fabrik/actor'),
			HObject = require('lib/bs/helper/object')

  require('components/builder/actors/fabrik/popup')
  require('components/builder/actors/table')

  return new Class(
  {
  	Extends: Actor,
  	Implements: Events,

  	fields: {},

  	popup: null,

		render: function()
		{
			let html = '',
					classes = ''

			this.fields = {}
			classes += this.opts.isEditable ? 'ed' : 'ro'

			this.getPluginManager().run('beforeRender')

			html =
				'<div id="'+this.key+'" class="fabrik form '+classes+'">'+
					this.renderForm()+
			  '</div>'

			return html
		},

		renderForm: function()
		{
			let html = ''

			if (this.opts.isEditable || this.opts.rowId)
			{
				html =
					'<div class="data">'+
				  	this.renderTmpl()+
				  '</div>'+
				  (this.opts.isEditable ? this.renderActions() : '')
			}
			else
			{
				html =
					'<div class="data">'+
						'<div class="row">'+
							'<div class="col col-md-24 norecord">Нет записи</div>'+
						'</div>'+
					'</div>'
			}

			return html
		},

		renderTmpl: function()
		{
			let self = this,
					html = '',
					table = this.getActor(this.opts.tmpl)
					
			table.parentobject = this
			html = App.render(table)

			return html
		},

		renderActions: function()
		{
			let html = ''

			if (App.ismobile && this.opts.actions.length > 2)
			{
				let closeBtn

				html = 
					'<div class="actions">'+
						'<div class="left">'+
						  '<div class="btn-group">'+
						    '<button type="button" class="btn btn-success dropdown-toggle">'+
						      'Действие'+
						    '</button>'+
						    '<div class="dropdown-menu">'+
									this.opts.actions.map(action => 
									{
										let html = ''

										if (action.name == 'close')
										{
											closeBtn = action
										}
										else
										{
											html = 
												'<button'+
													' type="'+get(action, 'submit', 'type')+'"'+
													' class="dropdown-item b b-s bs-r b-'+action.color+' '+get(action, '', 'class')+'"'+
													' value="'+get(action, '', 'name')+'"'+
												'>'+
													action.label+
												'</button>'

											return html
										}
									}).join('')+
						    '</div>'+
						  '</div>'+
						 '</div>'+
						 '<div class="right">'+
						 		this.renderAction(closeBtn)+
						 '</div>'+
				  '</div>'
			}
			else
			{
					html =
						'<div class="actions">'+
							'<div class="left">'+
								this.opts.actions.map((action) =>
								{
									if (action.position == 'left')
										return this.renderAction(action)
								}).join('')+
							'</div>'+
							'<div class="right">'+
								this.opts.actions.map((action) =>
								{
									if (action.position == 'right')
										return this.renderAction(action)
								}).join('')+
							'</div>'+
						'</div>'
			}





			return html
		},

		renderAction: function(action)
		{
			return '<button'+
								' type="'+get(action, 'submit', 'type')+'"'+
								' class="b b-s bs-r b-'+action.color+' '+get(action, '', 'class')+'"'+
								' value="'+get(action, '', 'name')+'"'+
							'>'+
								action.label+
							'</button>'
		},

		getElement: function(id)
		{
			if (!this.fields[id])
			{
				let data = this.opts.fields[id].data,
						element = new App.dep['components/field/actors/'+data.name](data)

				element.getPluginManager().setObserveForObj(this, {format: 'form', prefix: 'element'})

				// databasejoin
				if (element.opts.search)
				{
	      	element.opts.searchParams = {entityid: this.id, fieldid: id}
	      	element.opts.onSearch = (field, value) => this.search(field, value)
				}

				this.fields[id] = element
			}

			return this.fields[id]
		},

		search: function(field, value)
		{
			this.ajax({
				data: {
        	task: 'form.databasejoinSearch',
        	value: value,
        	entityid: field.opts.searchParams.entityid,
        	fieldid: field.opts.searchParams.fieldid
				},
				success: function(data)
				{
					field.opts.options = data.options
					field.updateRender(field, true)
				}
			})
		},

		getField: function(key)
		{
			let field

			if ($.isNumeric(key))
			{
				field = this.fields[key]
			}
			else
			{
				$.each(this.fields, function(id, f)
				{
					if (f.opts.name == key)
						field = f
				})
			}

			return field
		},

		onAfterRender: function()
		{
			this.submit()
			this.gb()
			this.edit()

			$('body').click(function(e)
			{
				if (!$(e.target).hasClass('dropdown-toggle'))
				{
					$('.btn-group').each(function()
					{
						$(this).removeClass('open')
					})
				}
			})

			this.node.find('.btn-group').each(function()
			{
				let self2 = this,
						btn = $(this).find('.dropdown-toggle'),
						menu = $(this).find('.dropdown-menu')

				btn.click(function()
				{
					$(self2).toggleClass('open')
				})
			})
		},

		edit: function()
		{
			let self = this

			this.node.find('.data a.edit').click(function(e)
			{
				e.preventDefault()
				self.openPopup(self.opts.rowId, 'Редактировать запись')
			})
		},

		add: function()
		{
			this.openPopup('', 'Добавить запись')
		},

		openPopup: function(rowid, label)
		{
			let popup = this.getActor({
		        group: 'builder',
		        name: 'popup',
		        branch: 'fabrik',
			      opts: {
							label: label,
							entityid: this.id,
							rowId: rowid
		        }
					})

			this.getPluginManager().run('beforeOpenPopup')
			popup.open()
		},

		gb: function()
		{
			if (this.popup) // onAfterOpenPopup !!!
				this.node.find('.actions .gb').click(()=>this.popup.close())
		},

		submit: function()
		{
			let self = this,
					btns = this.node.find('.actions button[type="submit"]')

			btns.click(function()
			{
				let result,
						args = {
							reqdata: {}
						}
				
				result = self.getPluginManager().run('beforeSubmit', [args])

				if (result.includes(false))
					return false

				self.ajax({
					data: $.extend(args.reqdata, {
          	task: 'form.process',
          	rowId: self.opts.rowId,
	          formData: self.getFormData(),
	          submit: $(this).val()
					}),
					afterSuccess: function(data)
					{
						let module,
								validAlert = ''

						if (data.validation.length)
						{
							data.validation.map(msg => validAlert += msg+"\n")
							alert(validAlert)
							return
						}

						self.getPluginManager().run('afterProcess', [data])

						if (self.popup)
						{
							if (self.opts.modulerefid)
							{
								let module = App.modules[self.opts.modulerefid]

								if (module.branch == 'fabrik.form')
									module.block.updateData(get(data.rowId))
								else if (module.branch == 'fabrik.list')
									module.block.updateData()
							}

							self.popup.close()
						}
					}
				})
			})
		},

		updateData: function(rowId)
		{
			let self = this

			this.getPluginManager().run('beforeUpdate')

			this.ajax({
				data: {
          task: 'form',
          rowId: get(rowId, this.opts.rowId)
				},
				success: function(data)
				{
					self.updateRender(data)
				}
			})
		},

		getFormData: function()
		{
			let data = {}

			// data2 = HObject.inputsToObject(this.node.find('.forminput'))

			this.opts.isdefaultvalues.map(row=>{ data[row.name] = row.value })

			$.each(this.fields, function(i, field)
			{	
				if (field.opts.isedit && field.opts.type != 'calc')
					data[field.opts.name] = field.getCurrentValue()
			})

			if (this.node.find('.params-container')[0])
				data.Params = HObject.inputsToObject(this.node.find('.params-container .forminput')).Params

			this.getPluginManager().run('afterFormData', [data])

			return data
		}
  })
})
