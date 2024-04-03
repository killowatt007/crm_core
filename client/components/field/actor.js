define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,

  	value: null,
    pplg: null,

  	getValue: function()
  	{
  		if (this.value === null)
  			this.getPluginManager().run('getValue')
  		
  		return this.value
  	},

    renderRO: function()
    {
      return this.getValue()
    },

    getName: function()
    {
      let name = ''

      get(this.opts, [], 'names').map((part, i) => { name += (!i) ? part : '['+part+']' })
      name += name ? '['+this.opts.name+']' : this.opts.name

      return name
    },

    setValueToObject: function(object, value)
    {
      this.opts.names.map((name, i) => 
      {
        if (!object[name])
          object[name] = {}

        object = object[name]
      })

      object[this.opts.name] = value
    },

    getClasses: function()
    {
      let className

      className = this.opts.name.replace(/\]\[/g, ' ')
      className = className.replace('[', ' ')
      className = className.replace(']', '')

      return className
    },

    getPplg: function()
    {
      if (!this.pplg)
      {
        let obj = this

        while (this.pplg === null) 
        {
          if (obj.type == 'plugin')
            this.pplg = obj
          else
            obj = obj.po
        }
      }

      return this.pplg
    },

    getCurrentValue: function()
    {
      let input
      
      if (this.node.hasClass('form-control'))
        input = this.node
      else
        input = this.node.find('.form-control')

      return input.val()
    }
  })
})
