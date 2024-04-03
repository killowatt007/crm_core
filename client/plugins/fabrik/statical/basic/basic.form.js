define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
		Extends: Plugin,

		onElementGetValue: function()
		{
			let obj = this.obj, sobj = this.sobj,
					i = sobj.opts.i,
					elname = sobj.opts.name,
					data = get(obj.opts, [], 'rows.'+i)

      if (sobj.opts.type == 'databasejoin' && !sobj.opts.isedit)
        sobj.value = data[elname+'_join'] ? data[elname+'_join'] : ''
      else
        sobj.value = data[elname] ? data[elname] : ''

      if (!sobj.opts.isedit && (obj.opts.editElementid == sobj.id))
        sobj.value = '<a href="#" class="edit">'+sobj.value+'</a>'
		},

    onBeforeOpenPopup: function() {this.streamData()},
    onBeforeSubmit: function() {this.streamData()},

    streamData: function()
    {
      let data = this.obj.opts,
      		moduleid = get(data, null, 'moduleid')

      if (!moduleid)
        moduleid = get(data, null, 'modulerefid')

      if (moduleid)
        App.setDataStream('modulerefid', moduleid)
    }
  })
})