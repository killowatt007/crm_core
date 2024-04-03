define(function(require) 
{
  return new Class(
  {
  	key: null,

    run: function(type, event, args)
    {
    	this.key = type
      this.import()

      App.plugins[this.key].map(function(plugin)
      {
        let method = 'on'+(event.charAt(0).toUpperCase()+event.slice(1))

        if (plugin[method])
        {
          plugin[method](...get(args, []))

          if (plugin.onAfterObjsRender)
          {
            if (App.pluginsAfterRender.indexOf(plugin) === -1)
              App.pluginsAfterRender.push(plugin)
          }
        }
      })
    },

    import: function()
    {
      let self = this

      if (!App.plugins[this.key])
      {
        App.plugins[this.key] = []

        $.each(App.dep, function(path, dep)
        {
          let pathArr = path.split('/'),
              space = null,
              isglobal = false,
              group,
              name

          // main
          if (pathArr[0] == 'plugins')
          {
            if (pathArr[1] == self.key)
            {
            	group = pathArr[1]
            	name = pathArr[2]
            }
          }

          // spaces global
          else if (pathArr[0] == 'spaces' && pathArr[3] == 'plugins')
          {
            if (pathArr[4] == self.key)
            {
            	space = pathArr[1]
            	isglobal = true
            	group = pathArr[4]
            	name = pathArr[5]
            }
          }

          if (group)
          	self.push(dep, group, name, space, isglobal)
        })
      }
    },

    push: function(dep, group, name, space, isglobal)
    {
      let plugin = new dep({
      			space: space,
      			isglobal: isglobal,
            group: group,
            name: name
		      })

      App.plugins[this.key].push(plugin)
    }
  })
})
