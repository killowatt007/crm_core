define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onAfterObjsRender: function()
    {
      let obj = this.obj,
          self = this

      obj.node.find('.forminput.Module').change(function()
      {
        let tmodule = $(this).val()

        if (tmodule)
        {
          self.ajax({
            method: 'getComponentParams',
            data: {
              tmodule: tmodule
            },
            success: function(data)
            {
              let html = '',
                  pdata
                  
              if (data.params)
              {
                html = App.render(self.getActor(data.params, {
                  data: {Params: JSON.parse(get(obj.opts.rows[0], '[]', 'Params'))}
                }))
              }
              else
              {
                html = 'Not params'
              }

              obj.node.find('.params-container').html(html)
            }
          })
        }
      }).change()
    }
  })
})