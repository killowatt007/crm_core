define(function(require) 
{
	let Actor = require('components/field/actor'),
			Sortable = require('lib/sortable.min'),
			HObject = require('lib/bs/helper/object')

  require('components/builder/actors/popup')

  return new Class(
  {
  	Extends: Actor,

  	currentPopup: null,
  	currentBlock: null,

  	typeParams: null,

  	adminlabels: [],

		render: function()
		{
			let self = this,
					html = '',
					pplg = this.getPplg()

			html =
				'<div id="'+this.key+'" class="sqlfilter">'+
					'<div class="cels">'+
						'<div class="inner">'+
						'</div>'+
						'<div class="plus-tab"><i class="fa fa-plus plus"></i></div>'+
					'</div>'+
				'</div>'

			pplg.obj.addEvent('afterFormData', function(data)
			{
				let params = []

				if (self.node)
				{
		      self.node.find('.cel').each(function()
		      {
		        params.push(JSON.parse($(this).find('> .params').text()))
		      })
				}

	      self.setValueToObject(data, params)
			})

			return html
		},

		getCell: function(params, i)
		{
			let classes = !params ? 'clear' : '',
					adminlabel = this.adminlabels[i] ? this.adminlabels[i] : '&nbsp;',
					html =
						'<div class="cel '+classes+'">'+
							'<div class="params">'+JSON.stringify(get(params, {}))+'</div>'+
							'<div class="admin-label">'+adminlabel+'</div>'+
							'<div class="cog horizont right">'+
								'<div class="inner">'+
									'<i class="fal fa-cog settings"></i>'+
									'<i class="fal fa-trash remove"></i>'+
								'</div>'+
							'</div>'+
						'</div>'

			return html
		},

		onAfterRender: function()
		{
			let pplg = this.getPplg()

			// if (pplg.opts.name == 'module')
			// {
				this.sortable()
				this.watchCog()

				if (this.value)
				{
					this.initAdminLabels(this.value)
					this.value.map((cel, i)=> this.node.find('.cels > .inner').append(this.getCell(cel, i)) )
				}
			// }
		},

		watchCog: function()
		{
			let self = this

			// plus
			this.node.find('.plus-tab').click(function()
			{
				self.node.find('.cels > .inner').append(self.getCell())
			})

			// remove
			this.node.on('click', '.remove', function()
			{
				$(this).parents('.cel:first').remove()
			})

			// settings
			this.node.on('click', '.settings', function()
			{
				let self2 = this

				self.ajax({
					data: {
						task: 'sqlwhere.popupParams',
						branch: 'fabrik'
					},
					success: function(data)
					{
		        let html = '',
		        		block = $(self2).parents('.cel'),
		            params = self.getActor(data.params, {
			  					data: self.getParams(block)
			  				}),
		            popup = self.getActor({
		              group: 'builder',
		              name: 'popup',
		              opts: {
			              label: 'Element',
			              width: 'litle',
			              afterRender: function(popup) {popup.plugin.afterPopap()}
		              }
		            })

		        html = 
		        	'<div class="params-modal">'+
		          	params.render()+
		          	'<div class="params-type" style="margin-top:20px;"></div>'+
		          	'<button type="submit" class="b b-s b-success apply">Apply</button>'+
		          '</div>'

		        self.currentPopup = popup
		        self.currentBlock = block

						popup.plugin = self
		        popup.opts.content = html
		        popup.open()
					}
				})
			})
		},

		afterPopap: function()
		{
			let self = this

			// type
			this.currentPopup.node.find('select.type').change(function()
			{
				let type = $(this).val(),
						container = self.currentPopup.node.find('.params-type'),
						task, branch

				if (type)
				{
					task = type == 'value' ? self.opts.flag+'.params' : 'sqlwhere.typeParams'
					branch = type == 'value' ? 'fabrik.sqlwhere' : 'fabrik'

					self.ajax({
						data: {
							task: task,
							branch: branch,

							ptype: type,
							extradata: self.getExtraData()
						},
						success: function(data)
						{
			        let html = ''

			        self.typeParams = self.getActor(data.params, {
								parent: self,
								data: self.getParams(self.currentBlock)
							})

			        html = 
		          	App.render(self.typeParams)+
		          	'<div class="params-aftertype" style="margin-top:20px;"></div>'

			        container.html(html)
						}
					})
				}
				else
				{
					container.html('')
				}
			}).change()

			// apply
			this.currentPopup.node.find('button.apply').click(function()
			{
        let params = HObject.inputsToObject(self.currentPopup.node.find('input, select, textarea'))

        self.initAdminLabels([params])

        $(self.currentBlock).removeClass('clear')
        $(self.currentBlock).find('.admin-label').html(self.adminlabels[0])
        $(self.currentBlock).find('> .params').text(JSON.stringify(params))
				self.currentPopup.close()
			})
		},

		getParams: function(block)
		{
			return JSON.parse($(block).find('> .params').text())
		},

		initAdminLabels: function(cels)
		{
			let self = this

			if (cels)
			{
				this.ajax({
					async: false,
					data: {
						task: 'sqlwhere.adminLabel',
						branch: 'fabrik',

						cels: cels,
						flag: this.opts.flag
					},
					test: 1,
					success: function(data)
					{
						self.adminlabels = data.labels
					}
				})
			}
		},

		sortable: function()
		{
      Sortable.create(this.node.find('.inner')[0], {
        // group: 'omega',
        // handle: '.drag',
        draggable: '.cel',
        animation: 150,
      })
		},

		getExtraData: function()
		{
			return this.po.opts.getExtraData()
		}
  })
})