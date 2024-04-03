define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,

    fields: {},

    initialize: function(data)
    {
      this.parent(data)
    },

    render: function()
    {
      let self = this,
          html = ''
          
      this.eachNames(this.opts.scheme, [])

      html  = '<div id="'+this.key+'" class="params">'
      html += this.build(this.opts.scheme)
      html += '</div>'

      this.logic()

      return html
    },

    eachNames: function(data, names)
    {
      let self = this

      if (data.type != 'fields')
      {
        if (name = get(data, null, 'name'))
          names.push(name)

        data.items.map(function(item)
        {
          if (item.data)
          {
            let _names = []

            if (name = get(item, null, 'name'))
              _names.push(name)

            self.eachNames(item.data, names.concat(_names))
          }
        })
      }
      else
      {
        data.names = get(names, [])
      }
    },

    build: function(params)
    {
      let html = '',
          method = 'type_'+params.type

      html = this[method](params)

      return html
    },

    type_tabs: function(data)
    {
      let self = this,
          html = 
            '<ul class="nav nav-tabs">'+
              data.items.map(function (tab, i)
              {
                let active = !i ? 'active' : '',
                		html = ''

                tab.c = App.paramsTabCounter++

                html =
                  '<li class="nav-item">'+
                    '<a class="nav-link '+active+'" href="#tab-tableparams-'+tab.c+'">'+tab.label+'</a>'+
                  '</li>'

                return html
              }).join('')+
            '</ul>'+
            '<div class="tab-content">'+
              data.items.map(function (tab, i)
              {
                let active = !i ? 'active' : '',
                    html =
                      '<div class="tab-pane '+active+'" id="tab-tableparams-'+tab.c+'">'+
                        (tab.data ? self.build(tab.data) : '')+
                      '</div>'

                return html
              }).join('')+
            '</div>'

      return html
    },

    type_sections: function(data)
    {
      let self = this,
          html = 
            '<div class="row">'+
              data.items.map(function(section)
              {
                let html =
                      '<div class="col-'+section.size+'">'+
                        '<fieldset>'+
                          (section.label ? '<legend>'+section.label+'</legend>' : '')+
                          (section.data ?self.build(section.data) : '')+
                        '</fieldset>'+
                      '</div>'

                return html
              }).join('')+
            '</div>'

      return html
    },

    type_fields: function(data)
    {
      let self = this,
          html =
            data.items.map(function(fieldData)
            {
              let field = self.getField(fieldData, data.names),
                  html = 
                    '<div class="form-group '+get(data, '', 'view')+'">'+
                    	self.getActiveparams(field, data)+
                      (fieldData.label ? '<label><span>'+fieldData.label+'</span></label>' : '')+
                      '<div class="control">'+App.render(field)+'</div>'+
                    '</div>'

              return html
            }).join('')

      return html
    },

    getActiveparams: function(field, data)
    {
    	let html = ''

      if (get(this.opts, false, 'activeparams') && get(field.opts, true, 'activeparams'))
      {
      	let name = field.opts.names.concat(['activeparams',field.opts.name])
      			checked =  get(this.opts.data, 0, name) ? 'checked' : ''

      	html = '<input type="checkbox" name="'+this.getAPname(field)+'" class="forminput activeparams" '+checked+'>'
      }

      return html
    },

    getField: function(opts, names)
    {
      let field,
          i = get(this.opts, null, 'i')

      opts.isedit = true
      opts.names = get(this.opts, [], 'names').concat(names)

      if (i !== null)
        opts.names.push(i)

      this.fields[opts.name] = this.getActor({
				group: 'field',
				name: opts.type,
				value: get(this.opts.data, get(opts, '', 'default'), opts.names.concat([opts.name])),
				opts: opts
			})

      return this.fields[opts.name]
    },

    getAPname: function(field)
    {
      let name = ''

      field.opts.names.map((part, i) => { name += (!i) ? part : '['+part+']' })
      name += name ? '[activeparams]' : 'activeparams'
      name += '['+field.opts.name+']'

      return name
    },

    logic: function()
    {
      let app = $(App.selector)[0]

      if (!app.lib_params)
      {
        app.lib_params = true

        $('body').on('click', '.nav-tabs a.nav-link', function(e)
        {
          e.preventDefault()
          let ancor = $(this).attr('href').substr(1),
              pane = $('.tab-content .tab-pane#'+ancor)

          if (pane[0])
          {
	          pane.parents('.tab-content:first').find('> .tab-pane').removeClass('active')
	          pane.addClass('active')

	          $(this).parents('.nav-tabs:first').find('> .nav-item .nav-link').removeClass('active')
	          $(this).addClass('active')
          }
        })
      }
    }
  })
})