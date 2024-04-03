define(function(require) 
{
  let Back = require('components/builder/back'),
      Sortable = require('lib/sortable.min')

  return new Class(
  {
    Extends: Back,

    render: function()
    {
      let data = this.options.data,
          html =
            '<div class="builder table">'+
              '<div class="rows">'+
                (data.length ? data.map(row=> this.getRow(row)).join('') : this.getRow())+
              '</div>'+
            '</div>'

      return html
    },

    getRow: function(row)
    {
      let self = this,
          html

      row = get(row, this.getDefParams('row'))
      html =
        '<div class="row rw">'+
          '<div class="params">'+this.stringParams(row, 'columns')+'</div>'+
          '<div class="cog left">'+
            '<div class="inner">'+
              '<i class="fal fa-arrows drag"></i>'+
              '<i class="fal fa-plus-circle plus-c"></i>'+
              '<i class="fal fa-cog settings"></i>'+
              '<i class="fal fa-trash remove"></i>'+
            '</div>'+
          '</div>'+
          '<div class="cog bottom">'+
            '<div class="inner">'+
              '<i class="fal fa-plus-circle plus-r"></i>'+
            '</div>'+
          '</div>'+
          '<div class="col-24 data">'+
            '<div class="row cls">'+
              row.columns.map(col=> self.getCol(col)).join('')+
            '</div>'+
          '</div>'+
        '</div>'

      return html
    },

    getCol: function(col)
    {
      let html

      col = get(col, this.getDefParams('col'))
      html =
        '<div class="col-'+col.size+' cl">'+
          '<div class="params">'+this.stringParams(col, 'data')+'</div>'+
          '<div class="inner">'+
            '<div class="resize"></div>'+
            '<div class="data">'+
              col.data.map(addon=> this.getAddon(addon)).join('')+
            '</div>'+
            '<div class="cog horizont">'+
              '<div class="inner">'+
                '<div>'+
                  '<i class="fal fa-arrows drag"></i>'+
                  '<i class="fal fa-cog settings"></i>'+
                '</div>'+
                '<div>'+
                  '<i class="fal fa-plus-circle plus-addon"></i>'+
                  '<i class="fal fa-trash remove"></i>'+
                '</div>'+
              '</div>'+
            '</div>'+
          '</div>'+
        '</div>'

      return html
    },

    getAddon: function(addon)
    {
      let html

      addon = get(addon, this.getDefParams('addon'))
      html =
        '<div class="addon">'+
          '<div class="params">'+JSON.stringify(addon)+'</div>'+
          '<div class="inner">'+
            '<div class="adminlabel"><span>'+addon.name+'</span> '+get(addon, '', 'adminlabel')+'</div>'+
            '<div class="cog horizont">'+
              '<div class="inner">'+
                '<i class="fal fa-cog edit-addon"></i>'+
                '<i class="fal fa-trash remove"></i>'+
              '</div>'+
            '</div>'+
          '</div>'+
        '</div>'

      return html
    },

    onAfterRender: function()
    {
      this.parent()
      this.node = $('.builder.table')
      this.sortable()
      this.watchCog()
      this.resize()
    },

    resizedata: null,

    resize: function()
    {
      let self = this

      // col remove
      $(document).on('mousemove mouseup', null, function(e)
      {
        let data = self.resizedata

        if (self.resizedata && e.type == 'mouseup')
        {
          self.setParams(data.col, data.params)
          self.resizedata = null
        }

        if (data)
        {
          let distance = data.start-e.pageX,
              isstep = (Math.abs(distance) / data.step) > 1

          if (isstep)
          {
            let isleft = distance>0,
                newsize = isleft ? data.params.size-1 : data.params.size+1

            if (data.allsize == 24 && !isleft)
              return
            else if (data.params.size < 3 && isleft)
              return

            data.col.removeClass('col-'+data.params.size)
            data.col.addClass('col-'+newsize)

            data.params.size = newsize
            data.allsize = isleft ? data.allsize-1 : data.allsize+1
            data.start = (data.resize.offset().left+2)
          }
        }
      })

      this.node.on('mousedown', '.cl .resize', function(e)
      {
        let cl = $(this).parents('.cl:first'),
            cls = cl.parents('.cls').find('.cl'),
            params = self.getParams(cl)

        self.resizedata = {
          resize: $(this),
          col: cl,
          start: $(this).offset().left,
          params: params,
          step: cl.width()/params.size,
          allsize: self.getClsSize(cls)
        }
      })
    },

    watchCog: function()
    {
      let self = this

      // opacity cog
      this.node.on('mouseenter mouseleave', '.rw, .cl > .inner, .addon > .inner', function(e)
      {
        let cog = $(this).find('> .cog')

        cog[0].timeout = false

        if (e.type == 'mouseenter')
        {
          cog.fadeIn(150)
          cog[0].timeout = false
        }
        else
        {
          cog[0].timeout = setTimeout(function()
          {
            if (cog[0].timeout)
            {
              cog.hide(0)
              cog[0].timeout = false
            }
          }, 150)
        }
      })

      // col remove
      this.node.on('click', '.cl .cog .remove', function()
      {
        if (confirm('Remove Col?'))
          $(this).parents('.cl').remove()
      })

      // // row settings
      // this.node.on('click', '.rw > .cog.left .settings', function()
      // {
      //   let pop

      //   self.currentNode = $(this).parents('.rw')
      //   pop = new cws.popup({
      //     width: 'litle',
      //     label: 'Row Settings',
      //     content: 'Row'
      //   })

      //   self.currentPopup = pop
      //   pop.show()
      // })

      // row remove
      this.node.on('click', '.rw > .cog.left .remove', function()
      {
        if (confirm('Remove Row?'))
          $(this).parents('.rw').remove()
      })

      // plus row
      this.node.on('click', '.rw > .cog.bottom .plus-r', function()
      {
        let rw = $(this).parents('.rw')

        rw.after(self.getRow())
        self.sortableCol(rw.next())
      })

      // plus col
      this.node.on('click', '.rw > .cog.left .plus-c', function()
      {
        let rw = $(this).parents('.rw'),
            cls = rw.find('.cls .cl'),
            size = 0,
            allSize = self.getClsSize(cls)

        if (allSize == 24)
        {
          $(cls.get().reverse()).each(function()
          {
            let params = self.getParams($(this))

            if (!size)
            {
              if (params.size > 2)
              {
                size = 2
                $(this).removeClass('col-'+params.size)
                $(this).addClass('col-'+(params.size-2))

                params.size = params.size-2
                self.setParams($(this), params)
              }
            }
          })
        }
        else
        {
          size = 2
        }

        if (size)
          rw.find('.cls').append(self.getCol({type:'column', size:size, params:{}, data:[]}))

        self.sortableAddon(rw.find('.cl:last'))
      })

      // plus addon
      this.node.on('click', '.cl > .inner > .cog .plus-addon', function()
      {
        $(this).parents('.cl').find('.data').append(self.getAddon())
      })
    },

    sortable: function()
    {
      Sortable.create(this.node.find('.rows')[0], { 
        // group: 'omega',
        handle: '.drag',
        draggable: '.rw',
        animation: 150,
      })
      this.sortableCol(this.node.find('.rw'))
      this.sortableAddon(this.node.find('.cls'))
    },

    sortableCol: function(rw)
    {
      rw.find('.cls').each(function()
      {
        Sortable.create(this, { 
          group: 'cls',
          handle: '.drag',
          draggable: '.cl',
          animation: 150,
        })
      })
    },

    sortableAddon: function(cls)
    {
      cls.find('.data').each(function()
      {
        Sortable.create(this, { 
          group: 'addon',
          draggable: '.addon',
          animation: 150,
        })
      })
    },

    getClsSize: function(cls)
    {
      let self = this,
          size = 0

      $(cls).each(function() 
      { 
        size += parseInt(self.getParams($(this)).size) 
      })

      return size
    },

    getExtraData: function()
    {
      return {
        entityid: this.options.entityid
      }
    },

    formData: function()
    {
      let self = this,
          data = []
      
      // rows
      this.node.find('.rows > .rw').each(function()
      {
        let row = JSON.parse($(this).find('> .params').text())
        row.columns = []

        // cls
        $(this).find('> .data > .cls > .cl').each(function()
        {
          let col = JSON.parse($(this).find('> .params').text())
          col.data = []

          // addons
          $(this).find('> .inner > .data > .addon').each(function()
          {
            col.data.push(self.formDataAddon(this))
          })

          row.columns.push(col)
        })

        data.push(row)
      })

      return data
    }
  })
})