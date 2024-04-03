define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onAfterProcess: function(_data)
    {
      let data = get(_data, {}, 'plugin.filter')

      if (data.newOption)
        this.setNewOption(data.newOption)
    },

    setNewOption: function(data)
    {
      let module = App.modules[data.moduleId]
          field = module.getField(data.fieldid),
          input = module.node.find('.field.n'+data.fieldid+' .forminput')

      input.find('option').removeAttr('selected')
      input.append('<option selected value="'+data.option.value+'">'+data.option.label+'</option>')
    },

    onBeforeOpenPopup: function() {this.streamData()},
    onBeforeUpdate: function() {this.streamData()},

    streamData: function()
    {
      let data = get(this.obj.opts, {}, 'plugin.filter')

      if (data.moduleId)
        App.modules[data.moduleId].setStream()
    },

    onAfterObjsRender: function()
    {
      this.controlFields()
    },

    controlFields: function()
    {
      let self = this,
          obj = this.obj,
          controlFields = obj.opts.plugin.filter.controlFields

          console.log();

      controlFields.map(function(data)
      {
        let field = obj.getField(data.parentid)

        obj.node.find('.forminput.'+field.opts.name).change(function()
        {
          self.ajax({
            method: 'controlFields',
            data: {
              value: $(this).val(),
              entityid: obj.id,
              parentid: data.parentid,
              childids: data.childids,
              modulerefid: obj.opts.modulerefid
            },
            success: function(data)
            {
              data.fields.map(function(field)
              {
                obj.getField(field.id).updateRender({
                  opts: {
                    options: field.options
                  }
                })
              })
            }
          })
        })
      })
    }
  })
})