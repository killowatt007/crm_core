define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onBeforeOpenPopup: function() {this.streamData()},
    onBeforeUpdate: function() {this.streamData()},

    streamData: function()
    {
      let data = get(this.obj.opts, {}, 'plugin.filter')

      if (data.moduleId)
        App.modules[data.moduleId].setStream()
    }
  })
})