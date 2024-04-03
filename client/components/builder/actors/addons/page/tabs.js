define(function(require) 
{
	let Addon = require('components/builder/addon')
      Sortable = require('lib/sortable.min'),
      HObject = require('lib/bs/helper/object')

  require('components/field/actors/html/tabs')
  require('components/builder/actors/popup')
  require('components/builder/actors/params')
  require('components/builder/actors/addons/general/html')
  require('components/builder/actors/addons/general/text')
  require('components/field/actors/field')

  return new Class(
  {
  	Extends: Addon,

    currentBlock: null,
    currentPopup: null,

    tabs: null,
    items: [],

		render_front: function()
		{
			let tabsData = [],
					html = '',
					tabs

      if (this.opts.type == 'advanced')
      {
        this.opts.tabs.map(tab => 
        {
          tabsData.push({
            label: tab.label,
            content: this.po.build({data:tab.data})
          })
        })
      }
      else
      {
        this.opts.tabs.map(tab => 
        {
          let actor = this.getActor(tab.data)

          tabsData.push({
            label: tab.label,
            content: App.render(actor)
          })
        })
      }

			tabs = this.getActor({
        group: 'field',
        name: 'tabs',
        branch: 'html',
        opts: {tabs: tabsData}
      })

			html = App.render(tabs)

			return html
		},

  	render_back: function()
  	{
      let html = ''

      if (this.opts.params.params.type == 'advanced')
        html = this.renderBackTabs()

      return this.po.addonBack(this.opts.params, html, this.key)
  	},

  	renderBackTabs: function()
  	{
  		let html = ''

      this.opts.params.params.items.map((item, i) => 
      {
        let content = item.data.map(row=> this.po.getRow(row)).join('')

        this.items.push({
          label: item.params.label,
          content: content,
          params: item.params
        })
      })

			this.tabs = new Tabs(
      {
        opts: {tabs: this.items},
        afterRender: function(html)
        {
          let div = $('<div>').append(html)

          div.find('.tabs').addClass('edit')
          div.find('.tabs .tab-tabs ul').append('<button class="b b-s b-primary plus-tab"><i class="fa fa-plus"></i></button>')
          div.find('.tabs .tab-tabs ul li').append(
            '<div class="cog">'+
              '<div class="inner">'+
                '<i class="fal fa-cog settings"></i>'+
                '<i class="fal fa-trash remove"></i>'+
              '</div>'+
            '</div>'
          )

          return div[0].innerHTML
        }
      })

  		html = '<div class="addon-tabs">'+App.render(this.tabs)+'</div>'
  					
  		return html	
  	},

    getFormParams: function()
    {
      let self = this,
          params = this.parent(),
          items = []

      if (this.opts.params.params.type == 'advanced')
      {
        this.node.find('.tab-content').each(function()
        {
          let i = $(this).index(),
              item = {
                params: self.items[i].params,
                data: []
              }

          // rows
          $(this).find('> .rw').each(function()
          {
            let row = JSON.parse($(this).find('> .params').text())
            row.columns = []

            // cls
            $(this).find('> .data > .cls > .cl').each(function()
            {
              let col = JSON.parse($(this).find('> .params').text())
              col.data = []

              // addons
              $(this).find('.addon').each(function()
              {
                col.data.push(JSON.parse($(this).find('> .params').text()))
              })

              row.columns.push(col)
            })

            item.data.push(row)
          })

          items.push(item)
        })

        params.params.items = items
      }

      return params
    },

  	onAfterRender: function()
  	{
      if (this.opts.side == 'back' && this.opts.type == 'advanced')
      {
        this.plusTab()
        this.removeTab()
        this.settings()
        this.sortable()
      }
  	},

    plusTab: function()
    {
      let self = this

      this.node.on('click', '.plus-tab', function()
      {
        self.items.push({
          label: 'Tab',
          content: self.po.getRow()
        })

        self.tabs.opts.tabs = self.items
        self.tabs.updateRender()
        self.sortable()
      })
    },

    removeTab: function()
    {
      self = this

      this.node.on('click', '.tab-tabs .cog .remove', function()
      {
        let i = $(this).parents('li:first').index()
            
        self.items.splice(i,1)

        self.tabs.opts.tabs = self.items
        self.tabs.updateRender()
        self.sortable()
      })
    },

    afterPopap: function()
    {
      let self = this

      this.currentPopup.node.find('.apply').click(function()
      {
        let params = HObject.inputsToObject(self.currentPopup.node.find('input, select, textarea')),
            i = $(self.currentBlock).index()

        self.items[i].params = params
        self.items[i].label = params.label

        $(self.currentBlock).find('a').text(params.label)
        self.currentPopup.close()
      })
    },

    sortable: function()
    {
      Sortable.create(this.node.find('.tabs .tab-tabs ul')[0], {
        // handle: '.remove',
        draggable: 'li',
        animation: 150,
        onEnd: evt =>
        {
          let b = this.items[evt.newIndex]

          this.items[evt.newIndex] = this.items[evt.oldIndex]
          this.items[evt.oldIndex] = b
          this.tabs.opts.tabs = this.items
        }
      })
    },

    settings: function()
    {
      let self = this

      this.node.on('click', '.tab-tabs .cog .settings', function()
      {
        let html = '',
            tab = $(this).parents('li:first'),
            popup = self.getActor({
              group: 'builder',
              name: 'popup',
              opts: {
                label: 'Tab',
                width: 'litle',
                afterRender: function(popup) {popup.tab.afterPopap()}
              }
            })

        html = 
          '<div class="params-modal">'+
            App.render(self.getActor({
              id: null,
              group: 'builder',
              name: 'params',
              opts: {
                scheme:
                {
                  type: 'sections',
                  items: [
                    {
                      size: 24,
                      data: {
                        type: 'fields',
                        items: [
                          {
                            type: 'field',
                            name: 'label',
                            label: 'Label',
                            isedit: true 
                          }
                        ]
                      }
                    }
                  ]
                },
                data: []
              }
            }))+
            '<div class="addon-params" style="margin-top:20px"></div>'+
            '<button type="submit" class="b b-s b-success apply">Apply</button>'+
          '</div>'

        // self.currentType = 'tab'
        self.currentBlock = tab
        self.currentPopup = popup

        popup.tab = self
        popup.opts.content = html
        popup.open()
      })
    }
  })
})