define(function(require) 
{
	let Obj = require('lib/bs/object/object')

  return new Class(
  {
  	Extends: Obj,

  	type: 'plugin',

		space: null,
		isglobal: false,
    plgtype: null,
    format: null,

    obj: null,
    sobj: null,

		ajax: function(obj)
		{
			obj.data = Object.assign(obj.data, {
        option: 'system',
        task: 'ajax',
        exttype: 'plugin',

				space: getfu(this.space),
				isglobal: getfu(this.isglobal),
				group: get(obj, this.group, 'group'),
				type: get(obj, this.plgtype, 'plgtype'),
				name: get(obj, this.name, 'name'),
				format: get(obj, getfu(this.format), 'format'),

				method: obj.method
			})

			App.ajax(obj)
		}
  })
})
