define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  require('components/fabrik/actors/list')

  return new Class(
  {
		Extends: Plugin,

		list: null,

		onAfterObjsRender: function()
		{
			// redirect
			let fdata = this.obj.opts.fabrik

      if (fdata)
      {
      	if (!this.list)
      	{
				  this.list = this.getActor({
						id: fdata.entity,
						group: 'fabrik',
						name: 'list',
						data: {
						  tablename: 'request_items'
						}
				  }) 			

				  this.list.openPopup(fdata.rowid)	
      	}
      }
		}
  })
})