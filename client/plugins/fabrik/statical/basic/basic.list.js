define(function(require) 
{
	let Plugin = require('components/fabrik/event/plugin')

	return new Class(
	{
		Extends: Plugin,

		onBeforeUpdate: function()
		{
			let obj = this.obj

			App.setDataStream('modulesData.'+obj.opts.moduleid, {
				display: obj.node.find('.list-footer .forminput.display').val(),
				pagination: obj.node.find('.list-footer .forminput.pagination').val()
			})
		},

		onElementGetValue: function()
		{
			let obj = this.obj, sobj = this.sobj,
					elname = sobj.opts.name,
					row = this.getRow()

			sobj.getPluginManager().run('basicBeforeGetValue')

			if (sobj.value === null)
			{
				if (sobj.opts.type == 'databasejoin' && !sobj.opts.isedit)
					sobj.value = row[elname+'_join']
				else
					sobj.value = row[elname]
			}

			// edit element
			if (sobj.id == obj.opts.editElementid)
			{
				sobj.value = '<a href="#" class="edit">'+sobj.value+'</a>'
			}
		},

    onBeforeOpenPopup: function() {this.streamData()},

    streamData: function()
    {
      let data = this.obj.opts,
      		moduleid = get(data, get(data, null, 'modulerefid'), 'moduleid')
      		
      if (moduleid)
        App.setDataStream('modulerefid', moduleid)
    }
	})
})