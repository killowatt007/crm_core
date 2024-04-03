define(function(require) 
{
  let Back = require('components/builder/back'),
      Sortable = require('lib/sortable.min')

  require('components/builder/actors/addons/page/module')
  require('components/builder/actors/addons/page/tabs')
  require('components/builder/actors/addons/page/title')

  return new Class(
  {
    Extends: Back,

    objs: {},

    render: function()
    {
      let data = this.options.data,
          html = 
            '<div class="builder page">'+
              '<div class="rows">'+
                (data.length ? data.map(row=> this.getRow(row)).join('') : this.getRow())+
              '</div>'+
              '<div class="add-row"><button class="b b-s b-primary">Add Row</button></div>'+
            '</div>'

      return html
    },

    getRow: function(row)
    {
      let html

      row = get(row, this.getDefParams('row'))
      html = 
        '<div class="row rw">'+
          '<div class="params">'+this.stringParams(row, 'columns')+'</div>'+
          '<div class="col-24 cog">'+
            '<div class="left">'+
              '<i class="fal fa-arrows drag"></i> ROW'+
            '</div>'+
            '<div class="right">'+
              '<i class="fal fa-plus-circle plus-col"></i>'+
              '<i class="fal fa-cog"></i>'+
              '<i class="fal fa-trash remove"></i>'+
            '</div>'+
          '</div>'+
          '<div class="col-24 data">'+
            '<div class="row cls">'+
            row.columns.map(col=> this.getCol(col)).join('')+
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
          '<div class="cog clearfix">'+
            '<div class="left">'+
              '<i class="fal fa-arrows drag"></i> COL'+
            '</div>'+
            '<div class="right">'+
              '<input value="'+col.size+'" class="size">'+
              '<i class="fal fa-plus-circle plus-addon"></i>'+
              '<i class="fal fa-cog"></i>'+
              '<i class="fal fa-trash remove"></i>'+
            '</div>'+
          '</div>'+
          '<div class="addons">'+
          col.data.map(addon=> this.getAddon(addon)).join('')+
          '</div>'+
        '</div>'

      return html
    },

    getAddon: function(addon)
    {
      let html

      if (get(addon, null, 'name'))
      {
        let addonBack = this.getActor({
              type: 'addon',
              group: 'builder',
              branch: 'addons.page',
              name: addon.name,            
              opts: {
                side: 'back',
                params: get(addon, this.getDefParams('addon'))
              }
            })

        this.objs[addonBack.key] = addonBack
        html = App.render(addonBack)
      }
      else
      {
        html = this.addonBack({})
      }

      return html
    },

    addonBack: function(params, back, key)
    {
      let id = key ? 'id="'+key+'"' : ''
          html = 
            '<div '+id+' class="addon clearfix">'+
              '<div class="params">'+JSON.stringify(params)+'</div>'+
              '<div class="top">'+
                '<div class="adminlabel"><span>'+get(params, '', 'name')+'</span> '+get(params, '', 'adminlabel')+'</div>'+
                '<div class="cog">'+
                  '<i class="fal fa-pencil edit-addon"></i>'+
                  '<i class="fal fa-eye"></i>'+
                  '<i class="fal fa-trash remove"></i>'+
                '</div>'+
              '</div>'+
              '<div class="back">'+get(back, '')+'</div>'+
            '</div>'

      return html
    },

    formDataAddon: function(addonNode)
    {
      let key = $(addonNode).attr('id'),
          data

      if (key)
        data = this.objs[key].getFormParams()
      else
        data = this.parent(addonNode)

      return data
    },

    onAfterRender: function()
    {
      this.parent()
      this.watchCog()
      this.sortable()

      this.resize()
    },

    resizedata: null,

    resize: function()
    {
      let self = this

      this.node.on('keyup', '.cl > .cog .size', function(e)
      {
        let size = parseInt($(this).val())

        if (size)
        {
          let cl = $(this).parents('.cl:first'),
              params = self.getParams(cl),
              oldsize = params.size

          params.size = size
          self.setParams(cl, params)

          cl.removeClass('col-'+oldsize)
          cl.addClass('col-'+size)

          $(this).val(size)
        }
      })
    },

    sortable: function()
    {
      Sortable.create(this.node.find('.rows')[0], {
        handle: '.drag',
        draggable: '.rw',
        animation: 150,
      });

      this.node.find('.cls').each(function()
      {
        Sortable.create(this, { 
          group: 'cls',
          handle: '.drag',
          draggable: '.cl',
          animation: 150,
        });
      })

      this.node.find('.addons').each(function()
      {
        Sortable.create(this, { 
          group: 'addons',
          draggable: '.addon',
          animation: 150,
        });
      })
    },

    watchCog: function()
    {
      let self = this

      // add row
      this.node.find('.add-row button').click(function()
      {
        self.node.find('.rows').append(self.getRow())
      })

      // add col
      this.node.on('click', '.plus-col', function()
      {
        $(this).parents('.rw:first').find('.cls').append(self.getCol())
      })

      // add addon
      this.node.on('click', '.plus-addon', function()
      {
        $(this).parents('.cl:first').find('.addons').append(self.getAddon())
      })

      // row remove
      this.node.on('click', '.rw > .cog .remove', function()
      {
        if (confirm('Remove row?'))
          $(this).parents('.rw').remove()
      })
      // col remove
      this.node.on('click', '.cl > .cog .remove', function()
      {
        if (confirm('Remove col?'))
          $(this).parents('.cl').remove()
      })
      // addon remove
      this.node.on('click', '.addon > .cog .remove', function()
      {
        if (confirm('Remove addon?'))
          $(this).parents('.addon').remove()
      })
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
          $(this).find('> .addons > .addon').each(function()
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