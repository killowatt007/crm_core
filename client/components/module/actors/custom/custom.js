define(function(require) 
{
	let View = require('view'),
			Sortable = require('lib/sortable.min'),
			Ace = require('ace/ace')

  require('components/builder/actors/popup')
  require('components/field/actors/list')

  return new Class(
  {
  	Extends: View,
  	
  	options: {},

		render: function()
		{
			return '<div id="'+this.key+'">'+this['f'+this.opts.flag]()+'</div>'
		},

		fdeveloper: function()
		{
			let html =
				'<div class="dev">'+
					'<div class="left">'+
						'<div class="group">'+
							'<div class="label">Modules</div>'+
							'<ul class="list">'+
								'<li>Fabrik Form</li>'+
								'<li>Fabrik List</li>'+
							'</ul>'+
						'</div>'+
						'<div class="group">'+
							'<div class="label">Fabrik Elements</div>'+
							'<ul class="list">'+
								'<li>Databasejoin</li>'+
								'<li>Field</li>'+
								'<li>Calc</li>'+
							'</ul>'+
						'</div>'+
						'<div class="group">'+
							'<div class="label">Fabrik Tables</div>'+
							'<ul class="list">'+
								'<li>Business Entity Registration</li>'+
								'<li>Resources Registration</li>'+
								'<li>Service Request</li>'+
							'</ul>'+
						'</div>'+
					'</div>'+
					'<div class="right">'+
						'<div class="bookmarks">'+


							'<div class="group">'+
								'<div class="inner">'+
									'<div class="label">Fabrik Form</div>'+
									'<div class="items">'+
										'<div class="item">fabrikForm.php</div>'+
										'<div class="item">fabrikForm.js</div>'+
									'</div>'+
								'</div>'+
							'</div>'+

							'<div class="group active">'+
								'<div class="inner">'+
									'<div class="label">Fabrik List</div>'+
									'<div class="items">'+
										'<div class="item active">fabrikList.php</div>'+
										'<div class="item">fabrikList.js</div>'+
									'</div>'+
								'</div>'+
							'</div>'+

							'<div class="group">'+
								'<div class="inner">'+
									'<div class="label">Databasejoin</div>'+
									'<div class="items">'+
										'<div class="item">databasejoin.php</div>'+
										'<div class="item">databasejoin.js</div>'+
									'</div>'+
								'</div>'+
							'</div>'+


						'</div>'+
						'<div id="editor"></div>'+
						'<div class="actions">'+
							'<button class="b b-s b-primary" type="button">Create New Version</button>'+
							'<button class="b b-s b-primary" type="button" style="margin-left:10px;">Save</button>'+
							'<button class="b b-s b-primary" type="button">Submit</button>'+
							'<button class="b b-s b-primary" type="button" style="margin-left:549px;">Approve</button>'+
						'</div>'+
					'</div>'+
				'</div>'
				
			return html
		},

		onAfterRender: function()
		{
			let ftmpl = $('.ftmpl'),
					btmpl = $('.btmpl')

			if (ftmpl[0])
			{
				// form
				if (!ftmpl[0].test)
				{
					ftmpl[0].test = true;

					ftmpl.on('mouseenter mouseleave', '.rw', function(e)
					{
						let cog = $(this).find('> .cog')

						if (e.type == 'mouseenter')
						{
							cog.show(0)
						}
						else
						{
							cog.hide(0)
						}
					})

					Sortable.create(ftmpl[0], { 
						// group: 'omega',
						handle: '.drag',
						draggable: '.rw',
						animation: 150,
					});

					ftmpl.find('.cels').each(function()
					{
						Sortable.create(this, { 
							group: 'cels',
							// handle: '.drag',
							draggable: '.cel',
							animation: 150,
							// emptyInsertThreshold: 100
						});
					})
				}

				// builder
				if (!btmpl[0].test)
				{
					btmpl[0].test = true;

					Sortable.create(btmpl[0], { 
						// group: 'omega',
						handle: '.drag',
						draggable: '.rw',
						animation: 150,
					});

					btmpl.find('.cls').each(function()
					{
						Sortable.create(this, { 
							group: 'cls',
							// handle: '.drag',
							draggable: '.cl',
							animation: 150,
						});
					})

					btmpl.find('.addons').each(function()
					{
						Sortable.create(this, { 
							group: 'addons',
							// handle: '.drag',
							draggable: '.addon',
							animation: 150,
						});
					})
				}



				// var sortables = [],
				// 		div;

				// $('.pbrow .drag').mousedown(function()
				// {
				// 	var sortable;

				// 	$('.pbrow').each(function()
				// 	{
				// 		sortable = Sortable.create(this, { 
				// 			group: 'omega',
				// 			handle: '.drag',
				// 			draggable: '.pbcol',
				// 			animation: 150,
				// 			onStart: function(evt)
				// 			{
				// 				var item = $(evt.item);

				// 				item.css({
				// 					opacity: '.5'
				// 				});
				// 			},
				// 			onEnd: function(evt)
				// 			{
				// 				$.each(sortables, function(i, sortable){
				// 					sortable.destroy();
				// 				});

				// 				sortables = [];

				// 				var item = $(evt.item);

				// 				item.css({
				// 					opacity: 1
				// 				});
				// 			}
				// 		});

				// 		sortables.push(sortable);
				// 	});
				// });

				// $('.addone').mousedown(function()
				// {
				// 	var sortable;

				// 	$('.pbcol').each(function()
				// 	{
				// 		sortable = Sortable.create(this, { 
				// 			group: 'omega',
				// 			group: 'omega2',
				// 			draggable: '.addone',
				// 			animation: 150,
				// 			onStart: function(evt)
				// 			{
				// 				var item = $(evt.item);

				// 				item.css({
				// 					opacity: '.5'
				// 				});
				// 			},
				// 			onEnd: function(evt)
				// 			{
				// 				$.each(sortables, function(i, sortable){
				// 					sortable.destroy();
				// 				});

				// 				sortables = [];

				// 				var item = $(evt.item);

				// 				console.log(evt.item);

				// 				item.css({
				// 					opacity: 1
				// 				});
				// 			}
				// 		});

				// 		sortables.push(sortable);
				// 	});
				// });
			}


			if ($('#editor')[0])
			{
				let editor = Ace.edit('editor'),
						text =
							'<php'+"\n"+
							'namespace bs\\modules;'+"\n"+
							'defined(\'EXE\') or die(\'Access\');'+"\n"+
							"\n"+
							'use \\bs\\libraries\\module\\Module;'+"\n"+
							"\n"+
							'class FabrikList extends Module'+"\n"+
							'{'+"\n"+
							'  protected function data()'+"\n"+
							'  {'+"\n"+
							'    $this->app = \\F::getApp();'+"\n"+
							'    $tableId = $this->getParam(\'tableId\');'+"\n"+
							     "\n"+
							'    $model = $this->app->getModel(\'fabrik\', \'list\');'+"\n"+
							'    $model->setMParams($this->id, $this->getParam());'+"\n"+
							'    $model->setId($tableId);'+
							'    $view = $this->app->getView(\'fabrik\', \'list\');'+"\n"+
							'    $view->setModel($model);'+"\n"+
							"    \n"+
							'    $data[\'view\'] = $view->getData();'+"\n"+
							"    \n"+
							'    return $data;'+"\n"+
							'  }'+"\n"+
							'}'

		    editor.setTheme('ace/theme/textmate')
		    editor.getSession().setMode({path:'ace/mode/php', inline:true})
		    editor.getSession().setOptions({ tabSize: 2, useSoftTabs: true })
		    editor.setValue(text, -1)

		    // editor.setReadOnly(true)

		    







			}


			this.afterLogin()
		},

		fslider: function()
		{
			let self = this,
					html = '',
					style = '',
					classname = ''

			style += 'height:'+$(window).height()+'px;'

			if (this.opts.slide)
				style += 'background-image: url(/server/'+this.opts.slide+');'

			classname = this.opts.slide ? '' : 'emptyslide'

			html = 
				'<div class="slider '+classname+'" style="'+style+'">'+
					'<div class="text">'+
						this.opts.product.Description+'<br>'+
						(this.opts.logo 
							? '<img src="/server/'+this.opts.logo+'">' 
							: '<span class="productName">'+this.opts.product.Name+'</span>'
						)+
					'</div>'+
				'</div>'

			return html
		},

		fdashboard: function()
		{
			let self = this,
					html = '&ensp;'

			if (!this.opts.guest)
			{
				html = 
					'<div class="items">'+
						'<div class="item dashboard">'+
							'<span class="llabel">'+
								'<i class="far fa-tachometer-fast icon"></i>'+
								'<a href="'+this.opts.path+'" type="button">Главная</a>'+
							'</span>'+
						'</div>'+
					'</div>'
			}

			return html
		},

		afterLogin: function()
		{
			// base
			if (this.opts.base)
			{
				let base = this.node.find('.base'),
						ddown = base.find('.ddown')

				base.click(() => ddown.toggleClass('open'))

				$('body').click(function(e)
				{
					let target = ($(e.target).parents('.base')[0] || $(e.target).hasClass('base'))

					if (!target)
					{
						if (ddown.hasClass('open'))
							ddown.removeClass('open')
					}
				})

				base.find('a').click(function()
				{
					let href = $(this).attr('href')
					$(this).attr('href', href+'&path='+App.item.path)
				})
			}
		},

		flogin: function()
		{
			let self = this,
					html = '',
					label = (this.opts.guest) ? 'Войти' : 'Выйти',
					classBtn = (this.opts.guest) ? 'in' : 'out',
					items = [],
					popup

			if (!this.opts.guest)
			{
				// bell
				if (this.opts.bell)
				{
					items.push(
						'<div class="item bell">'+
							'<span class="llabel">'+
								'<i class="far fa-bell"></i>'+
								// '<div class="n">2</div>'+
							'</span>'+
						'</div>'
					)
				}

				// base
				if (this.opts.base)
				{
					let labelbase,
							lihtml =
								this.opts.base.items.map(item => 
								{
									let html = '',
											selected = (item.value == this.opts.base.active)

									if (selected)
										labelbase = item.label

									html = 
										'<li>'+
											(selected ? '<i class="far fa-check check"></i>' : '')+
											'<a href="/bootstrap.php?option=system&task=system.base&baseid='+item.value+'">'+item.label+'</a>'+
										'</li>'
									
									return html
								}).join('')

					items.push(
						'<div class="item base">'+
							'<span class="llabel">'+
								labelbase+
								'<i class="far fa-chevron-down arrow"></i>'+
							'</span>'+
							'<div class="ddown">'+
								'<ul>'+lihtml+'</ul>'+
							'</div>'+
						'</div>'
				)
				}
			}

			items.push(
				'<div class="item login '+classBtn+'">'+
					'<span class="llabel">'+
						'<i class="far fa-sign-out-alt icon"></i>'+
						label+
					'</span>'+
				'</div>'
			)

			html += 
				'<div class="items">'+
					items.map(item => item).join('')+
				'</div>'

			// login form
			$('body').on('click', '.builder .login.in', function()
			{
				popup = self.getActor({
          group: 'builder',
          name: 'popup',
          opts: {
						label: 'Войдите в свой аккаунт',
						labelAlign: 'center',
						btnClose: false,
						width: 'litle',
						content: 
							'<div class="loginform">'+
								'<div class="field"><span class="label">Логин:</span> <input class="user" type="text"></div>'+
								'<div class="field"><span class="label">Пароль:</span> <input class="password" type="password"></div>'+
								'<div class="actions">'+
									'<button type="button" class="submit">Вход</button>'+
									'<span class="error_msg"></span>'+
									'<button type="button" class="cclose">Закрыть</button>'+
								'</div>'+
							'</div>'
          }
        })	

				popup.open()
			})

			// close
			$('body').on('click', '.loginform button.cclose', function()
			{
				popup.close()
			})

			// submit
			$('body').on('click', '.loginform button.submit', function()
			{
				let loginform = $('.loginform'),
						productId = loginform.find('.field .product').val(),
						user = loginform.find('.field .user').val(),
						password = loginform.find('.field .password').val(),

						error_msg = loginform.find('.error_msg')

				error_msg.html('')

				App.ajax({
					data: {
						option: 'system',
						task: 'user.login',
						productId: productId,
						user: user,
						password: password
					},
					success: function(data) 
					{
						if (data.error)
						{
							error_msg.html(data.error)
						}
						else if (data.redirect)
						{
							let urlArgs = new URLSearchParams(window.location.search),
									redirect = data.redirect

							urlArgs.forEach((val, key) => 
							{
								if (key == 'red')
									redirect = decodeURI(val)
							})

							window.location.href = redirect
						}
					}
				})
			})

			// submit
			$('body').on('click', '.builder .login.out', function()
			{
				App.ajax({
					data: {
						option: 'system',
						task: 'user.logout'
					},
					success: function(data) 
					{
						if (data.redirect)
						{
							window.location.replace(data.redirect)
						}
					}
				})
			})

			return html
		},

		fbase: function()
		{
      // let html = '',
      // 		list = this.getActor({
      //       group: 'field',
      //       name: 'list',
      //       value: null,
      //       opts: {
      //       	options: [
      //       		{value:1, label:1}
      //       	],
      //         name: 'date_from',
      //         isedit: true
      //       }
      //     })

      // html =
      // 	'<div class="base">'+
      // 		'<div class="llabel">База</div>'+
      // 		'<div class="control">'+App.render(list)+'</div>'+
      // 	'</div>'

      // return html
		}
  })
})