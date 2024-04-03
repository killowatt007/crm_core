define(function(require) 
{
  return new Class(
  {
		initialize: function() 
    {
      let self = this

      window.onpopstate = function(e) 
      {
        self.open(location.pathname, {popstate: true})
      }
    },

    open: function(key, _opt)
    {
      let self = this,
          opt = _opt ? _opt : {},
          _data = {
            isWindow: 1,
            isrun: get(opt, false, 'isrun')
          }

      _data = $.extend(_data, get(opt, {}, 'args'))

      if ($.isNumeric(key))
        _data.itemId = key
      else
        _data.path = key

      if (App.item)
        _data.referrerItemId = App.item.id

      if (App.getPagePluginManager()) // temp!!!
        App.getPagePluginManager().run('openWindow')
      
      App.ajax({
        data: _data,
        success: function(data)
        {
          let object = new App.dep[App.clientPath](data),
              url,
              urlargs = ''

          if (!opt.popstate)
          {
            url = App.item.path

            if (_data.isrun)
              urlargs += window.location.search.substr(1) ? '?'+window.location.search.substr(1) : ''

            url += urlargs
            history.pushState(null, null, url)
          }

          object.execute()

          if (opt.callback)
            opt.callback()
        }
      })
    }
  })
})
