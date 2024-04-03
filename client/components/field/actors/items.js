define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

    container: true,
    counter: 0,

		render: function()
		{
      let items = this.value ? this.value : [],
          classes = '',
          html

      classes = this.opts.isopen ? 'isopen' : '';

      html =
        '<div id="'+this.key+'" class="field items '+classes+'">'+
          '<button class="b b-s bs-f b-primary plus"><i class="fa fa-plus"></i></button>'+
          '<div class="items-block">'+
            items.map((item, i)=> this.getItem(item)).join('')+
          '</div>'+
        '</div>'            

      return html
		},

    getItem: function(data)
    {
      let i = this.counter++,
          _data = {},
          __data = null,
          html = '',
          names = get(this.opts, [], 'names').concat([this.opts.name])

      __data = _data
      names.map(name => 
      {
        __data[name] = {}
        __data = __data[name]
      })

      __data[i] = data

      params = this.getActor({
        group: 'builder',
        name: 'params',
        opts: {
          scheme: {type: 'fields', items: this.getFields()},
          data: _data,
          names: get(this.opts, [], 'names').concat([this.opts.name]),
          i: i
        }
      })

      html =
        '<div class="item">'+
          '<div class="llabel">'+
            '<i class="far fa-bars drag"></i>'+
            (!get(this.opts.isopen, 0) ? '<span>'+get(data, 'Tab', 'label')+'</span>' : '')+
            '<i class="fal fa-trash remove"></i>'+
          '</div>'+
          App.render(params)+
        '</div>'

      return html
    },

    getFields: function()
    {
      let fields = JSON.parse(JSON.stringify(this.opts.fields))
          
      if (get(this.opts.labelfield, 1) && !get(this.opts.isopen, 0))
        fields = [{type:'field', name:'label', label:'Label', value:'Tab'}].concat(fields)

      return fields
    },

    onAfterRender: function()
    {
      let self = this

      // plus
      this.node.find('.plus').click(function()
      {
        self.node.find('.items-block').append(self.getItem())
      })

      // remove
      this.node.on('click', '.llabel .remove', function(e)
      {
        $(this).parents('.item').remove()
      })

      // label
      this.node.on('input', '.params .input.label', function(e)
      {
        $(this).parents('.item').find('.llabel span').text($(this).val())
      })

      // open
      this.node.on('click', '.llabel', function()
      {
        $(this).parents('.item').toggleClass('active')
      })
    }
  })
})