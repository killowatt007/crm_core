define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,
  	// Implements: Events,

  	html: null,
  	applyField: null,

		render: function()
		{
			let html =
						'<div id="'+this.key+'" class="fabrik filter">'+
							(this.html ? this.html : this.renderFields())+
						'</div>'

			return html
		},

		renderFields: function()
		{
			let self = this,
					html = ''

			this.opts.fields.map(function(field)
			{
	      html += self.renderField(field)
			})

			return html
		},

		renderField: function(field, notlabel)
		{
      let self = this,
      		html = ''

      if (field.display)
      {
	  		html =
					'<div i="'+field.id+'" class="field '+field.type+' n'+field.id+'">'

				// fabrik
				if (field.type == 'fabrik')
				{
		      field.field = this.getActor({
						group: 'field',
						name: field.bname,
						value: field.value,
						opts: {
							options: field.options,
							name: field.name,
							isedit: true
						}
					})

		      if (field.issearch)
		      {
		      	field.field.opts.searchPrams = {filterid: this.id, fieldid: field.id}
		      	field.field.opts.onSearch = (field, value) => this.search(field, value)
		      }

		      html += 
						(!notlabel ? '<div class="label">'+field.label+':</div>' : '')+
						'<div class="control">'+
							App.render(field.field)+
						'</div>'
				}

				// input
				else if (field.type == 'input')
				{
		      field.field = this.getActor({
						group: 'field',
						name: 'field',
						value: (field.value ? field.value : ''),
						opts: {
							name: field.name,
							placeholder: get(field, '', 'placeholder'),
							isedit: true
						}
					})

		      html += 
						(!notlabel ? '<div class="label">'+field.label+':</div>' : '')+
						'<div class="control">'+
							App.render(field.field)+
						'</div>'
				}

				// date
				else if (field.type == 'date')
				{
		      field.field = this.getActor({
						group: 'field',
						name: 'date',
						value: (field.value ? field.value : ''),
						opts: {
							name: field.name,
							placeholder: get(field, '', 'placeholder'),
							isedit: true,
							onApply: (field, ev, picker) => self.apply(field.options),
							onClear: (field) => self.apply(field.options)
						}
					})

		      html += 
						(!notlabel ? '<div class="label">'+field.label+':</div>' : '')+
						'<div class="control">'+
							App.render(field.field)+
						'</div>'
				}

				// add
				else if (field.type == 'add')
				{
					html += 
						'<div class="button">'+
			 				'<a href="#" class="b b-c add"><i class="fa fa-plus"></i></a>'+
			 			'</div>'
				}

				html += 
					'</div>'
      }

			return html
		},

		search: function(field, value)
		{
			this.ajax({
				data: {
        	task: 'filter.search',
        	branch: 'fabrik',
          value: value,
          fieldid: field.opts.searchPrams.fieldid,
          filterid: field.opts.searchPrams.filterid
				},
        success: function(data)
        {
					field.opts.options = data.options
					field.value = ''
					
					field.updateRender(field, true)
        }
			})
		},

		onAfterRender: function()
		{
			let self = this

			this.controlElements()
			this.clear()
		},

		clear: function()
		{
			let self = this

			this.node.find('.filter-clear').click(function()
			{
				self.opts.fields.map(field =>
				{
					if (field.type == 'date')
						field.field.val('')
					else if (field.type == 'fabrik')
					{
						self.node.find('.forminput.'+field.name).val('')
						self.node.find('.forminput.'+field.name).select2()
						
					}
					else
						self.node.find('.forminput.'+field.name).val('')
				})

				App.setDataStream('modulesData.'+self.id+'.isclear', 1)
				self.apply()
			})
		},

		controlElements: function()
		{
			let self = this

			this.node.find('.field').each(function()
			{
				let id = $(this).attr('i'),
						opt = self.getField(id),
						method = 'control_'+opt.type

				self[method](opt, this)
			})
		},

		control_add: function(opt, node)
		{
			$(node).find('a').click(function(e)
			{
				e.preventDefault()
				App.modules[opt.moduleid].block.add()
			})
		},

		control_fabrik: function(opt, node)
		{
			var self = this

			// apply
			if (opt.isapply)
			{
				$(node).find('.control > select').change(function()
				{
					self.applyField = opt
					self.apply(opt)
				})
			}

			if (opt.parentsField)
			{
				opt.parentsField.map(function(fieldid)
				{
					self.node.find('.fabrik.field.n'+fieldid).change(function()
					{
						self.setStream()

						self.ajax({
							data: {
			        	task: 'filter.getOptions',
			        	branch: 'fabrik',
			          filterid: self.id,
			          fieldid: opt.id
							},
			        success: function(data)
			        {
			        	let field = self.getField(opt.id)

								field.field.opts.options = data.options
								field.field.updateRender(field, true)
			        }
						})
					})
				})
			}
		},

		control_input: function(opt, node)
		{
			let self = this

  		$(node).find('.forminput').keyup(function(e)
	  	{
	  		let value = $.trim($(this).val())

	  		if (e.keyCode === 13)
	  		{
	  			self.apply(opt)
	  		}
	  	})
		},

		control_date: function(opt, node)
		{
			let self = this

  		$(node).find('.forminput').keyup(function(e)
	  	{
	  		let value = $.trim($(this).val())

	  		if (e.keyCode === 13)
	  		{
	  			self.apply(opt)
	  		}
	  	})
		},

		apply: function(opt)
		{
			let relatedModules = [],
					modulesData = {}

			this.applyField = opt

			this.opts.relatedModules.map(function(row)
			{
				if (row.direct)
					relatedModules.push(row.id)
			})

			this.setStream()

			this.ajax({
				data: {
        	task: 'filter.apply',
        	branch: 'fabrik',
          relatedModules: relatedModules,
          moduleid: this.id
				}
			})
		},

		renderClearButton: function()
		{
			let html =
						'<button class="b b-s b-st-inp b-primary filter-clear">Очистить</button>'

			return html
		},

    setStream: function(filter)
    {
      App.setDataStream('modulesData.'+this.id+'.fields', this.getFieldsData())
    },

		getFieldsData: function()
		{
			let self = this,
					data = {}

			this.opts.fields.map(function(field)
			{
				let input

				if (field.type != 'add')
				{
					if (field.type == 'date')
					{
						data[field.id] = field.field.value
					}
					else
					{
						input = self.node.find('.field.n'+field.id+' .control .forminput')
						data[field.id] = input.val()
					}
				}
			})

			return data
		},

		getField: function(key)
		{
			let field,
					isnumeric = $.isNumeric(key)

			this.opts.fields.map(function(item) 
			{
				if (isnumeric && item.id == key)
					field = item
				else if (!isnumeric && item.name == key)
					field = item
			})

			return field
		}
  })
})