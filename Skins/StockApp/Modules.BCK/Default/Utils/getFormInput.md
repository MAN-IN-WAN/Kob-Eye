                [SWITCH [!El::type!]|=]
                    [CASE boolean]
                            {
                                xtype: 'togglefield',
                                name : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]'
                            }
                    [/CASE]
                    [CASE text]
                            {
                                xtype: 'textareafield',
                                name : '[!El::name!]',
                                maxRows: 10,
                                label: '[JSON][!El::description!][/JSON]'
                            }
                    [/CASE]
                    [CASE html]
                            {
                                xtype: 'textareafield',
                                name : '[!El::name!]',
                                maxRows: 10,
                                label: '[JSON][!El::description!][/JSON]'
                            }
                    [/CASE]
                    [CASE bbcode]
                            {
                                xtype: 'textareafield',
                                name : '[!El::name!]',
                                maxRows: 10,
                                label: '[JSON][!El::description!][/JSON]'
                            }
                    [/CASE]
                    [CASE password]
                            {
                                xtype: 'passwordfield',
                                name : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                clearIcon  : !Ext.theme.is.Blackberry
                            }
                    [/CASE]
                    [CASE email]
                            {
                                xtype      : 'emailfield',
                                name       : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                placeHolder: 'me@sencha.com',
                                clearIcon  : true
                            }
                    [/CASE]
                    [CASE url]
                            {
                                xtype      : 'urlfield',
                                name       : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                placeHolder: 'http://sencha.com',
                                clearIcon  : true
                            }
                    [/CASE]
                    [CASE int]
                            {
                                xtype      : 'spinnerfield',
                                name       : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                minValue   : 0,
                                maxValue   : [!El::value:+100!],
                                stepValue  : 1,
                                cycle      : true
                            }
                    [/CASE]
                    [CASE fkey]
                            {
                                xtype: 'selectfield',
                                name : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
//                                [!BRACK:=]!]
//                                /*options: [
//                                    [STORPROC [!El::objectModule!]/[!El::objectName!]|Op|0|10000]
//                                        {
//                                            text : '[!Op::getFirstSearchOrder()!]',
//                                            value: '[!Op::Id!]'
//                                        }[IF [!Pos!]<[!NbResult!]],[/IF]
//                                    [/STORPROC]
//                                 [!BRACK!]*/
                                displayField: 'Nom',
                                valueField: 'id',
                                store: '[!El::objectName!]s'
                            }
                
                    [/CASE]
                    [CASE date]
                            {
                                xtype: 'datepickerfield',
                                destroyPickerOnHide: true,
                                name : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                value: '[!El::value!]',
                                picker: {
                                    yearFrom: 1990
                                }
                            }
                    [/CASE]
                    [DEFAULT]
                        [IF [!El::values!]]
                            {
                                xtype: 'selectfield',
                                name : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                options: [
                                [STORPROC [!El::values:/,!]|Op]
                                    [IF [!Array::isArray([!Op:/::!])!]]
                                        [!Op2:=[!Op:/::!]!]
                                        {
                                            text : '[!Op2::0!]',
                                            value: '[!Op2::1!]'
                                        }
                                    [ELSE]
                                        {
                                            text : '[!Op!]',
                                            value: '[!Op!]'
                                        }
                                    [/IF]
                                [/STORPROC]
                                ]
                            }
                        [ELSE]
                            {
                                xtype         : 'textfield',
                                name : '[!El::name!]',
                                label: '[JSON][!El::description!][/JSON]',
                                placeHolder   : '[!El::value!]',
                                autoCapitalize: true,
                                required      : [!El::obligatoire!],
                                clearIcon     : true
                            }
                        [/IF]
                    [/DEFAULT]
                [/SWITCH]
