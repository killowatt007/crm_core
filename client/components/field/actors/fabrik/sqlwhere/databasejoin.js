define(function(require) 
{
	let List = require('components/field/actors/list')

  return new Class(
  {
  	Extends: List,

  	params: null,

		onAfterRender: function()
		{
			let self = this

			// value
			this.node.change(function()
			{
				let type = $(this).val(),
						container = self.po.po.currentPopup.node.find('.params-aftertype')

				if (type)
				{
					self.ajax({
						async: false,
						data: {
							task: 'databasejoin.valueParams',
							branch: 'fabrik.sqlwhere',

							ptype: type,
							extradata: self.po.po.getExtraData()
						},
						success: function(data)
						{
			        let html = ''
			            
			        self.params = self.getActor(data.params, {data: self.getFormData()})
			        container.html(App.render(self.params))
						},
						afterSuccess: function()
						{
							self.afterRenderValue()
						}
					})
				}
				else
				{
					container.html('')
				}
			}).change()
		},

		afterRenderValue: function()
		{
			let self = this

			// Filter
			this.po.po.currentPopup.node.find('select.filterid').change(function()
			{
				let filterid = $(this).val()

				if (filterid)
				{
					self.ajax({
						async: false,
						data: {
							task: 'databasejoin.getFilterFields',
							branch: 'fabrik.sqlwhere',

							filterid: filterid
						},
						success: function(data)
						{
							self.params.fields.filter_fieldid.opts.options = data.options
							self.params.fields.filter_fieldid.updateRender(self.params.fields.filter_fieldid, true)
						}
					})
				}
			}).change()

			// Filter Field
			this.po.po.currentPopup.node.find('select.filter_fieldid').change(function()
			{
				let filter_fieldid = $(this).val()

				if (filter_fieldid)
				{
					self.ajax({
						async: false,
						data: {
							task: 'databasejoin.getEntityFields',
							branch: 'fabrik.sqlwhere',

							filter_fieldid: filter_fieldid
						},
						success: function(data)
						{
							self.params.fields.entity_fieldid.opts.options = data.options
							self.params.fields.entity_fieldid.updateRender(self.params.fields.entity_fieldid, true)
						}
					})
				}
			}).change()
		},

		getFormData: function()
		{
			return this.po.po.getParams(this.po.po.currentBlock)
		}
  })
})