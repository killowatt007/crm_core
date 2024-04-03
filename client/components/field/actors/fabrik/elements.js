define(function(require) 
{
	let List = require('components/field/actors/list')

  return new Class(
  {
  	Extends: List,

		onAfterRender: function()
		{
			let self = this,
					parent = this.node.parents('.params').find('.forminput.'+this.opts.table)

			parent.change(function()
			{
				let entityid = parent.val()

				if (entityid)
				{
					self.ajax({
						// async: false,
						data: {
		          option: 'field',
		          branch: 'fabrik',
		          task: 'elements.getFields',

		          entityid: entityid
						},
						success: function(data)
						{
							self.opts.options = data.options
							self.updateRender(self, true)
						}
					})
				}
			}).change()
		}
  })
})