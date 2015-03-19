/**
 * example:
 * <div class="dataItem" data-src="/[!El::objectModule!]/[!El::objectName!]/[!P::ObjectType!]/[!P::Id!]/getJsonDatatable.json" data-module="[!El::objectModule!]" data-objectclass="[!El::objectName!]" data-interface="getJsonDatatable.json"  data-var="listdep_[!El::objectName!]" data-icon="[!Pa::getIcone()!]" data-title="[!Pa::getDescription()!]" data-form="/[!P::getUrl()!]/[!Pa::ObjectType!]" data-description="[!El::description!]" data-key="[!El::name!]"></div>
 */

var DataItem = $.inherit({
	/**
	 * Constructor 
	 */
	__constructor : function(item){
		this.item = item;
		//récupératio ndes informations de l'item
		//source de donnée
		this.src=$(item).attr('data-src');
		//icone
		this.icon=$(item).attr('data-icon');
		//titre de la donnée
		this.title=$(item).attr('data-title');
		//url du formulaire à charger
		this.form=$(item).attr('data-form');
		//nom de la clef
		this.key=$(item).attr('data-key');
		//nom de la variable a recupérer
		this.vars=$(item).attr('data-var');
		//module
		this.module = $(item).attr('data-module');
		//objectclass
		this.objectclass = $(item).attr('data-objectclass');
		//interface de donnée
		this.interfaces = $(item).attr('data-interface');
		//titre de la fenêtre
		this.description = $(item).attr('data-description');
		//execution de la requete
		this.getData(this.src);
	},
	/**
	 * getData
	 * Execute une requete et affiche en suite le résultat 
 	 */
	getData : function (query){
		//suppression du contenu
		this.item.empty();
		var t = this;
		//execution de la requete
		$.getJSON(query+'?sEcho=1').done( function( dat ) {
			var items = [];
			$.each( dat.aaData, function( key, val ) {
				items.push(t.addItem(val));
			});
			//creatio nd'une reference vers cette classe'
			var it = $(items.join( "" ));
			it.on('form_valid',function (event, form){
				t.item.empty();
				$.each(form,function (index,item){
					if (item["name"]==t.vars)
						t.setTemporaryData(item["value"]);
				});
			});
			it.appendTo( t.item );
			//o déclenche le détecteur de modales
			launch_modal_form_popup();
		});
	},
	addItem: function (val) {
		var data 	= 	'<div class="media dataItem-item ke-form-modal" href="'+this.form+'/Deplacer.htm" data-var="dataItem" title="Redéfinir '+this.description+'">';
		data 	+=	'	<a class="pull-left">';
		data 	+=	'		<img class="media-object" src="'+this.icon+'">';
		data 	+=	'		<input type="hidden" name="'+this.key+'[]" value="'+val["Id"]+'">';
		data 	+=	'	</a>';
		data 	+=	'	<div class="media-body">';
		data 	+=	'		<h4 class="media-heading">'+this.title+'</h4>';
		data 	+=	'		<div class="media">';
		var i = 0;
		$.each(val,function (index,val2){
			if (i>0) data+=", ";
			data 	+=	'<strong>'+index+'</strong>:'+val2;
			i++;
		});
		data 	+=	'		</div>';
		data 	+=	'	</div>';
		data 	+=	'</div>';
		return data;
	},
	setTemporaryData : function (f){
		var t = this;
		$.getJSON('/'+this.module+'/'+this.objectclass+'/'+f+'/'+this.interfaces+'?sEcho=1').done( function( dat ) {
			var items = [];
			$.each( dat.aaData, function( key, val ) {
				items.push(t.addItem(val));
			});
			//creatio nd'une reference vers cette classe'
			var it = $(items.join( "" ));
			it.on('form_valid',function (event, form){
				t.item.empty();
				$.each(form,function (index,item){
					if (item["name"]==t.vars)
						t.setTemporaryData(item["value"]);
				});
			});
			it.appendTo( t.item );
			//o déclenche le détecteur de modales
			launch_modal_form_popup();
		});
	}
});

/**
 * Execution du script
 * 
 */
function parse_dataItem(){
	$('.dataItem').each(function (index,item){
		$(item).data("dataItem",new DataItem($(item)));
	});
}
parse_dataItem();
