define(function(require) 
{
  return new Class(
  {
    dispatcher: null,
    objsObserve: [],

    initialize: function(dispatcher, obj) 
    {
      this.dispatcher = dispatcher
      this.setObserveForObj(obj)
    },

    run: function(event, args = [])
    {
      let self = this,
          result,
          _result

      this.objsObserve.map((obj, i) => 
      {
        self.dispatcher.obj = obj.object
        sobj = !i ? null : self.objsObserve[0].object

        if (!i)
        {
          if (obj.object.fireEvent)
            obj.object.fireEvent(event, args)
        }

        _result = self.dispatcher.run(event, get(args, []), sobj, obj.opts)

        if (!i)
          result = _result
      })

      return result
    },

    setObserveForObj: function(obj, opts)
    {
      this.objsObserve.push({
        object: obj,
        opts: get(opts, {})
      })
    }
  })
})
