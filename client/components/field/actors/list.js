define(function(require) 
{
	let Actor = require('components/field/actor'),
			Select2 = require('lib/select2/select2')

  return new Class(
  {
  	Extends: Actor,

  	searchValue: null,

		render: function()
		{
      let html,
      		className,
      		attrs = ''

			if (this.opts.isedit)
			{
				if (get(this.options, 0, 'multilist'))
					attrs = 'multiple="multiple"'

    		html = 
          '<select '+attrs+' id="'+this.key+'" class="'+this.getClasses()+' forminput form-control" name="'+this.getName()+'">'+
          	this.getOptions()+
          '</select>'
			}
			else
			{
				html = this.getValue()
			}

      return html
		},

		getOptions: function()
		{
			let self = this,
					def = self.getValue(),
					html = ''

			if (this.opts.options.length)
			{
				html =
	      	(this.isps() ? '<option value="">- Не выбрано -</option>' : '')+
	        this.opts.options.map(option => 
	        { 
	          let html = ''

	          if (option.options)
	          {
          		html = 
    						'<optgroup label="'+option.label+'">'+
                	option.options.map(option => self.getOption(option, def)).join('')+
                '</optgroup>'
	          }
	          else
	          {
	          	html = self.getOption(option, def)
	          }

	          return html
	        }).join('')
			}
			else
			{
				html = '<option value="">'+(this.opts.onSearch ? '- Не выбрано -' : '- Пусто -')+'</option>'
			}

			return html
		},

		getOption: function(option, def)
		{
    	let html,
    			selected = $.inArray(String(option.value), def) !== -1 ? 'selected' : ''
    				
    	return '<option '+get(option, '', 'attrs')+' value="'+option.value+'" '+selected+'>'+option.label+'</option>'
		},

		getValue: function()
		{
			let value = this.parent()

			value = Array.isArray(value) ? value : [value]
			value.map((val, i) => value[i] = String(val))

			return value
		},

		isps: function()
		{
			return get(this.opts.isps, true)
		},

		onAfterRender: function()
		{
			let data = {
						minimumResultsForSearch: get(this.opts, 10, 'minimumResultsForSearch')
					}

			if (this.opts.onSearch)
				data.minimumResultsForSearch = 0

			$(this.node).select2(data)

			if (this.opts.onSearch)
				this.search()
		},

		onAfterUpdateRender: function()
		{
			if (this.opts.onSearch)
			{
	      $(this.node).select2('close')
	      // $(this.node).trigger('change.select2')
				$(this.node).select2('open')
				
				if (!this.isps() && this.opts.options.length > 0)
					$('.select2-results__options li:first').remove()

				$('.select2-search__field').val(this.searchValue)

				if (App.ismobile)
					setTimeout(() => $('.select2-search__field').blur(), 10)

				$('.select2-dropdown').css('opacity', 1)
			}
		},

		search: function()
		{
			let self = this

			$(this.node).on('select2:open', function()
			{
				let search = $('.select2-search__field')

	  		search
	  			.attr('placeholder', 'Поиск')
					.unbind()
			  	.keyup(function(e)
			  	{
			  		let value = $.trim($(this).val())

			  		if (value && e.keyCode === 13)
			  		{
			  			$('.select2-dropdown').css('opacity', '0.5')

			  			self.searchValue = value
			  			self.opts.onSearch(self, value)
			  		}
			  	})
			})
		}
  })
})


