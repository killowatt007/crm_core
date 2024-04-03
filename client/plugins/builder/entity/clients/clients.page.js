define(function(require) 
{
	/**
	 * $version 1.1
	 */

  let Plugin = require('components/fabrik/event/plugin')

  require('components/fabrik/actors/list')
  require('components/domofon/actors/invoice/quarter')

  return new Class(
  {
		Extends: Plugin,

		clientlist: null,
		clienrID: null,

		first: true,

		onAfterSuccess: function(resData, reqData)
		{
			this.afterSubmit(resData, reqData)
			this.applyFilter(resData, reqData)

			/*temp!!!*/
			if (reqData.isWindow)
			{
				App.modules[31].opts.fields[1].field.node.change()
			}
		},

		onBeforeRenderAddon: function(page, addon)
		{
			this.renderFilter(page, addon)
			this.renderActions(page, addon)
		},

		onBeforeSend: function(reqData)
		{
			if (reqData.branch == 'fabrik' && reqData.task == 'filter.apply')
			{
				App.setDataStream('pageData.applyFieldName', App.modules[31].applyField.name)
			}
		},

		renderFilter: function(page, addon)
		{
		  if (addon.name == 'module')
		  {
			  if (addon.opts.branch == 'fabrik.filter')
			  {
					let module = addon.getAddonActor()

					if (App.ismobile)
					{
						module.html = 
							'<div class="row">'+
							  '<label class="col-6">ФИО</label>'+
							  '<div class="col-18 value fio">'+
									module.renderField(module.getField('fio'), true)+
							  '</div>'+
							'</div>'+
							'<div class="row">'+
							  '<label class="col-6">Счет</label>'+
							  '<div class="col-18 value id">'+
									module.renderField(module.getField('id'), true)+
							  '</div>'+
							'</div>'+
							'<div class="row">'+
							  '<label class="col-6">Примечания</label>'+
							  '<div class="col-18 value note">'+
							  	'<textarea class="form-control" placeholder="Примечания" style="font-weight:500;"></textarea>'+
					        '<i class="fal fa-save save"></i>'+
					        '<i class="fal fa-question-circle faq"></i>'+
							  '</div>'+
							'</div>'+
							'<div class="row">'+
							  '<label class="col-6">Адрес</label>'+
							  '<div class="col-18 value address"></div>'+
							'</div>'+

							'<div class="row">'+
								'<div class="col-12">'+
									'<div class="row">'+
									  '<label class="col-12">Договор</label>'+
									  '<div class="col-12 control value contractid"></div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-12">Абонплата</label>'+
									  '<div class="col-12 control value rate"></div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-12">Статус</label>'+
									  '<div class="col-12 control value status"></div>'+
									'</div>'+
								'</div>'+
								'<div class="col-12">'+
									'<div class="row">'+
									  '<label class="col-12">Начислено</label>'+
									  '<div class="col-12 control value inv"></div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-12">Оплачено</label>'+
									  '<div class="col-12 control value pay"></div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-12">Баланс</label>'+
									  '<div class="col-12 control value left"></div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-12">До 31.12</label>'+
									  '<div class="col-12 control value leftForYear"></div>'+
									'</div>'+
								'</div>'+
							'</div>'
					}
					else
					{
						module.html = 
							'<div class="row">'+
								'<div class="col">'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-8 col-form-label">ФИО</label>'+
									  '<div class="col-sm-12 control value fio">'+
											module.renderField(module.getField('fio'), true)+
									  '</div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-8 col-form-label">Счет</label>'+
									  '<div class="col-sm-12 control value id">'+
											module.renderField(module.getField('id'), true)+
									  '</div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-8 col-form-label">Адрес</label>'+
									  '<div class="col-sm-12 control value address"></div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-8 col-form-label">Договор</label>'+
									  '<div class="col-sm-12 control value contractid"></div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-8 col-form-label">Абонплата</label>'+
									  '<div class="col-sm-12 control value rate"></div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-8 col-form-label">Статус</label>'+
									  '<div class="col-sm-12 control value status"></div>'+
									'</div>'+
							  '</div>'+
								'<div class="col">'+
								  '<div class="mb-8 row">'+
										'<label class="col-sm-8 col-form-label">Начислено</label>'+
										'<div class="col-sm-12 control value inv"></div>'+
								  '</div>'+
								  '<div class="mb-8 row">'+
										'<label class="col-sm-8 col-form-label">Оплачено</label>'+
										'<div class="col-sm-12 control value pay"></div>'+
								  '</div>'+
								  '<div class="mb-8 row">'+
										'<label class="col-sm-8 col-form-label">Баланс</label>'+
										'<div class="col-sm-12 control value left"></div>'+
								  '</div>'+
								  '<div class="mb-8 row">'+
										'<label class="col-sm-8 col-form-label">До 31.12</label>'+
										'<div class="col-sm-12 control value leftForYear"></div>'+
								  '</div>'+
								  '<div class="mb-8 row">'+
										'<div class="col-sm-24 control value note">'+
									  	'<textarea class="form-control" placeholder="Примечания" style="font-weight:500;"></textarea>'+
							        '<i class="fal fa-save save"></i>'+
							        '<i class="fal fa-question-circle faq"></i>'+
									  '</div>'+
									'</div>'+
								'</div>'+
							'</div>'
					}
			  }
		  }
		},

		afterSubmit: function(resData, reqData)
		{
			if (get(reqData, '', 'option') == 'fabrik' && get(reqData, '', 'task') == 'form.process')
			{
				App.modules[31].opts.fields[1].field.node.change()
			}
		},

		applyFilter: function(resData, reqData)
		{
			let self = this

			if (get(reqData, '', 'branch') == 'fabrik' && get(reqData, '', 'task') == 'filter.apply')
			{
				let data = get(resData, null, 'data.data.currentpage.plugin.clients')

				if (data)
				{
					let filter = $('.fabrik.filter')

					$.each(data.data, function(key, value)
					{
						filter.find('.value.'+key).html(value)
					})

					self.clienrID = data.id

					filter.find('.value.note textarea').val(data.note)

					filter.find('.value.id select').html('<option value="'+data.id+'" selected>'+data.id_l+'</option')
					filter.find('.value.fio select').html('<option value="'+data.id+'" selected>'+data.fio+'</option')
				}
			}
		},

		renderActions: function(page, addon)
		{
		  if (addon.name == 'title')
		  {
        let invoice_quarter = this.getActor(this.obj.opts.invoice.quarter)

				addon.opts.title += 
					'<div class="header-actions">'+
						App.render(invoice_quarter)+
						'<button class="b b-s b-primary editClient">'+
							'<i class="far fa-edit"></i>'+
							(!App.ismobile ? 'Редактировать Абонента' : '')+
						'</button>'+
						'<button class="b b-s b-success addClient">'+
							'<i class="far fa-plus-circle"></i>'+
							(!App.ismobile ? 'Добавить Абонента' : '')+
						'</button>'+
					'</div>'
		  }
		},

		onAfterObjsRender: function(resData, reqData)
		{
			if (reqData.isWindow)
			{
				this.addClient()
				this.editClient()	

				this.subconst('clientSaveNote')
			}
		},

    clientSaveNote: 
    {
      init: function()
      {
        let self = this

				$('.note i.save').click(function(e)
				{
					let value = $('.note textarea').val(),
							isactive = $('.note').hasClass('active')

					if (isactive)
						self.ajax(value)
				})

				$('.note textarea').keydown(function(e)
				{
					if (e.keyCode == 13 && e.shiftKey)
						this.isshift = true

					if (e.keyCode == 13 && !e.shiftKey)
					{
			  		e.preventDefault()
			  		return false
			 		}
				})

				$('.note textarea').keyup(function(e)
				{
					let value = $(this).val(),
							isactive = $('.note').hasClass('active')

					if (e.keyCode == 13 && !this.isshift)
					{
						this.isshift = false

						if (isactive)
							self.ajax(value)
					}
					else
					{
						$('.note').addClass('active')
					}

					if (e.keyCode == 13 && this.isshift)
						this.isshift = false
				})
      },

      ajax: function(value)
      {
				let clientid = $('.forminput.id').val()

				if (clientid)
				{
					this.po.ajax({
						group: 'fabrik',
						type: 'entity',
						name: 'clients',
						format: 'form',
					  method: 'saveNote',
					  data: {
							value: value,
							clientid: clientid
					  },
					  success: function(data)
					  {
					  	$('.note').removeClass('active')
					  }
					})
				}
      }
    },

		addClient: function()
		{
			let self = this

			$('.addClient').click(function()
			{
				self.getClientList().openPopup()
			})
		},

		editClient: function()
		{
			let self = this

			$('.editClient').click(function()
			{
				if (self.clienrID)
					self.getClientList().openPopup(self.clienrID)
			})
		},

		getClientList: function()
		{
			if (!this.clientlist)
			{
			  this.clientlist = this.getActor({
					id: 32,
					group: 'fabrik',
					name: 'list',
					data: {
					  tablename: 'clients'
					}
			  })
			}

			return this.clientlist
		}
  })
})