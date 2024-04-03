define(function(require) 
{
	let Field = require('components/field/actors/field'),
			Daterp = require('lib/daterangepicker'),
			moment = require('moment')

  return new Class(
  {
  	Extends: Field,

  	opts: {
  		format: 'DD.MM.YYYY'
  	},

		// initialize: function(data)
		// {
		// 	this.parent(data)
		// 	this.setOpts(data.opts)
		// },

		render: function()
		{
			let html = 
				'<div id="'+this.key+'" class="field-date">'+
					this.parent()+
					(this.opts.isedit ? '<div class="button"><i class="far fa-backspace"></i></div>' : '')+
				'</div>'

			return html
		},

		renderEdit: function()
		{
			let value = get(this.getValue(), '')

			if (value)
				value = moment(value).format(this.opts.format)
			else if (value === null)
				value = ''

			return '<input name="'+this.getName()+'" class="forminput form-control '+this.getClasses()+'" type="text" value="'+value+'" placeholder="'+get(this.opts, '', 'placeholder')+'">'
		},

		renderRO: function()
		{
			let value = this.getValue()
			return (value ? moment(value).format(this.opts.format) : '')
		},

		onAfterRender: function()
		{
			if (this.opts.isedit)
			{
	      this.node.find('.forminput').daterangepicker({
			    startDate: (this.value ? moment(this.value).format(this.opts.format) : true),
			    autoUpdateInput: false,
					singleDatePicker: true,
					showDropdowns: true,
					drops: 'down',
					autoApply: true,
					locale: {
						format: this.opts.format
					},
				  // timePicker: true,
					// opens: 'center',
	      })

			  this.node.find('.forminput').on('apply.daterangepicker', (ev, picker) => this.apply(ev, picker))
			  this.node.find('.button i').click(() => this.clear())

			  if (this.value)
			  	this.showClear()
			}
		},

		apply: function(ev, picker)
		{
	    this.node.find('.forminput').val(picker.startDate.format(this.opts.format))
	    this.value = picker.startDate.format('YYYY-MM-DD HH:mm:ss')

	    if (this.opts.onApply)
	    	this.opts.onApply(this, ev, picker)

	    this.showClear()
		},

		val: function(val)
		{
			if (!val)
				this.hideClear()
			
	  	this.value = val
	    this.node.find('.forminput').val(val)
		},

		clear: function()
		{
	  	this.val('')

	    if (this.opts.onClear)
	    	this.opts.onClear(this)
		},

		showClear: function()
		{
			this.node.find('.button i').show(0)
		},

		hideClear: function()
		{
			this.node.find('.button i').hide(0)
		},

    getCurrentValue: function()
    {
      return this.value
    }
  })
})

// $.datepicker.regional['ru'] = {
//   closeText: 'Закрыть',
//   prevText: 'Предыдущий',
//   nextText: 'Следующий',
//   currentText: 'Сегодня',
//   monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
//   monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
//   dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
//   dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
//   dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
//   weekHeader: 'Не',
//   dateFormat: 'dd.mm.yy',
//   firstDay: 1,
//   isRTL: false,
//   showMonthAfterYear: false,
//   yearSuffix: ''
// };
// $.datepicker.setDefaults($.datepicker.regional['ru']);