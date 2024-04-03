define(function(require) 
{
  /**
   * $version 1.1
   */

  return new Class(
  {
    group: null,
    name: null,

    subconstCls: {},

    opts: {},
    co: {},

		initialize: function(data)
		{
			this.setOpts(data.opts)
			this.initProp(data)
		},

		initProp: function(data)
		{
			let self = this

			$.each(data, function(key, val)
			{
				if (key != 'opts')
					self[key] = val
			})
		},

		ajax: function(obj)
		{
			App.ajax(obj)
		},

		getActor: function(data, opts)
		{
      let path,
          obj

      path  = get(data, 'components', 'exttype')+'/'
      path += data.group+'/actors/'
      path += data.branch ? data.branch.replace(/\./g, '/')+'/' : ''
      path += data.name

      data.po = this
      data.opts = $.extend(data.opts, get(opts, {}))

      obj = new App.dep[path](data)

      if (obj.init)
      	obj.init()

      this.setTree(obj)

      if (obj.group == 'module')
      	App.modules[obj.id] = obj

      if (obj.alias)
      	this.co[obj.alias] = obj

      return obj
		},

    subconst: function(name, opts)
    {
      if (!this.subconstCls[name])
        this.subconstCls[name] = new Class(this[name])

      let obj = new this.subconstCls[name]()

		  obj.opts = get(opts, {})
		  obj.po = this

		  if (obj.init)
		  	obj.init()

      return obj
    },

		setTree: function(obj, isplg)
		{
      let n

      if (isplg)
      {
				n = App.tree.objects.indexOf(this)

				if (n !== -1)
				{
					if (!App.tree.childs[n].includes(obj))
						App.tree.childs[n].push(obj)
				}
      }
      else
      {
	      if (!App.tree.objects.includes(this))
	      {
	      	App.tree.objects.push(this)
	      	App.tree.childs.push([])
	      }

	      n = App.tree.objects.indexOf(this)

	      App.tree.childs[n].push(obj)
      }

      App.getPluginManager().run('system', 'test')
		},

		setOpts: function(opts)
		{
			if (this.opts)
				Object.assign(this.opts, opts)
		}
  })
})
