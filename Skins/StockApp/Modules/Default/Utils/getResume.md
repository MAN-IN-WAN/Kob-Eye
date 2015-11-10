[INFO [!Query!]|I]
//generation object
[OBJ [!I::Module!]|[!I::TypeChild!]|P]


Ext.define('ActivaStock.fiche.Resume[!I::TypeChild!]', {
    extend: 'Ext.form.Panel',
    requires:[
        'Ext.Img'
    ],
    xtype: 'resume[!I::TypeChild!]',
    config: {
        title:'Resume',
        iconCls: 'info',
        items: [
            {
                xtype: 'panel',
                title: 'Détails',
                items: [
                    {
                        xtype: 'container',
                        layout: {
                          type: 'hbox',
                          pack: 'left'
                        },
                        items:[
                            {
                                xtype: 'image',
                                width: '200',
                                height: '200',
                                flex: 1,
                            },
                            {
                                xtype: 'container',
                                flex: 5,
                                layout: {
                                  type: 'vbox',
                                  pack: 'center'
                                },
                                items:[
                                    {
                                        xtype: 'container',
                                        layout: {
                                          type: 'vbox',
                                          pack: 'center'
                                        },
                                    },
                                    {
                                        xtype: 'container',
                                        layout: {
                                          type: 'vbox',
                                          pack: 'center'
                                        },
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    },
    updateRecord: function (record) {
/*        console.log('image '+record.get('Image'));
        this.down('#fichearticleresumeimage').setSrc(record.get('Image'));
        this.down('#fichearticleresumenom').setHtml('<h1>'+record.get('Nom')+'</h1>');
        this.down('#fichearticleresumereference').setHtml('<p>'+record.get('Reference')+'</p>');
        
        //declenche masonry
        this.on( {
            "painted" : function() {
                var $container = $('.masonry');
                // init
                $container.isotope({
                  // options
                  itemSelector: '.masonry-item',
                  layoutMode: 'masonry'
                });
            }
        });  */
    }
});
