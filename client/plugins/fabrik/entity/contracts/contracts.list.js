define(function(require) 
{
	let Plugin = require('components/fabrik/event/plugin')

	return new Class(
	{
		Extends: Plugin,

		onElementGetValue: function()
		{
			let obj = this.obj, sobj = this.sobj

			// ClientsBtn
			if (sobj.opts.name == 'ClientsBtn')
			{
				sobj.value = 
					'<div class="clients-btn">'+
						'<span>'+
							// '<i class="fad fa-spinner-third"></i>'+
							'<i class="fal fa-users user"></i>'+
							'<i class="fal fa-chevron-down arrow"></i>'+
						'</span>'+
					'<div>'
			}

			// SelectItem
			else if (sobj.opts.name == 'SelectItem')
			{
				sobj.value = 
					'<input type="checkbox" class="selectItem">'
			}
		},

		onAfterObjsRender: function()
		{
			this.subconst('showClients')
		},

    showClients: 
    {
     	init: function()
      {
        let self = this

        this.openClient()
				this.po.obj.node.find('.clients-btn span').click(function()
				{
					let btn = this,
							row = $(this).parents('tr:first'),
							contractid = row.attr('rowid'),
							isopen = $(this).hasClass('open'),
							isrender = $(this).hasClass('render')

					$(this).toggleClass('open')

					if (!isrender)
					{
		        self.po.ajax({
		          method: 'getClients',
		          format: 'form',
		          data: {
		          	contractid: contractid
		          },
		          success: function(data)
		          {
		          	let html =
							  			'<tr class="sub-rw clients open link-'+contractid+'">'+
									      '<td colspan="10">'+
													'<table>'+
									          '<thead>'+
									            '<tr>'+
									            	'<th style="'+(App.ismobile ? 'width:180px;' : '')+'">ФИО</th>'+
									              '<th style="min-width:70px;">Счет</th>'+
									              '<th>Баланс</th>'+
									              (App.ismobile ? '' : '<th>Телефон</th>')+
									              (App.ismobile ? '' : '<th>Мобильный</th>')+
									              '<th>Кв.</th>'+
									              (App.ismobile ? '<th></th>' : '<th>Статус</th>')+
									            '</tr>'+
									          '</thead>'+
									          '<tbody>'+
									          	data.clients.map(function(client)
									          	{
									          		let html = '',
									          				status = App.ismobile ? (client.StatusId == 2 ? '<i class="fa fa-solid fa-circle" style="color:#367D46;"></i>' : '<i class="fa fa-solid fa-circle" style="color:#ef3535;"></i>') : client.StatusId_j

									          		html  =
											            '<tr>'+
											              '<td>'+
											              	'<a href="/operator-clients?_ffilter[31][12]='+client.id+'&_ffilter[31][13]='+client.id+'" target="_blank" class="redirect_icon"><i class="fal fa-external-link"></i></a>'+
											              	'<a href="#" class="redirect" client-id="'+client.id+'">'+client.FIO+'</a>'+
											              '</td>'+
											              '<td>'+client.id+'</td>'+
											              '<td>'+client.balance+'</td>'

											          if (!App.ismobile)
											          {
												          html +=
												              '<td>'+client.Phone+'</td>'+
												              '<td>'+client.Mobile+'</td>'
											          }

											          html +=
											              '<td>'+client.FlatNumber+'</td>'+
											              '<td>'+status+'</td>'+
											            '</tr>'

													      return html
									          	}).join('')+
									          '</tbody>'+
								        	'</table>'+
									      '</td>'+
								      '</tr>'


								row.after(html)
								$(btn).addClass('render')
		          }
		        })
					}
					else
					{
						self.po.obj.node.find('.sub-rw.clients.link-'+contractid).toggleClass('open')
					}
				})
      },

      openClient: function()
      {
      	let self = this
      	// sub-rw clients
      	
      	this.po.obj.node.on('click', '.sub-rw .redirect', function(e)
    		{
    			e.preventDefault()

					let id = $(this).attr('client-id')

					App.window.open(75, { 
						args: {
							_ffilter: {31: {12:id,13:id}}
						},
						callback: ()=>
						{
							$(window).scrollTop(0);
							App.modules[5].setActive(75)
						}
					})
    		})
      }
    }
	})
})