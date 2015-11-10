[INFO [!Query!]|I]
//generation object
[OBJ [!I::Module!]|[!I::TypeChild!]|P]

Ext.define('ActivaStock.item.Liste[!I::TypeChild!]Item', {
    extend: 'Ext.dataview.component.DataItem',
    xtype : 'liste[!I::TypeChild!]Item',

    requires: [
        'Ext.Img',
        'ActivaStock.item.Liste[!I::TypeChild!]ItemDetail'
    ],

    config: {
            [STORPROC [!P::getElementsByAttribute(type,image,1)!]|El|0|1]
        cls: 'img-list-item',
        [NORESULT]
        cls: 'list-item',
        [/NORESULT]
            [/STORPROC]

        dataMap: {
            [STORPROC [!P::getElementsByAttribute(type,image,1)!]|El|0|1]
            getImage: {
                setSrc: '[!El::name!]'
            },
            [/STORPROC]

            getDetail: {
                [STORPROC [!P::getElementsByAttribute(searchOrder,,1)!]|El|0|6]
                [IF [!Pos!]>1],[/IF]
                set[!El::name!]: '[!El::name!]'
                [/STORPROC]
            }
        },

        [STORPROC [!P::getElementsByAttribute(type,image,1)!]|El|0|1]
        image: {
            width: 200,
            height: 80,
            showAnimation: 'slideIn',
            rounded: true
        },
        [/STORPROC]

        detail: {
            cls: 'list-item-detail',
            flex: 1
        },
        layout: {
            type: 'hbox',
            align: 'center'
        },
        listeners: {
            'updatedata': function (that, newData, eOpts) {
                console.log ('ITEM '+this.getRecord().getFlatClassName()+' Update data', newData);
                this.getDetail().updateRecord(this.getRecord());
            },
            'painted': function (that, newData, eOpts) {
                var dv = this._dataview;
                this.getDetail().updateStore(dv.getStore());
            }
        }
    },

    [STORPROC [!P::getElementsByAttribute(type,image,1)!]|El|0|1]
    applyImage: function(config) {
        return Ext.factory(config, Ext.Img, this.getImage());
    },

    updateImage: function(newImage, oldImage) {
        if (newImage) {
            this.add(newImage);
        }

        if (oldImage) {
            this.remove(oldImage);
        }
    },
    [/STORPROC]

    applyDetail: function(config) {
         return Ext.factory(config, ActivaStock.item.Liste[!I::TypeChild!]ItemDetail, this.getDetail());
    },

    updateDetail: function(newDetail, oldDetail) {
        if (newDetail) {
            this.add(newDetail);
        }

        if (oldDetail) {
            this.remove(oldDetail);
        }
    }
});