define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

  	opts: {
  		tabs: []
  	},

		render: function()
		{
			let self = this,
					html = ''

			html +=
				'<div id="'+this.key+'" class="tabs">'+
					'<div class="tab-tabs">'+
						'<div class="layer">'+
							'<ul>'+
								this.opts.tabs.map((tab, i) => 
								{
									let active = !i ? 'active' : ''
									return '<li class="'+active+'"><a href="#tab'+i+'">'+tab.label+'</a></li>'
								}).join('')+
							'</ul>'+
						'</div>'+
					'</div>'+
					'<div class="tab-contents">'+
						this.opts.tabs.map(function(tab, i) {
							let active = !i ? 'active' : ''
							return '<div class="tab-content '+active+'" id="tab'+i+'">'+tab.content+'</div>'
						}).join('')+
					'</div>'+
				'</div>'

			return html
		},

		onAfterRender: function()
		{
			this.controlTabs()
		},

		controlTabs: function()
		{
			let self = this

			this.node.on('click', '.tab-tabs ul li', function(e)
			{
				e.preventDefault()

				let a = $(this).find('a'),
						key = a.attr('href').slice(1),
						content = self.node.find('#'+key),
						tabs = self.node.find('.tab-tabs ul li'),
						contents = self.node.find('.tab-content')

				tabs.removeClass('active')
				$(this).addClass('active')
				contents.removeClass('active')
				content.addClass('active')
			})
		}
  })
})