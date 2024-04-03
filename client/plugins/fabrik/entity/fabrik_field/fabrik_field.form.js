define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onAfterObjsRender: function()
    {
      let self = this,
          obj = this.obj

      obj.node.find('.forminput.Type').change(function()
      {
        let type = $(this).val()

        if (type)
        {
          self.ajax({
            method: 'getFieldParams',
            data: {
              ftype: type,
              rowid: self.obj.opts.rowId
            },
            success: function(data)
            {
              let html = ''

              if (data.params)
              {
                html = App.render(self.getActor(data.params, {
                  data: {Params: JSON.parse(get(obj.opts.rows[0], '[]', 'Params'))},
                  names: ['Params'],
                  getExtraData: function()
                  {
                    return {
                      fieldid: obj.opts.rowId
                    }
                  }
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