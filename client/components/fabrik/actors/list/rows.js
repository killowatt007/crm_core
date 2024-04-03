define(function(require) 
{
	let Actor = require('components/fabrik/actor')

  return new Class(
  {
  	Extends: Actor,

  	opts: {
  		actions: []
  	},

  	fields: {},

		render: function()
		{
			let odd = true,
					html = ''

			if (this.opts.rows.length)
			{
				let r_length = this.opts.rows.length

				html =
					this.opts.fieldsgroup.map((fields, i) =>
					{
						let classname = odd ? 'odd' : '',
								islast = ((r_length-1) == i),
								cn_tr = islast ? 'last' : '',
								args = {html: ''}

								args.html = 
									'<tr class="'+this.opts.tr_class+' '+cn_tr+' '+classname+'" key="'+i+'" rowalias="'+this.opts.alias+'" rowid="'+this.opts.rows[i].id+'">'+
										this.renderRow(i, fields)+
										this.renderActions(i)+
									'</tr>'

								this.opts.list.getPluginManager().run('rowsAfterRowRender', [this, i, args])

						odd = !odd
						return args.html
					}).join('')
			}
			else
			{
				html =
					'<tr>'+
						'<td class="norecord" colspan="'+(this.opts.colspan)+'">Нет записей</td>'+
					'</tr>'
			}

			return html
		},

		renderActions: function(i)
		{
			let html = '',
					args = {
						actions: this.opts.actions
					}

      // this.opts.list.getPluginManager().run('beforeAction', [this, i, args])

			if (args.actions.length)
			{
				html +=
					'<td class="actions">'+
						args.actions.map(action => 
						{
							return '<i class="'+action.icon+' '+get(action, '', 'classes')+' '+action.name+'"></i>'
						}).join('')+
					'</td>'
			}

			return html
		},

		renderRow: function(rowi, fields)
		{
			let self = this,
					size = fields.length,
					html = 
						fields.map((field, i) =>
						{
							let object = this.getElement(rowi, field.data),
									value = App.render(object),
									colspan = size==(i+1) ? 'colspan="'+this.opts.left_colspan+'"' : ''

							return '<td '+colspan+'>'+value+'</td>'
						}).join('')

			return html
		},

		getElement: function(i, key)
		{
			let data

			if (typeof key != 'object')
			{
				this.opts.fieldsgroup[i].map(fieldData =>
				{
					if (fieldData.data.opts.name == key)
						data = fieldData.data
				})
			}
			else
			{
				data = key
			}

			if (!this.fields[i])
				this.fields[i] = {}

			if (!this.fields[i][data.id])
			{
				data.opts.rowsalias = this.opts.alias

				this.fields[i][data.id] = this.getActor(data)
				this.fields[i][data.id].getPluginManager().setObserveForObj(this.opts.list, {format: 'list', prefix: 'element'})
			}

			return this.fields[i][data.id]
		}
  })
})
