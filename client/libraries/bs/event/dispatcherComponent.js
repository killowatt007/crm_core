define(function(require) 
{
  return new Class(
  {
    obj: null,

    cgroup: null,
    cname: null,
    key: null,

    _keys: {
      s: {},
      e: {},
      s_e: {},

      s_g_s: {}
    },

    run: function(event, args, sobj, opts)
    {
      let self = this,
          result = [],
          cgroup = this.obj.group,
          cname = get(opts, this.obj.name, 'format'),
          key = cgroup+cname+this.obj.id

      this.import(cgroup, cname, key)

      $.each(this._keys, function(_key, arr)
      {
        arr = arr[key]

        if (arr)
        {
          arr.map(i => 
          {
            let plugin = App.plugins[key][i],
                method = 'on',
                ok

            method += opts.prefix ? opts.prefix.charAt(0).toUpperCase()+opts.prefix.slice(1) : ''
            method += event.charAt(0).toUpperCase()+event.slice(1)

            self.obj.setTree(plugin, true)
            plugin.obj = self.obj
            plugin.sobj = sobj

            if (plugin[method])
            {
              ok = plugin[method](...get(args, []))

              if (ok === false)
                result.push(false)
              else
                result.push(true)
            }

            if (plugin.onAfterObjsRender)
            {
              if (App.pluginsAfterRender.indexOf(plugin) === -1)
                App.pluginsAfterRender.push(plugin)
            }
          })
        }
      })

      return result
    },

    import: function(cgroup, cname, key)
    {
      let self = this

      if (!App.plugins[key])
      {
        App.plugins[key] = []

        $.each(App.dep, function(path, dep)
        {
          let pathArr = path.split('/'),
              group, plgtype, name, fname, format, space, isglobal

          if (pathArr[0] == 'spaces' && (pathArr[2] == 'plugins' || pathArr[3] == 'plugins') && (pathArr[3] == cgroup || pathArr[4] == cgroup))
          {
            space = pathArr[1]

            if (pathArr[2] == 'globals')
            {
              isglobal = true
              group = pathArr[4]
              plgtype = pathArr[5]
              name = pathArr[6]
              fname = pathArr[7]
            }
            else
            {
              isglobal = false
              group = pathArr[3]
              plgtype = pathArr[4]
              name = pathArr[5]
              fname = pathArr[6]
            }

            format = fname.split('.')[1]

            // spaces statical
            if (plgtype == 'statical' && format == cname)
              self.push(key, dep, group, plgtype, name, format, space, isglobal)
            else if (plgtype == 'entity' && name == self.obj.opts.tablename)
              self.push(key, dep, group, plgtype, name, format, space, isglobal)
          }

          if (pathArr[0] == 'plugins' && pathArr[1] == cgroup)
          {
            group = pathArr[1]
            plgtype = pathArr[2]
            name = pathArr[3]
            fname = pathArr[4]
            format = fname.split('.')[1]

            // statical
            if (plgtype == 'statical' && format == cname)
              self.push(key, dep, group, plgtype, name, format)

            // entity
            if (plgtype == 'entity' && fname == self.obj.opts.tablename+'.'+cname)
              self.push(key, dep, group, plgtype, name, format)
          }
        })
      }

      return App.plugins[key]
    },

    push: function(key, dep, group, plgtype, name, format, space, isglobal)
    {
      let plugin = new dep({
            space: get(space),
            isglobal: get(isglobal),
            group: group,
            plgtype: plgtype,
            name: name,
            format: format
          }),
          _key = '',
          n = App.plugins[key].length

      _key += space ? 's_' : ''
      _key += isglobal ? 'g_' : ''
      _key += plgtype=='entity' ? 'e' : 's'

      if (!this._keys[_key][key])
        this._keys[_key][key] = []

      this._keys[_key][key].push(n)

      App.plugins[key].push(plugin)
    }
  })
})
