define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onAfterObjsRender: function()
    {
      let obj = this.obj, sobj = this.sobj

      if (obj.opts.params)
      {
        let html = App.render(this.getActor(obj.opts.params.scheme, {
              data: {Params: JSON.parse(get(obj.opts.rows[0], '[]', 'Params'))},
            }))    

        obj.node.find('.params-container').html(html)        
      }
    }

    // 27.02.2022
    // onElementGetValue: function()
    // {
    //   let obj = this.obj, sobj = this.sobj

    //   if (obj.opts.params && obj.name == 'form' && sobj.opts.name == 'ParamsHtml')
    //   {
    //     let html = App.render(this.getActor(obj.opts.params.scheme, {
    //           data: {Params: JSON.parse(get(obj.opts.rows[0], '[]', 'Params'))},
    //         }))    

    //     obj.node.find('.params-container').html(html)        

    //     sobj.value = html
    //   }
    // }
  })
})