define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin'),
      Builders = {
        table: require('components/builder/back/table'),
        page: require('components/builder/back/page')
      }

  return new Class(
  {
    Extends: Plugin,

    builder: null,

    onAfterFormData: function(data)
    {
    	let self = this,
          obj = this.obj,
          bdata = []

      bdata = this.builder.formData()

      if (obj.opts.isNewRecord)
      {
        data.Name = 'New'
        data.StatusId = 3
        data.ParentId = obj.opts.builder.parentid
        data.EntityTypeId = obj.opts.builder.entitytypeid
        data.EntityId = obj.opts.builder.entityid
      }

      data.Data = JSON.stringify(bdata)
    },

    onElementGetValue: function()
    {
      let obj = this.obj, sobj = this.sobj

      if (obj.name == 'form' && obj.opts.builder)
      {
        obj.opts.builder.data = JSON.parse(get(obj.opts.rows, '[]', '0.Data'));
        this.builder = new Builders[obj.opts.builder.type](obj.opts.builder, this)

        sobj.value = App.render(this.builder)
      }
    }
  })
})