define(function(require) 
{
  let Plugin = require('plugin')

  return new Class(
  {
    Extends: Plugin,

    onTmplAfterBuild: function(tmpl, tmplData)
    {
    	let data = get(tmpl.opts, {}, 'plugin.control')

    	if (data && tmplData.render && !tmplData.parentId)
    	{
        if (data.spaces)
        {
          (function()
          {
            // let html = 
            //       '<div class="swccontrol">'+
            //         '<div class="label"><i class="fas fa-cogs"></i></div>'+
            //         '<div class="layer">'+

            //         '<div>'+
            //       '</div>'

            // tmpl.html += html
          })()
        }
    	}
    },

    onAfterObjsRender: function()
    {
      let self = this

      if (!window.temp)
      {
        window.temp = 1;
        $('.swccontrol select').change(function()
        {
          let spaceid = $(this).val()

          self.ajax({
            method: 'changeSpace',
            data: {
              spaceid: spaceid
            },
            success: function(data)
            {
              window.location.replace(data.redirect)
            }
          })
        })
      }
    }
  })
})