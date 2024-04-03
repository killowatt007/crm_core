define(function(require) 
{
  /**
   * $version 1.1
   */

  let Actor = require('lib/bs/object/actor')

  return new Class(
  {
    Extends: Actor,

    opts: {
      type: 'popup',
      btnClose: true,
      label: null,
      labelAlign: 'left',
      width: 'full',
      content: null,
      ajax: null,
      afterRender: null,
      hide: false
    },

    open: function()
    {
      var self = this

      if (this.opts.beforeOpen)
        this.opts.beforeOpen()

      if (!this.node)
      {
        if (this.opts.ajax)
        {
          App.ajax({
            data: this.opts.ajax.data,
            success: function(data)
            {
              self.opts.content = self.opts.ajax.success(data)
              self._render()
            }
          })
        }
        else
        {
          this._render(this.opts.content)
          App.afterRender()
        }
      }
      else
      {
        this.node.show(0)
      }
    },

    _render: function()
    {
      let html = App.render(this)

      $('body').append(html)

      this.node = $('#'+this.key)
      this.node.find('.header .cclose').click(()=>this.close())

      if (this.opts.afterRender)
        this.opts.afterRender(this)

      $('body').attr('style', 'overflow:hidden;')
    },

    render: function()
    {
      let method = 'type_'+this.opts.type,
          html = this[method](this.opts.content)

      return html
    },

    close: function()
    {
      if (!this.opts.hide)
      {
        this.node.remove()
        this.node = null
      }
      else
      {
        this.node.hide(0)
      }

      $('body').attr('style', '')
    },

    type_popup: function(content)
    {
      let classes = App.ismobile ? 'mobile' : ''
          html = 
            '<div id="'+this.key+'" class="modal '+classes+'">'+
              '<div class="backdrop"></div>'+
              '<div class="layer">'+
                '<div class="cont '+this.opts.width+'">'+
                  '<div class="header">'+
                    '<div class="title align-'+this.opts.labelAlign+'">'+this.opts.label+'</div>'+
                    (this.opts.btnClose ? '<div class="buttons"><button class="b b-c b-primary cclose"><i class="far fa-times"></i></button></div>' : '')+
                  '</div>'+
                  '<div class="body">'+content+'</div>'+
                '</div>'+
              '</div>'+
            '</div>'

      return html
    }
  })
})
