define(function(require) 
{
  return new Class(
  {
    options: {},
    fields: {},

		initialize: function(options) 
    {
      this.options = options
      this.logic()
    },

    render: function()
    {
      let self = this,
          html = ''

      this.eachNames(this.options.params, [])

      html  = '<div class="params">'
      html += this.build(this.options.params)
      html += '</div>'

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
                    html =
                      '<li class="nav-item">'+
                        '<a class="nav-link '+active+'" href="#tab-tableparams-'+i+'">'+tab.label+'</a>'+
                      '</li>'

                return html
              }).join('')+
            '</ul>'+
            '<div class="tab-content">'+
              data.items.map(function (tab, i)
              {
                let active = !i ? 'active' : '',
                    html =
                      '<div class="tab-pane '+active+'" id="tab-tableparams-'+i+'">'+
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
                      (get(self.options, false, 'activeparams') ? '<input type="checkbox" name="'+self.getAPname(field)+'" class="forminput activeparams">' : '')+
                      (fieldData.label ? '<label><span>'+fieldData.label+'</span></label>' : '')+
                      '<div class="control">'+App.render(field)+'</div>'+
                    '</div>'

              return html
            }).join('')

      return html
    },

    getField: function(opts, names)
    {
      let field,
          i = get(this.options, null, 'i')

      opts.isedit = true
      opts.names = get(this.options, [], 'names').concat(names)

      if (i !== null)
        opts.names.push(i)

      this.fields[opts.name] = new App.dep['components/field/views/'+opts.type]({
        parentobject: this.options.parent,
        group: 'field',
        name: opts.type,
        opts: opts,
        value: get(this.options.data, get(opts, '', 'default'), names.concat([opts.name]))
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

          pane.parents('.tab-content').find('.tab-pane').removeClass('active')
          pane.addClass('active')

          $(this).parents('.nav-tabs').find('.nav-link').removeClass('active')
          $(this).addClass('active')
        })
      }
    }
  })
})
