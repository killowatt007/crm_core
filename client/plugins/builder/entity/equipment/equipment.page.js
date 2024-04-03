define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  require('components/fabrik/actors/list')
  require('components/builder/actors/fabrik/popup')

  return new Class(
  {
    Extends: Plugin,

    goodslist: null,
    module_treeid: 78,
    module_itemsid: 80,

    onBeforeRenderAddon: function(page, addon)
    {
      this.renderActions(page, addon)
    },

    renderActions: function(page, addon)
    {
      if (addon.name == 'title')
      {
        addon.opts.title += 
          '<div class="header-actions">'+
            '<button class="b b-s b-primary add-good">'+
              '<i class="fas fa-plus-circle"></i>'+
              (!App.ismobile ? 'Обороудование' : '')+
            '</button>'+
            '<button class="b b-s b-primary add-category">'+
              '<i class="fas fa-plus-circle"></i>'+
              (!App.ismobile ? 'Категория' : '')+
            '</button>'+
          '<div>'
      }
    },

    onAfterObjsRender: function(resData, reqData)
    {
      let self = this

      if (reqData.isWindow)
			{
        this.addGoods()
        this.addCategory()

        App.modules[this.module_treeid].addEvent('changeCategory', function(categoryid) {
          App.setDataStream('tree_catalog.active_category_id', categoryid)
          App.modules[self.module_itemsid].block.updateData() 
        })
      }
    },

    addCategory: function()
    {
			let self = this

			$('.add-category').click(function()
			{
        App.setDataStream('tree_catalog.active_category_id', App.modules[self.module_treeid].activeItem)
        App.modules[self.module_treeid].editCategory()
			})
    },

		addGoods: function()
		{
			let self = this

      App.modules[this.module_itemsid].block.addEvent('beforeOpenPopup', function(opts)
      {
        opts.afterOpen = function(form)
        {
          form.addEvent('afterProcess', function(data) 
          {
            App.setDataStream('tree_catalog.active_category_id', App.modules[self.module_treeid].activeItem)
          })
        }
      })

			$('.add-good').click(() => this._addGodds(1))
		},

    _addGodds: function(type)
    {
      App.setDataStream('tree_catalog.active_category_id', App.modules[this.module_treeid].activeItem)
      App.modules[this.module_itemsid].block.openPopup()
    }
  })
})