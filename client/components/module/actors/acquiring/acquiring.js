define(function(require) 
{
	let View = require('view')

	// require('components/field/actors/list')
	require('components/field/actors/date')
	require('components/field/actors/list')

  return new Class(
  {
  	Extends: View,

		date: null,

		render: function()
		{
			let html = ''

			html +=
				'<div id="'+this.key+'" class="acquiring">'+
					this.renderFilter()+
					'<div class="container-data"></div>'+
				'</div>'

			return html
		},

		renderFilter: function()
		{
			let html = ''

      this.date = this.getActor({
        group: 'field',
        name: 'date',
        opts: {
          name: 'date',
          isedit: true
        }
      })

			html =
				'<div style="margin-bottom:20px;">'+
					'<div class="row">'+
						'<div class="col-sm-24 col-xs-24">'+
							'<div class="fabrik filter">'+
								'<div class="field fabrik">'+
									'<div class="label">Дата</div>'+
									'<div class="control">'+
										App.render(this.date)+
									'</div>'+
								'</div>'+
								'<button class="b b-s b-st-inp b-primary apply">Показать</button>'+
							'</div>'+
						'</div>'+
					'</div>'+
				'</div>'

			return html
		},

		onAfterRender: function()
		{
			let self = this

			this.node.find('.apply').click(function()
			{
				self.ajax({
					data: {
	        	task: 'acquiring.test',
	        	branch: 'acquiring',
	        	date: self.date.getCurrentValue()
					},
	        success: function(data)
	        {
	        	let list = self.subconst('list', {
					      	label: null,
					      	headers: data.headers,
					      	rows: data.rows,

					      	actions: [],
					      	buttons: [],
					      	tr_class: 'rw data-row'
								})

						self.node.find('.container-data').html(list.render())
	        }
				})
			})
		},

    list: 
    {
      opts: {
      	label: '',
      	actions: [],
      	headers: [],
      	rows: [],
      	buttons: [],

      	tr_class: ''
      },

      render: function()
      {
      	let html = '76'

				html = 
					'<div class="fabrik list">'+
						this.renderLabel()+
						'<div class="data">'+
							this.renderTable()+
					  '</div>'+
					  // this.renderFooter()+
				  '</div>'

      	return html
      },

			renderTable: function()
			{
				let html = ''

				html = 
					'<table>'+
						this.renderHeader()+
						this.renderBody()+
					'</table>'

				return html
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
								let html = '<th>'+header+'</th>'

								return html;
							}).join('')+
							// '<th class="buttons" '+colspan+'>'+
							// 	this.renderButtons()+
							// '</th>'
						'</tr>'+
					'</thead>'

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
				let odd = true,
						html = ''

				if (this.opts.rows.length)
				{
					let r_length = this.opts.rows.length

					html =
						this.opts.rows.map((row, i) =>
						{
							let classname = odd ? 'odd' : '',
									islast = ((r_length-1) == i),
									cn_tr = islast ? 'last' : '',
									html = ''

									html = 
										'<tr class="'+this.opts.tr_class+' '+cn_tr+' '+classname+'" key="'+i+'">'+
											this.renderRow(i, row)+
											// this.renderActions(i)+
										'</tr>'

							odd = !odd
							return html
						}).join('')
				}
				else
				{
					html =
						'<tr>'+
							'<td class="norecord" colspan="2">Нет записей</td>'+
						'</tr>'
				}

				return '<tbody>'+html+'</tbody>'
			},

			renderRow: function(rowi, row)
			{
				let self = this,
						html = ''

				$.each(row, (key, value) =>
				{
					html += '<td>'+value+'</td>'
				})

				return html
			},

			// renderActions: function(i)
			// {
			// 	let html = ''

			// 	if (this.opts.actions.length)
			// 	{
			// 		html +=
			// 			'<td class="actions">'+
			// 				this.opts.actions.map(action => 
			// 				{
			// 					return '<i class="'+action.icon+' '+get(action, '', 'classes')+' '+action.name+'"></i>'
			// 				}).join('')+
			// 			'</td>'
			// 	}

			// 	return html
			// }
    }
  })
})
