define(function(require) 
{
  let Plugin = require('plugin')

  return new Class(
  {
    Extends: Plugin,

		getRow: function()
		{
			let data, obj = this.obj, sobj = this.sobj,
					i = sobj.opts.i

			data = obj.rows[sobj.opts.rowsalias].opts.rows[i]

			return data
		},
  })
})
