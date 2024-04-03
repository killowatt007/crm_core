define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
		Extends: Plugin,

		onBeforeRenderAddon: function(page, addon)
		{
			this.renderFilter(page, addon)
		},

		renderFilter: function(page, addon)
		{
		  if (addon.name == 'module')
		  {
		  	// console.log('1 - '+addon.opts.branch);
			  if (addon.opts.branch == 'fabrik.filter')
			  {
					let module = addon.getAddonActor(),
							client = module.getField('client'),
							staff = module.getField('staff'),
							priority = module.getField('priority'),
							status = module.getField('status'),
							date_from = module.getField('date_from'),
							date_to = module.getField('date_to'),
							contract = module.getField('contract')

					if (App.ismobile)
					{
						module.html =
							'<div class="row">'+
							  '<label class="col-24">'+contract.label+'</label>'+
							  '<div class="col-24 control fio">'+
									module.renderField(contract, true)+
							  '</div>'+
							'</div>'+
							'<div class="row">'+
							  '<label class="col-24">'+client.label+'</label>'+
							  '<div class="col-24 control fio">'+
									module.renderField(client, true)+
							  '</div>'+
							'</div>'+
							'<div class="row">'+
							  '<label class="col-24">'+staff.label+'</label>'+
							  '<div class="col-24 control id">'+
									module.renderField(staff, true)+
							  '</div>'+
							'</div>'+
							'<div class="row">'+
								'<div class="col-12">'+
									'<div class="row">'+
									  '<label class="col-24">'+status.label+'</label>'+
									  '<div class="col-24 control value contractid">'+
									  	module.renderField(status, true)+
									  '</div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-24">'+priority.label+'</label>'+
									  '<div class="col-24 control value contractid">'+
									  	module.renderField(priority, true)+
									  '</div>'+
									'</div>'+
								'</div>'+
								'<div class="col-12">'+
									'<div class="row">'+
									  '<label class="col-24">'+date_from.label+'</label>'+
									  '<div class="col-24 control value contractid">'+
									  	module.renderField(date_from, true)+
									  '</div>'+
									'</div>'+
									'<div class="row">'+
									  '<label class="col-24">'+date_to.label+'</label>'+
									  '<div class="col-24 control value contractid">'+
									  	module.renderField(date_to, true)+
									  '</div>'+
									'</div>'+
								'</div>'+
							'</div>'
					}
					else
					{
						module.html = 
							'<div class="row">'+
								'<div class="col-7">'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-5 col-form-label">'+contract.label+'</label>'+
									  '<div class="col-sm-16 control fio">'+
											module.renderField(contract, true)+
									  '</div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-5 col-form-label">'+client.label+'</label>'+
									  '<div class="col-sm-16 control fio">'+
											module.renderField(client, true)+
									  '</div>'+
									'</div>'+
									(staff.display ?
									'<div class="mb-8 row">'+
									  '<label class="col-sm-5 col-form-label">'+staff.label+'</label>'+
									  '<div class="col-sm-16 control id">'+
											module.renderField(staff, true)+
									  '</div>'+
									'</div>'
									: '')+
							  '</div>'+
								'<div class="col-7">'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-6 col-form-label">'+status.label+'</label>'+
									  '<div class="col-sm-16 control id">'+
											module.renderField(status, true)+
									  '</div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-6 col-form-label">'+priority.label+'</label>'+
									  '<div class="col-sm-16 control fio">'+
											module.renderField(priority, true)+
									  '</div>'+
									'</div>'+
							  '</div>'+
								'<div class="col-7">'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-7 col-form-label">Дата С</label>'+
									  '<div class="col-sm-15 control fio">'+
											module.renderField(date_from, true)+
									  '</div>'+
									'</div>'+
									'<div class="mb-8 row">'+
									  '<label class="col-sm-7 col-form-label">Дата По</label>'+
									  '<div class="col-sm-15 control fio">'+
											module.renderField(date_to, true)+
									  '</div>'+
									'</div>'+
							  '</div>'+
								'<div class="col-3">'+
									'<div class="mb-8 row">'+
									  '<div class="col-sm-25 control fio">'+
											module.renderClearButton()+
									  '</div>'+
									'</div>'+
							  '</div>'+
							'</div>'
					}
			  }
		  }
		}
  })
})