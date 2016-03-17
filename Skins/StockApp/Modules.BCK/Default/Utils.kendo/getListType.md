					[SWITCH [!E::type!]|=]
						[CASE int]
							 {
								"mData" : "[!E::name!]",
								"sTitle" : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
								"sWidth": '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]50[/IF]',
								"mRender" : function(data, type, full) {
									if (data>0)
										return '<div class="badge badge-success special">' + data + '</div>';
									else 
										return '<div class="badge special badge-warning">0</div>';
								}
							}
						[/CASE]
						[CASE image]
							 {
								"mData" : "[!E::name!]",
								"sTitle" : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
								"sWidth": '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]50[/IF]',
								"mRender" : function(data, type, full) {
									if (data!='')
										return '<a href="/'+data+'"><img src="/' + data + '.mini.150x28.jpg" /></div>';
									else 
										return '<div class="badge special">No file</div>';
								}
							}
						[/CASE]
						[CASE date]
							{
								"mData" : "[!E::name!]",
								"sTitle" : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
								"sWidth": '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]50[/IF]'
							}
						[/CASE]
						[CASE boolean]
							{
								"mData" : "[!E::name!]",
								"sTitle" : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
								"sWidth": '5%',
								"mRender" : function(data, type, full) {
									if (data=="1"){
										return '<div  class="badge btn-success special">OK</a>';
									}else{
										return '<div  class="badge btn-danger special">NO</a>';
									}
								}
							}
						[/CASE]
						[DEFAULT]
							{
								"mData" : "[!E::name!]",
								"sTitle" : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
								"sWidth": '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]50[/IF]'
							}
						[/DEFAULT]
					[/SWITCH]