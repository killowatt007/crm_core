define(function(require) 
{
	/**
	 * $version 1.1
	 */

	let View = require('view')

	require('components/field/actors/field')
	require('components/field/actors/list')

  return new Class(
  {
  	Extends: View,

		render: function()
		{
			let html = '',
					fdebt = this.getActor({
	          group: 'field',
	          name: 'field',
	          value: '',
	          opts: {
	            name: 'debt',
	            isedit: true
	          }
	        }),
      		fstatus = this.getActor({
            group: 'field',
            name: 'list',
            // value: this.opts.display,
            opts: {
              options: [
              	{value:2 ,label:'Активный'},
              	{value:6 ,label:'Отключен'},
              	{value:-1 ,label:'Все'}
              ],
              name: 'status',
              isedit: true,
              isps: false,
              minimumResultsForSearch: -1
            }
          }),
      		fbase = this.getActor({
            group: 'field',
            name: 'list',
            // value: this.opts.display,
            opts: {
              options: [
              	{value:-1 ,label:'Все'},
              	{value:48 ,label:'Севастополь'},
              	{value:47 ,label:'Симферополь'}
              ],
              name: 'base',
              isedit: true,
              isps: false,
              minimumResultsForSearch: -1
            }
          })

	    this.list = new App.dep['components/fabrik/actors/list'](this.opts.list)

			html +=
				'<div id="'+this.key+'" class="analytics_dash">'+
					'<h4 style="margin-bottom:17px;">Общая информация</h4>'+
					'<div class="row">'+
						'<div class="col">'+
							'<div style="font-weight:600;margin-bottom:5px;">'+
								(App.ismobile ? 'Колличество' : 'Колличество абонентов')+':'+
							'</div>'+
							'<ul>'+
								'<li>'+this.getText('Севастополь', this.opts.l_sev)+'</li>'+
								'<li>'+this.getText('Симферополь', this.opts.l_sim)+'</li>'+
								'<li style="margin-top:5px;">'+this.getText('Всего', this.opts.l_all)+'</li>'+
							'</ul>'+
						'</div>'+
						'<div class="col">'+
							'<div style="font-weight:600;margin-bottom:5px;">'+
								(App.ismobile ? 'Отключенные' : 'Отключенные абонентов')+':'+
							'</div>'+
							'<ul>'+
								'<li>'+this.getText('Севастополь', this.opts.d_sev)+'</li>'+
								'<li>'+this.getText('Симферополь', this.opts.d_sim)+'</li>'+
								'<li style="margin-top:5px;">'+this.getText('Всего', this.opts.d_all)+'</li>'+
							'</ul>'+
						'</div>'+
						'<div class="col">'+
							'<div style="font-weight:600;margin-bottom:5px;">'+
								(App.ismobile ? 'Долг' : 'Сумма долга активных абонентов')+':'+
							'</div>'+
							'<ul>'+
								'<li>'+this.getText('Севастополь', this.opts.all_left.sev)+'</li>'+
								'<li>'+this.getText('Симферополь', this.opts.all_left.sim)+'</li>'+
								'<li style="margin-top:5px;">'+this.getText('Всего', this.opts.all_left.all)+'</li>'+
							'</ul>'+
						'</div>'+
					'</div>'+
					'<h4 style="margin:20px 0 17px 0;">Абоненты по сумме долга</h4>'+
					'<div>'+
						'<div class="row">'+
							'<div class="col-sm-24 col-xs-24">'+
								'<div class="fabrik filter">'+
									'<div class="field fabrik">'+
										'<div class="label">Сумма долга больше:</div>'+
										'<div class="control">'+
											App.render(fdebt)+
										'</div>'+
									'</div>'+
									'<div class="field fabrik">'+
										'<div class="label">Статус:</div>'+
										'<div class="control">'+
											App.render(fstatus)+
										'</div>'+
									'</div>'+
									'<div class="field fabrik">'+
										'<div class="label">База:</div>'+
										'<div class="control">'+
											App.render(fbase)+
										'</div>'+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>'+
					'<div style="margin-top:10px;">'+
						'<span class="debtSum">'+
							(App.ismobile ? 'Сумма' : 'Общая сумма долга')+': '+
							'<span class="value" style="font-weight:500;">0</span>'+
						'</span>'+
						'<span class="debt_l" style="margin-left:20px;">'+
							(App.ismobile ? 'Колл-во' : 'Колл-во абонентов')+': '+
							'<span class="value" style="font-weight:500;">0</span>'+
						'</span>'+
					'</div>'+
					App.render(this.list)+
				'</div>'

			return html
		},

		getText: function(label, value)
		{
			let html = ''

			html += label
			html += App.ismobile ? '<br>' : ' - '
			html += '<span style="font-weight:500;">'+value+'</span>'
			
			return html
		},

		onAfterRender: function()
		{
			let self = this

			this.node.find('.forminput.debt').keyup(function(e)
	  	{
	  		if (e.keyCode === 13)
	  			self.updateList()
	  	})

			this.node.find('.forminput.status').change(() => self.updateList())
			this.node.find('.forminput.base').change(() => self.updateList())
		},

		updateList: function()
		{
			let self = this,
					debt = this.node.find('.forminput.debt').val(),
					status = this.node.find('.forminput.status').val(),
					base = this.node.find('.forminput.base').val()

			App.setDataStream('analytics', {
				debt: debt,
				status: status,
				base: base
			})

      this.ajax({
        data: {
          option: 'module',
          task: 'analytics_dash.get_list_data',
          branch: 'analytics_dash'
        },
        success: function(data)
        {
        	self.node.find('.debtSum .value').text(data.allData.debtSum)
        	self.node.find('.debt_l .value').text(data.allData.debt_l)
        	
         	self.list.updateRender(data.list)
        }
      })
		}
  })
})
