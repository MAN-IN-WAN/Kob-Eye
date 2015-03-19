/*
Mif.Tree
*/
if(!Mif) var Mif={};

Mif.Tree = new Class({

	Implements: [new Events, new Options],
		
	options:{
		types: {},
		forest: false,
		animateScroll: true,
		height: 18,
		expandTo: true
	},
	
	initialize: function(options) {
		this.setOptions(options);
		$extend(this, {
			types: this.options.types,
			forest: this.options.forest,
			animateScroll: this.options.animateScroll,
			dfltType: this.options.dfltType,
			height: this.options.height,
			container: $(options.container),
			UID: 0,
			key: {},
			expanded: []
		});
		this.defaults={
			name: '',
			cls: '',
			openIcon: 'mif-tree-empty-icon',
			closeIcon: 'mif-tree-empty-icon',
			loadable: false
		};
		this.dfltState={
			open: false
		};
		this.updateOpenState();
		if(this.options.expandTo) this.initExpandTo();
		Mif.Tree.UID++;
		this.DOMidPrefix='mif-tree-';
		this.wrapper=new Element('div').addClass('mif-tree-wrapper').injectInside(this.container);
		this.initEvents();
		this.initScroll();
		this.initSelection();
		this.initHover();
	},
	
	initEvents: function(){
		this.wrapper.addEvents({
			mousemove: this.mouse.bindWithEvent(this),
			mouseover: this.mouse.bindWithEvent(this),
			mouseout: this.mouse.bindWithEvent(this),
			mouseleave: this.mouseleave.bind(this),
			mousedown: function(){this.fireEvent('mousedown'); return false;}.bind(this),
			click: this.toggleClick.bindWithEvent(this),
			dblclick: this.toggleDblclick.bindWithEvent(this),
			keydown: this.keyDown.bindWithEvent(this),
			keyup: this.keyUp.bindWithEvent(this)
		});
		if(Browser.Engine.trident){
			this.wrapper.addEvent('selectstart', $lambda(false));
		}
	},
	
	$getIndex: function(){//return array of visible nodes.
		this.$index=[];
		var node=this.forest ? this.root.getFirst() : this.root;
		do{
			this.$index.push(node);
		}while(node=node.getNextVisible());
	},
	
	mouseleave: function(){
		this.mouse.coords={x:null,y:null};
		this.mouse.target=false;
		this.mouse.node=false;
		if(this.hover) this.hover();
	},
	
	mouse: function(event){
		this.mouse.coords=this.getCoords(event);
		var target=this.getTarget(event);
		this.mouse.target=target.target;
		this.mouse.node	= target.node;
	},
	
	getTarget: function(event){
		var target=event.target, node;
		while(!/mif-tree/.test(target.className)){
			target=target.parentNode;
		}
		var test=target.className.match(/mif-tree-(gadjet)-[^n]|mif-tree-(icon)|mif-tree-(name)|mif-tree-(checkbox)/);
		if(!test){
			var y=this.mouse.coords.y;
			if(y==-1||!this.$index) {
				node=false;
			}else{
				node=this.$index[((y)/this.height).toInt()];
			}
			return {
				node: node,
				target: 'node'
			};
		}
		for(var i=5;i>0;i--){
			if(test[i]){
				var type=test[i];
				break;
			}
		}
		return {
			node: Mif.Tree.Nodes[target.getAttribute('uid')],
			target: type
		};
	},
	
	getCoords: function(event){
		var position=this.wrapper.getPosition();
		var x=event.page.x-position.x;
		var y=event.page.y-position.y;
		var wrapper=this.wrapper;
		if((y-wrapper.scrollTop>wrapper.clientHeight)||(x-wrapper.scrollLeft>wrapper.clientWidth)){//scroll line
			y=-1;
		};
		return {x:x, y:y};
	},
	
	keyDown: function(event){
		this.key=event;
		this.key.state='down';
	},
	
	keyUp: function(event){
		this.key={};
		this.key.state='up';
	},
	
	toggleDblclick: function(event){
		var target=this.mouse.target;
		if(!(target=='name'||target=='icon')) return;
		this.mouse.node.toggle();
	},
	
	toggleClick: function(event){
		if(this.mouse.target!='gadjet') return;
		this.mouse.node.toggle();
	},
	
	initScroll: function(){
		this.scroll=new Fx.Scroll(this.wrapper);
	},
	
	scrollTo: function(node){
		var position=node.getVisiblePosition();
		var top=position*this.height;
		var up=top<this.wrapper.scrollTop;
		var down=top>(this.wrapper.scrollTop+this.wrapper.clientHeight);
		if(position==-1 || ( !up && !down ) ) {
			this.scroll.fireEvent('complete');
			return false;
		}
		if(this.animateScroll){
			this.scroll.start(this.wrapper.scrollLeft, top-(down ? this.wrapper.clientHeight-this.height : 0));
		}else{
			this.scroll.set(this.wrapper.scrollLeft, top-(down ? this.wrapper.clientHeight-this.height : 0));
			this.scroll.fireEvent('complete');
		}
	},
	
	updateOpenState: function(){
		this.addEvents({
			'drawChildren': function(parent){
				var children=parent.children;
				for(var i=0, l=children.length; i<l; i++){
					children[i].updateOpenState();
				}
			},
			'drawRoot': function(){
				this.root.updateOpenState();
			}
		});
	},
	
	expandTo: function(node){
		if (!node) return this;
		var path = [];
		while( !node.isRoot() && !(this.forest && node.getParent().isRoot()) ){
			node=node.getParent();
			if(!node) break;
			path.unshift(node);
		};
		path.each(function(el){
			el.toggle(true)
		});
		return this;
	},
	
	initExpandTo: function(){
		this.addEvent('loadChildren', function(parent){
			if(!parent) return;
			var children=parent.children;
			for( var i=children.length; i--; ){
				var child=children[i];
				if(child.expandTo) this.expanded.push(child);
			}
		});
		function expand(){
			this.expanded.each(function(node){
				this.expandTo(node);
			}, this);
			this.expanded=[];
		};
		this.addEvents({
			'load': expand.bind(this),
			'loadNode': expand.bind(this)
		});
	}
	
});
Mif.Tree.UID=0;

Mif.Tree.version='1.1';


/*
Mif.Tree.Node
*/
Mif.Tree.Node = new Class({

	$family: {name: 'mif:tree:node'},

	Implements: [new Events],
	
	initialize: function(structure, options) {
		$extend(this, structure);
		this.children=[];
		this.type=options.type||this.tree.dfltType;
		this.property=options.property;
		this.data=options.data;
		this.state=$extend($unlink(this.tree.dfltState), options.state);
		this.$calculate();
		
		this.UID=Mif.Tree.Node.UID++;
		Mif.Tree.Nodes[this.UID]=this;
	},
	
	$calculate: function(){
		$extend(this, $unlink(this.tree.defaults));
		this.type=$splat(this.type);
		this.type.each(function(type){
			var props=this.tree.types[type];
			if(props) $extend(this, props);
		}, this);
		$extend(this, this.property);
	},
	
	getDOM: function(what){
		var node=$(this.tree.DOMidPrefix+this.UID);
		if(what=='node') return node;
		var wrapper=node.getFirst();
		if(what=='wrapper') return wrapper;
		if(what=='children') return wrapper.getNext();
		return wrapper.getElement('.mif-tree-'+what);
	},
	
	getGadjetType: function(){
		return (this.loadable && !this.isLoaded()) ? 'plus' : (this.hasChildren() ? (this.isOpen() ? 'minus' : 'plus') : 'none');
	},
	
	toggle: function(state) {
		if(this.state.open==state || this.$loading || this.$toggling) return;
		if(this.loadable && !this.state.loaded) {
            if(!this.load_event){
                this.load_event=true;
                this.addEvent('load',function(){
                    this.toggle();
                }.bind(this));
            }
            this.load();
            return;
        }
		if(!this.hasChildren()) return;
		var next=this.getNextVisible();
		this.state.open = !this.state.open;
		state=this.state.open;
		if(!this.$draw) Mif.Tree.Draw.children(this);
		var children=this.getDOM('children');	
		var gadjet=this.getDOM('gadjet');
		var icon=this.getDOM('icon');
		children.style.display=this.isOpen() ? 'block' : 'none';
		gadjet.className='mif-tree-gadjet mif-tree-gadjet-'+this.getGadjetType();
		icon.className='mif-tree-icon '+this[this.isOpen() ? 'openIcon' : 'closeIcon'];
		this.tree.hoverState.gadjet=false;
		this.tree.hover();
		this.tree.$getIndex();
		this.tree.fireEvent('toggle', [this, this.state.open]);
	},
	
	recursive: function(fn, args){
		args=$splat(args);
		if(fn.apply(this, args)!==false){
			this.children.each(function(node){
				if(node.recursive(fn, args)===false){
					return false;
				}
			});
		}
		return this;
	},
	
	isOpen: function(){
		return this.state.open;
	},
	
	isLoaded: function(){
		return this.state.loaded;
	},
	
	isLast: function(){
		if(this.parentNode==null || this.parentNode.children.getLast()==this) return true;
		return false;
	},
	
	isFirst: function(){
		if(this.parentNode==null || this.parentNode.children[0]==this) return true;
		return false;
	},
	
	isRoot: function(){
		return this.parentNode==null ? true : false;
	},
	
	getChildren: function(){
		return this.children;
	},
	
	hasChildren: function(){
		return this.children.length ? true : false;
	},
	
	index: function(){
		if( this.isRoot() ) return 0;
		return this.parentNode.children.indexOf(this);
	},
	
	getNext: function(){
		if(this.isLast()) return null;
		return this.parentNode.children[this.index()+1];
	},
	
	getPrevious: function(){
		if( this.isFirst() ) return null;
		return this.parentNode.children[this.index()-1];
	},
	
	getFirst: function(){
		if(!this.hasChildren()) return null;
		return this.children[0];
	},
	
	getLast: function(){
		if(!this.hasChildren()) return null;
		return this.children.getLast();		
	},
	
	getParent: function(){
		return this.parentNode;
	},
	
	getNextVisible: function(){
		var current=this;
		if(current.isRoot()){
			if(!current.isOpen() || !current.hasChildren()) return false;
			return current.getFirst();
		}else{
			if(current.isOpen() && current.getFirst()){
				return current.getFirst();
			}else{
				var parent=current;
				do{
					current=parent.getNext();
					if(current) return current;
				}while( parent=parent.parentNode );
				return false;
			}
		}
	},
	
	getPreviousVisible: function(){
		var current=this;
		if( current.isFirst() && ( !current.parentNode || (current.tree.forest && current.parentNode.isRoot()) ) ){
			return false;
		}else{
			if( current.getPrevious() ){
				current=current.getPrevious();
				while( current.isOpen() && current.getLast() ){
					current=current.getLast();
				}
				return current;
			}else{
				return current.parentNode;
			}
		}
	},
	
	getVisiblePosition: function(){
		return this.tree.$index.indexOf(this);
	},
		
	contains: function(node){
		do{
			if(node==this) return true;
			node=node.parentNode;
		}while(node);
		return false;
	},

	addType: function(type){
		this.type.include(type);
		this.$calculate();
		Mif.Tree.Draw.update(this);
		return this;
	},

	removeType: function(type){
		this.type.erase(type);
		this.$calculate();
		Mif.Tree.Draw.update(this);
		return this;
	},
	
	set: function(props){
		this.tree.fireEvent('beforeSet', [this]);
		$extend(this, props);
		if(props.property||props.type||props.state){
			this.$calculate();
			Mif.Tree.Draw.update(this);
		}
		this.tree.fireEvent('set', [this, props]);
	},
	
	updateOpenState: function(){
		if(this.state.open){
			this.state.open=false;
			this.toggle();
		}
	}
	
});

Mif.Tree.Node.UID=0;
Mif.Tree.Nodes={};

/*
Mif.Tree.Draw
*/
Mif.Tree.Draw={

	getHTML: function(node,html){
		var prefix=node.tree.DOMidPrefix;
		if($defined(node.state.checked)){
			if(!node.hasCheckbox) node.state.checked='nochecked';
			var checkbox='<span class="mif-tree-checkbox mif-tree-node-'+node.state.checked+'" uid="'+node.UID+'">'+Mif.Tree.Draw.zeroSpace+'</span>';
		}else{
			var checkbox='';
		}
		html=html||[];
		html.push(
		'<div class="mif-tree-node ',(node.isLast() ? 'mif-tree-node-last' : ''),'" id="',prefix,node.UID,'">',
			'<span class="mif-tree-node-wrapper ',node.cls,'" uid="',node.UID,'">',
				'<span class="mif-tree-gadjet mif-tree-gadjet-',node.getGadjetType(),'" uid="',node.UID,'">',Mif.Tree.Draw.zeroSpace,'</span>',
				checkbox,
				'<span class="mif-tree-icon ',node.closeIcon,'" uid="',node.UID,'">',Mif.Tree.Draw.zeroSpace,'</span>',
				'<span class="mif-tree-name" uid="',node.UID,'">',node.name,'</span>',
			'</span>',
			'<div class="mif-tree-children" style="display:none"></div>',
		'</div>'
		);
		return html;
	},
	
	children: function(parent, container){
		parent.open=true;
		parent.$draw=true;
		var html=[];
		var children=parent.children;
		for(var i=0,l=children.length;i<l;i++){
			this.getHTML(children[i],html);
		}
		container=container || parent.getDOM('children');
		container.set('html', html.join(''));
		parent.tree.fireEvent('drawChildren',[parent]);
	},
	
	root: function(tree){
		var domRoot=this.node(tree.root);
		domRoot.injectInside(tree.wrapper);
		tree.fireEvent('drawRoot');
	},
	
	forestRoot: function(tree){
		var container=new Element('div').addClass('mif-tree-children-root').injectInside(tree.wrapper);
		Mif.Tree.Draw.children(tree.root, container);
	},
	
	node: function(node){
		return new Element('div').set('html', this.getHTML(node).join('')).getFirst();
	},
	
	update: function(node){
		if(!node) return;
		if( (node.tree.forest && node.isRoot()) || (node.getParent() && !node.getParent().$draw) ) return;
		if(!node.hasChildren()) node.state.open=false;
		node.getDOM('name').set('html', node.name);
		node.getDOM('wrapper').className='mif-tree-node-wrapper '+node.cls;
		node.getDOM('gadjet').className='mif-tree-gadjet mif-tree-gadjet-'+node.getGadjetType();
		node.getDOM('icon').className='mif-tree-icon '+node[node.isOpen() ? 'openIcon' : 'closeIcon'];
		node.getDOM('node')[(node.isLast() ?'add' : 'remove')+'Class']('mif-tree-node-last');
		node.select(node.isSelected());
		node.tree.updateHover();
		if(node.$loading) return;
		var children=node.getDOM('children');
		children.className='mif-tree-children';
		if(node.isOpen()){
			if(!node.$draw) Mif.Tree.Draw.children(node);
			children.style.display='block';
		}else{
			children.style.display='none';
		}
		node.tree.fireEvent('updateNode', node);
		return node;
	},
	
	updateDOM: function(node, domNode){
		domNode= domNode||node.getDOM('node');
		var previous=node.getPrevious();
		if(previous){
			domNode.injectAfter(previous.getDOM('node'));
		}else{
			if(node.tree.forest && node.parentNode.isRoot()){
				var children=node.tree.wrapper.getElement('.mif-tree-children-root');
			}else{
				var children=node.parentNode.getDOM('children');
			}
			domNode.injectTop(children);
		}
	}
	
};
Mif.Tree.Draw.zeroSpace=Browser.Engine.trident ? '&shy;' : (Browser.Engine.webkit ? '&#8203' : '');


/*
Mif.Tree.Selection
*/
Mif.Tree.implement({
	
	initSelection: function(){
		this.defaults.selectClass='';
		this.wrapper.addEvent('mousedown', this.attachSelect.bindWithEvent(this));
	},
	
	attachSelect: function(event){
		if(!['icon', 'name', 'node'].contains(this.mouse.target)) return;
		var node=this.mouse.node;
		if(!node) return;
		this.select(node);
	},
	
	select: function(node, preventFocus) {
		if(!preventFocus && (Browser.Engine.gecko||Browser.Engine.webkit)) {
			this.wrapper.focus();
		}
		var current=this.selected;
		if (current==node) return this;
		if (current) {
			current.select(false);
			this.fireEvent('unSelect', [current]).fireEvent('selectChange', [current, false]);
		}
		this.selected = node;
		node.select(true);
		this.fireEvent('select', [node]).fireEvent('selectChange', [node, true]);
		return this;
	},
	
	unselect: function(){
		var current=this.selected;
		if(!current) return this;
		this.selected=false;
		current.select(false);
		this.fireEvent('unSelect', [current]).fireEvent('selectChange', [current, false]);
		return this;
	},
	
	getSelected: function(){
		return this.selected;
	},
	
	isSelected: function(node){
		return node.isSelected();
	}
	
});

Mif.Tree.Node.implement({
		
	select: function(state) {
		this.state.selected = state;
		var wrapper=this.getDOM('wrapper');
		wrapper[(state ? 'add' : 'remove')+'Class'](this.selectClass||'mif-tree-node-selected');
	},
	
	isSelected: function(){
		return this.state.selected;
	}
	
});


/*
Mif.Tree.Hover
*/
Mif.Tree.implement({
	
	initHover: function(){
		this.defaults.hoverClass='';
		this.wrapper.addEvent('mousemove', this.hover.bind(this));
		this.wrapper.addEvent('mouseout', this.hover.bind(this));
		this.defaultHoverState={
			gadjet: false,
			checkbox: false,
			icon: false,
			name: false,
			node: false
		};
		this.hoverState=$unlink(this.defaultHoverState);
	},
	
	hover: function(){
		var cnode=this.mouse.node;
		var ctarget=this.mouse.target;
		$each(this.hoverState, function(node, target, state){
			if(node==cnode && (target=='node'||target==ctarget)) return;
			if(node) {
				Mif.Tree.Hover.out(node, target);
				state[target]=false;
				this.fireEvent('hover', [node, target, 'out']);
			}
			if(cnode && (target=='node'||target==ctarget)) {
				Mif.Tree.Hover.over(cnode, target);
				state[target]=cnode;
				this.fireEvent('hover', [cnode, target, 'over']);
			}else{
				state[target]=false;
			}
		}, this);
	},
	
	updateHover: function(){
		this.hoverState=$unlink(this.defaultHoverState);
		this.hover();
	}
	
});

Mif.Tree.Hover={
	
	over: function(node, target){
		var wrapper=node.getDOM('wrapper');
		wrapper.addClass((node.hoverClass||'mif-tree-hover')+'-'+target);
		if(node.state.selected) wrapper.addClass((node.hoverClass||'mif-tree-hover')+'-selected-'+target);
	},
	
	out: function(node, target){
		var wrapper=node.getDOM('wrapper');
		wrapper.removeClass((node.hoverClass||'mif-tree-hover')+'-'+target).removeClass((node.hoverClass||'mif-tree-hover')+'-selected-'+target);
	}
	
};


/*
Mif.Tree.Load
*/
Mif.Tree.Load={
		
	children: function(children, parent, tree){
		for( var i=children.length; i--; ){
			var child=children[i];
			var subChildren=child.children;
			delete child.children;
			var node=new Mif.Tree.Node({
				tree: tree,
				parentNode: parent||undefined
			}, child);
			if( tree.forest || parent != undefined){
				parent.children.unshift(node);
			}else{
				tree.root=node;
			}
			if(subChildren && subChildren.length){
				arguments.callee(subChildren, node, tree);
			}
		}
		if(parent) parent.state.loaded=true;
		tree.fireEvent('loadChildren', parent);
	}
	
};

Mif.Tree.implement({

	load: function(options){
		var tree=this;
		this.loadOptions=this.loadOptions||$lambda({});
		function success(json){
			if(tree.forest){
				tree.root=new Mif.Tree.Node({
					tree: tree,
					parentNode: null
				}, {});
				var parent=tree.root;
			}else{
				var parent=null;
			}
			Mif.Tree.Load.children(json, parent, tree);
			Mif.Tree.Draw[tree.forest ? 'forestRoot' : 'root'](tree);
			tree.$getIndex();
			tree.fireEvent('load');
			return tree;
		}
		options=$extend($extend({
			isSuccess: $lambda(true),
			secure: true,
			onSuccess: success,
			method: 'get'
		}, this.loadOptions()), options);
		if(options.json) return success(options.json);
		new Request.JSON(options).send();
		return this;
	}
	
});

Mif.Tree.Node.implement({
	
	load: function(options){
		this.$loading=true;
		options=options||{};
		this.addType('loader');
		var self=this;
		function success(json){
			Mif.Tree.Load.children(json, self, self.tree);
			delete self.$loading;
			self.state.loaded=true;
			self.removeType('loader');
			self.fireEvent('load');
			self.tree.fireEvent('loadNode', self);
			return self;
		}
		options=$extend($extend($extend({
			isSuccess: $lambda(true),
			secure: true,
			onSuccess: success,
			method: 'get'
		}, this.tree.loadOptions(this)), this.loadOptions), options);
		if(options.json) return success(options.json);
		new Request.JSON(options).send();
		return this;
	}
	
});


/*mootools patch*/

if(document.documentElement.getBoundingClientRect){//ie, opear9.5+, ff3+

	Element.implement({

		getPosition: function(relative){
			rect=this.getBoundingClientRect();
			var clientTop = document.html.clientTop || document.body.clientTop || 0, clientLeft = document.html.clientLeft || document.body.clientLeft || 0
			var position={x: rect.left-this.scrollLeft-clientLeft, y:rect.top-this.scrollTop-clientTop};
			var relativePosition = (relative && (relative = $(relative))) ? relative.getPosition() : {x: 0, y: 0};
			return {x: position.x - relativePosition.x, y: position.y - relativePosition.y};
		}
		
	});

}

/*
Mif.Tree.KeyNav
*/
Mif.Tree.KeyNav=new Class({
	
	initialize: function(tree){
		this.tree=tree;
		tree.wrapper.setAttribute('tabIndex',1);
		tree.wrapper.addEvent('keydown',function(event){
			if(!['down','left','right','up'].contains(event.key)) return;
			if(!tree.selected){
				tree.select(tree.forest ? tree.root.getFirst() : tree.root);
			}else{
				var current=tree.selected;
				switch (event.key){
					case 'down': this.goForward(current);event.stop();break;  
					case 'up': this.goBack(current);event.stop();break;   
					case 'left': this.goLeft(current);event.stop();break;
					case 'right': this.goRight(current);event.stop();break;
				}
			}
			var height=tree.height;
			function autoScroll(){
				var wrapper=tree.wrapper;
				var i=tree.selected.getVisiblePosition();
				var top=i*height-wrapper.scrollTop;
				var bottom=top+height;
				if(top<height){
					wrapper.scrollTop-=height;
				}
				if(wrapper.offsetHeight-bottom<height){
					wrapper.scrollTop+=height;
				}
			}
			autoScroll();
		}.bind(this));
	},

	goForward: function(current){
		var forward=current.getNextVisible();
		if( forward ) this.tree.select(forward)
	},
	
	goBack: function(current){
		var back=current.getPreviousVisible();
		if (back) this.tree.select(back);
	},
	
	goLeft: function(current){
		if(current.isRoot()){
			if(current.isOpen()){
				current.toggle();
			}else{
				return false;
			}
		}else{
			if( current.hasChildren() && current.isOpen() ){
				current.toggle();
			}else{
				if(current.tree.forest && current.getParent().isRoot()) return false;
				return this.tree.select(current.getParent());
			}
		}
	},
	
	goRight: function(current){
		if(!current.hasChildren()&&!current.loadable){
			return false;
		}else if(!current.isOpen()){
			return current.toggle();
		}else{
			return this.tree.select(current.getFirst());
		}
	}
});


/*
Mif.Tree.Sort
*/
Mif.Tree.implement({
	
	initSortable: function(sortFunction){
		this.sortable=true;
		this.sortFunction=sortFunction||function(node1, node2){
			if(node1.name>node2.name){
				return 1;
			}else if(node1.name<node2.name){
				return -1;
			}else{
				return 0;
			}
		};
		this.addEvent('loadChildren', function(parent){
			if(parent) parent.sort();
		});
		this.addEvent('structureChange', function(from, to, where, type){
			from.sort();
		});
		return this;
	}
	
});


Mif.Tree.Node.implement({

	sort: function(sortFunction){
		this.children.sort(sortFunction||this.tree.sortFunction);
		return this;
	}
	
});


/*
Mif.Tree.Transform
*/
Mif.Tree.Node.implement({
	
	inject: function(node, where, domNode){//domNode - internal property
		var parent=this.parentNode;
		var previous=this.getPrevious();
		var type=domNode ? 'copy' : 'move';
		switch(where){
			case 'after':
			case 'before':
				if( node['get'+(where=='after' ? 'Next' : 'Previous')]()==this ) return false;
				if(this.parentNode) this.parentNode.children.erase(this);
				this.parentNode=node.parentNode;
				this.parentNode.children.inject(this, node, where);
				break;
			case 'inside':
				if( node.getLast()==this ) return false;
				if(this.parentNode) this.parentNode.children.erase(this);
				node.children.push(this);
				this.parentNode=node;
				node.$draw=true;
				node.state.open=true;
				break;
		}		
		var tree=node.tree.unselect();
		if(this.tree!=node.tree){
			var oldTree=this.tree.unselect();
			this.tree=node.tree;
		};
		tree.fireEvent('structureChange', [this, node, where, type]);
		Mif.Tree.Draw.updateDOM(this, domNode);
		[node, this, parent, previous, this.getPrevious()].each(function(node){
			Mif.Tree.Draw.update(node);
		});
		tree.$getIndex();
		if(oldTree)	oldTree.$getIndex();
		tree.select(this).scrollTo(this);
		return this;
	},
	
	copy: function(node, where){
		function copy(structure){
			var node=structure.node;
			var tree=structure.tree;
			var options=$unlink({
				property: node.property,
				type: node.type,
				state: node.state,
				data: node.data
			});
			options.state.open=false;
			var nodeCopy = new Mif.Tree.Node({
				parentNode: structure.parentNode,
				children: [],
				tree: tree
			}, options);
			node.children.each(function(child){
				var childCopy=copy({
					node: child,
					parentNode: nodeCopy,
					tree: tree
				});
				nodeCopy.children.push(childCopy);
			});
			return nodeCopy;
		};
		var nodeCopy=copy({
			node: this,
			parentNode: null,
			tree: node.tree
		});
		return nodeCopy.inject(node, where, Mif.Tree.Draw.node(nodeCopy));
	},
	
	remove: function(){
		this.tree.fireEvent('remove', [this]);
		var parent=this.parentNode, previous=this.getPrevious();
		if(parent) parent.children.erase(this);
		this.tree.selected=false;
		this.getDOM('node').destroy();
		Mif.Tree.Draw.update(parent);
		Mif.Tree.Draw.update(previous);
		this.tree.mouse.node=false;
		this.tree.updateHover();
		this.tree.$getIndex();
	}
	
});


Mif.Tree.implement({

	move: function(from, to, where){
		if ( from.inject(to, where) ){
			this.fireEvent('move', [from, to, where]);
		}
		return this;
	},
	
	copy: function(from, to, where){
		var copy = from.copy(to, where);
		if ( copy ){
			this.fireEvent('copy', [from, to, where, copy]);
		}
		return this;
	},
	
	remove: function(node){
		node.remove();
		return this;
	},
	
	add: function(node, current, where){
		if($type(node)!='mif:tree:node'){
			node=new Mif.Tree.Node({
				parentNode: null,
				tree: this
			}, node);
		};
		node.inject(current, where, Mif.Tree.Draw.node(node));
		this.fireEvent('add', [node, current, where]);
		return this;
	}
	
});

Array.implement({
	
	inject: function(added, current, where){//inject added after or before current;
		var pos=this.indexOf(current)+(where=='before' ? 0 : 1);
		for(var i=this.length-1;i>=pos;i--){
			this[i+1]=this[i];
		}
		this[pos]=added;
		return this;
	}
	
});

/*
Mif.Tree.Drag
*/
Mif.Tree.Drag = new Class({
	
	Implements: [new Events, new Options],
	
	Extends: Drag,
	
	options:{
		group: 'tree',
		droppables: [],
		snap: 4,
		animate: true,
		open: 600,//time to open node
		scrollDelay: 100,
		scrollSpeed: 100,
		modifier: 'control',//copy
		startPlace: ['icon', 'name']
	},

	initialize: function(tree, options){
		tree.drag=this;
		this.setOptions(options);
		$extend(this,{
			tree: tree,
			snap: this.options.snap,
			groups: [],
			droppables: [],
			action: this.options.action
		});
		
		this.addToGroups(this.options.group);
		
		this.setDroppables(this.options.droppables);
		
		$extend(tree.defaults, {
			dropDenied: [],
			dragDisabled: false
		});
		tree.addEvent('drawRoot',function(){
			tree.root.dropDenied.combine(['before', 'after']);
		});
		
		this.pointer=new Element('div').addClass('mif-tree-pointer').injectInside(tree.wrapper);
		
		this.current=Mif.Tree.Drag.current;
		this.target=Mif.Tree.Drag.target;
		this.where=Mif.Tree.Drag.where;

		this.element=[this.current, this.target, this.where];
		this.document = tree.wrapper.getDocument();
		
		this.selection = (Browser.Engine.trident) ? 'selectstart' : 'mousedown';
		
		this.bound = {
			start: this.start.bind(this),
			check: this.check.bind(this),
			drag: this.drag.bind(this),
			stop: this.stop.bind(this),
			cancel: this.cancel.bind(this),
			eventStop: $lambda(false),
			leave: this.leave.bind(this),
			enter: this.enter.bind(this),
			keydown: this.keydown.bind(this)
		};
		this.attach();
		
		this.addEvent('start', function(){
			Mif.Tree.Drag.dropZone=this;
			this.tree.unselect();
			document.addEvent('keydown', this.bound.keydown);
			this.setDroppables();
			this.droppables.each(function(item){
				item.getElement().addEvents({mouseleave: this.bound.leave, mouseenter: this.bound.enter});
			}, this);
			Mif.Tree.Drag.current.getDOM('name').addClass('mif-tree-drag-current');
			this.addGhost();
		}, true);
		this.addEvent('complete', function(){
			document.removeEvent('keydown', this.bound.keydown);
			this.droppables.each(function(item){
				item.getElement().removeEvent('mouseleave', this.bound.leave).removeEvent('mouseenter', this.bound.enter);
			}, this);
			Mif.Tree.Drag.current.getDOM('name').removeClass('mif-tree-drag-current');
			var dropZone=Mif.Tree.Drag.dropZone;
			if(!dropZone || dropZone.where=='notAllowed'){
				Mif.Tree.Drag.startZone.onstop();
				Mif.Tree.Drag.startZone.emptydrop();
				return;
			}
			if(dropZone.onstop) dropZone.onstop();
			dropZone.beforeDrop();
		});
	},
	
	getElement: function(){
		return this.tree.wrapper;
	},
	
	addToGroups: function(groups){
		groups=$splat(groups);
		this.groups.combine(groups);
		groups.each(function(group){
			Mif.Tree.Drag.groups[group]=(Mif.Tree.Drag.groups[group]||[]).include(this);
		}, this);
	},
	
	setDroppables: function(droppables){
		this.droppables.combine($splat(droppables));
		this.groups.each(function(group){
			this.droppables.combine(Mif.Tree.Drag.groups[group]);
		}, this);
	},

	attach: function(){
		this.tree.wrapper.addEvent('mousedown', this.bound.start);
		return this;
	},

	detach: function(){
		this.tree.wrapper.removeEvent('mousedown', this.bound.start);
		return this;
	},
	
	dragTargetSelect: function(){
		function addDragTarget(){
			this.current.getDOM('name').addClass('mif-tree-drag-current');
		}
		function removeDragTarget(){
			this.current.getDOM('name').removeClass('mif-tree-drag-current');
		}
		this.addEvent('start',addDragTarget.bind(this));
		this.addEvent('beforeComplete',removeDragTarget.bind(this));
	},
	
	leave: function(event){
	
		var dropZone=Mif.Tree.Drag.dropZone;
		if(dropZone){
			dropZone.where='notAllowed';
			Mif.Tree.Drag.ghost.firstChild.className='mif-tree-ghost-icon mif-tree-ghost-'+dropZone.where;
			if(dropZone.onleave) dropZone.onleave();
			Mif.Tree.Drag.dropZone=false;
		}
		
		var relatedZone=this.getZone(event.relatedTarget);
		if(relatedZone) this.enter(null, relatedZone);
	},
	
	onleave: function(){
		this.tree.unselect();
		this.clean();
		$clear(this.scrolling);
		this.scrolling=null;
		this.target=false;
	},
	
	enter: function(event, zone){
		if(event) zone=this.getZone(event.target);
		var dropZone=Mif.Tree.Drag.dropZone;
		if(dropZone && dropZone.onleave) dropZone.onleave();
		Mif.Tree.Drag.dropZone=zone;
		zone.current=Mif.Tree.Drag.current;
		if(zone.onenter) zone.onenter();
	},
	
	onenter: function(){
		this.onleave()
	},
	
	getZone: function(target){//private leave/enter
		if(!target) return false;
		var parent=$(target);
		do{
			for(var l=this.droppables.length;l--;){
				var zone=this.droppables[l];
				if( parent==zone.getElement() ) {
					return zone;
				}
			}
			parent=parent.getParent();
		}while(parent);
		return false;
	},
	
	keydown: function(event){
		if(event.key=='esc') {
			var zone=Mif.Tree.Drag.dropZone;
			if(zone) zone.where='notAllowed';
			this.stop(event);
		}
	},
	
	autoScroll: function(){
		var y=this.y;
		if(y==-1) return;
		var wrapper=this.tree.wrapper;
		var top=y-wrapper.scrollTop;
		var bottom=wrapper.offsetHeight-top;
		var sign=0;
		if(top<this.tree.height){
			var delta=top;
			sign=1;
		}else if(bottom<this.tree.height){
			var delta=bottom;
			sign=-1;
		}
		if(sign && !this.scrolling){
			this.scrolling=function(node){
				if(y!=this.y){
					y=this.y;
					delta = (sign==1 ? (y-wrapper.scrollTop) : (wrapper.offsetHeight-y+wrapper.scrollTop))||1;
				}
				wrapper.scrollTop=wrapper.scrollTop-sign*this.options.scrollSpeed/delta;
			}.periodical(this.options.scrollDelay, this, [sign])
		}
		if(!sign){
			$clear(this.scrolling);
			this.scrolling=null;
		}
	},
	
	start: function(event){//mousedown
		if (this.options.preventDefault) event.preventDefault();
		this.fireEvent('beforeStart', this.element);
		//
		
		var target=this.tree.mouse.target;
		if(!target) return;
		this.current=$splat(this.options.startPlace).contains(target) ? this.tree.mouse.node : false;
		if(!this.current || this.current.dragDisabled) {
			return;
		}
		Mif.Tree.Drag.current=this.current;
		Mif.Tree.Drag.startZone=this;
		
		this.mouse={start:event.page};
		this.document.addEvents({mousemove: this.bound.check, mouseup: this.bound.cancel});
		this.document.addEvent(this.selection, this.bound.eventStop);
	},
	
	drag: function(event){
		Mif.Tree.Drag.ghost.position({x:event.page.x+20,y:event.page.y+20});
		var dropZone=Mif.Tree.Drag.dropZone;
		if(!dropZone||!dropZone.ondrag) return;
		Mif.Tree.Drag.dropZone.ondrag(event);
	},

	ondrag: function(event){
		this.autoScroll();
		
		if(!this.checkTarget()) return;
		
		this.clean();
		var where=this.where;
		var target=this.target;
		var ghostType=where;
		if(where=='after'&&(target.getNext())||where=='before'&&(target.getPrevious())){
			ghostType='between';
		}
		Mif.Tree.Drag.ghost.firstChild.className='mif-tree-ghost-icon mif-tree-ghost-'+ghostType;
		if(where == 'notAllowed'){
			this.tree.unselect();
			return;
		}
		this.tree.select(target);
		if(where == 'inside'){
			if(!target.isOpen() && !this.openTimer && (target.loadable||target.hasChildren()) ){
				this.wrapper=target.getDOM('wrapper').setStyle('cursor', 'progress');
				this.openTimer=function(){
					target.toggle();
					this.clean();
				}.delay(this.options.open,this);
			}
		}else{
			var wrapper=this.tree.wrapper;
			var top=this.index*this.tree.height;
			if(where=='after') top+=this.tree.height;
			this.pointer.setStyles({
				left: wrapper.scrollLeft,
				top: top,
				width: wrapper.clientWidth
			});
		}
	},

	clean: function(){
		this.pointer.style.width=0;
		if(this.openTimer){
			$clear(this.openTimer);
			this.openTimer=false;
			this.wrapper.style.cursor='inherit';
			this.wrapper=false;
		}
	},
	
	addGhost: function(){
		var wrapper=this.current.getDOM('wrapper');
		var ghost=new Element('span').addClass('mif-tree-ghost');
		ghost.adopt(Mif.Tree.Draw.node(this.current).getFirst())
		.injectInside(document.body).addClass('mif-tree-ghost-notAllowed').setStyle('position', 'absolute');
		new Element('span').set('html',Mif.Tree.Draw.zeroSpace).injectTop(ghost);
		ghost.getLast().getFirst().className='';
		Mif.Tree.Drag.ghost=ghost;
	},
	
	checkTarget: function(){
		this.y=this.tree.mouse.coords.y;
		var target=this.tree.mouse.node;
		this.target=target;
		if(!target){
			this.target=false;
			this.where='notAllowed';
			this.fireEvent('drag');
			return true;
		};
		if(this.current.contains(target)){
			this.where='notAllowed';
			this.fireEvent('drag');
			return true;
		};
		this.index=Math.floor(this.y/this.tree.height);
		var delta=this.y-this.index*this.tree.height;
		var deny=this.target.dropDenied;
		if(this.tree.sortable){
			deny.include('before').include('after');
		};
		var where;
		if(!deny.contains('inside') && delta>(this.tree.height/4) && delta<(3/4*this.tree.height)){
			where='inside';
		}else{
			if(delta<this.tree.height/2){
				if(deny.contains('before')){
					if(deny.contains('inside')){
						where=deny.contains('after') ? 'notAllowed' : 'after';
					}else{
						where='inside';
					}
				}else{
					where='before';
				}
			}else{
				if(deny.contains('after')){
					if(deny.contains('inside')){
						where=deny.contains('before') ? 'notAllowed' : 'before';
					}else{
						where='inside';
					}
				}else{
					where='after';
				}
			}
		};
		if(this.where==where && this.target==target) return false;
		this.where=where; this.target=target;
		this.fireEvent('drag');
		return true;
	},
	
	emptydrop: function(){
		var current=this.current, target=this.target, where=this.where;
		var scroll=this.tree.scroll;
		var complete=function(){
			scroll.removeEvent('complete', complete);
			if(this.options.animate){
				var wrapper=current.getDOM('wrapper');
				var position=wrapper.getPosition();
				Mif.Tree.Drag.ghost.set('morph',{
					duration: 'short',
					onComplete: function(){
						Mif.Tree.Drag.ghost.dispose();
						this.fireEvent('emptydrop', this.element);
					}.bind(this)
				});
				Mif.Tree.Drag.ghost.morph({left: position.x, top: position.y});
				return;
			};
			Mif.Tree.Drag.ghost.dispose();
			this.fireEvent('emptydrop', this.element);
			return;
		}.bind(this);
		scroll.addEvent('complete', complete);
		this.tree.select(this.current);
		this.tree.scrollTo(this.current);
	},
	
	beforeDrop: function(){
		if(this.options.beforeDrop){
			this.options.beforeDrop.apply(this, [this.current, this.trarget, this.where]);
		}else{
			this.drop();
		}
	},
	
	drop: function(){
		var current=this.current, target=this.target, where=this.where;
		Mif.Tree.Drag.ghost.dispose();
		var action=this.action || (this.tree.key[this.options.modifier] ? 'copy' : 'move');
		if(this.where=='inside' && !target.isOpen()){
			target.toggle();
			if(target.$loading){
				var self=this;
				var onLoad=function(){
					self.tree[action](current, target, where);
					self.fireEvent('drop', [current, target, where]);
					target.removeEvent('load',onLoad);
				};
				target.addEvent('load',onLoad);
				return;
			};
		};
		this.tree[action](current, target, where);
		this.fireEvent('drop', [current, target, where]);
	},
	
	onstop: function(){
		this.clean();
		$clear(this.scrolling);
	}
});

Mif.Tree.Drag.groups={};


/*
Mif.Tree.Drag.Element.js
*/
Mif.Tree.Drag.Element=new Class({

	Implements: [Options, Events],

	initialize: function(element, options){
		
		this.element=$(element);
		
		this.setOptions(options);
		
	},
	
	getElement: function(){
		return this.element;
	},
	
	onleave: function(){
		this.where='notAllowed';
		Mif.Tree.Drag.ghost.firstChild.className='mif-tree-ghost-icon mif-tree-ghost-'+this.where;
	},
	
	onenter: function(){
		this.where='inside';
		Mif.Tree.Drag.ghost.firstChild.className='mif-tree-ghost-icon mif-tree-ghost-'+this.where;
	},
	
	beforeDrop: function(){
		if(this.options.beforeDrop){
			this.options.beforeDrop.apply(this, [this.current, this.trarget, this.where]);
		}else{
			this.drop();
		}
	},
	
	drop: function(){
		Mif.Tree.Drag.ghost.dispose();
		this.fireEvent('drop', Mif.Tree.Drag.current);
	}
	

});


/*
Mif.Tree.Rename
*/

Mif.Tree.implement({
	
	attachRenameEvents: function(){
		this.wrapper.addEvents({
			click: function(event){
				if($(event.target).get('tag')=='input') return;
				this.beforeRenameComplete();
			}.bind(this),
			keydown: function(event){
				if(event.key=='enter'){
					this.beforeRenameComplete();
				}
				if(event.key=='esc'){
					this.renameCancel();
				}
			}.bind(this)
		});
	},
	
	disableEvents: function(){
		if(!this.eventStorage) this.eventStorage=new Element('div');
		this.eventStorage.cloneEvents(this.wrapper);
		this.wrapper.removeEvents();
	},
	
	enableEvents: function(){
		this.wrapper.removeEvents();
		this.wrapper.cloneEvents(this.eventStorage);
	},
	
	getInput: function(){
		if(!this.input){
			this.input=new Element('input').addClass('mif-tree-rename');
			this.input.addEvent('focus',function(){this.select()});
			Mif.Tree.Rename.autoExpand(this.input);
		}
		return this.input;
	},
	
	startRename: function(node){
		this.unselect();
		this.disableEvents();
		this.attachRenameEvents();
		var input=this.getInput();
		input.value=node.name;
		this.renameName=node.getDOM('name');
		this.renameNode=node;
		input.setStyle('width', this.renameName.offsetWidth+15);
		input.replaces(this.renameName);
		input.focus();
	},
	
	finishRename: function(){
		this.renameName.replaces(this.getInput());
	},
	
	beforeRenameComplete: function(){
		if(this.options.beforeRename){
			var newName=this.getInput().value;
			var node=this.renameNode;
			this.options.beforeRename.apply(this, [node, node.name, newName]);
		}else{
			this.renameComplete();
		}
	},
		
	renameComplete: function(){
		this.enableEvents();
		this.finishRename();
		var node=this.renameNode;
		var oldName=node.name;
		node.set({
			property:{
				name: this.getInput().value
			}
		});
		this.fireEvent('rename', [node, node.name, oldName]);
		this.select(node);
	},
	
	renameCancel: function(){
		this.enableEvents();
		this.finishRename();
		this.select(this.renameNode);
	}
	
});

Mif.Tree.Node.implement({
	
	rename: function(){
		this.tree.startRename(this);
	}
	
});

Mif.Tree.Rename={
	
	autoExpand: function(input){
		var span=new Element('span').addClass('mif-tree-rename').setStyles({
			position: 'absolute',
			left: -2000,
			top:0,
			padding: 0
		}).injectInside(document.body);
		input.addEvent('keydown',function(event){
			(function(){
			input.setStyle('width',Math.max(20, span.set('html', input.value.replace(/\s/g,'&nbsp;')).offsetWidth+15));
			}).delay(10);
		});
	}
	
};


/*
Mif.Tree.Row
*/
Mif.Tree.implement({

	initRows: function(){
		this.addRowWrapper();
		this.addEvent('drawRoot',function(){
			new Element('div',{'id':'mif-tree-row-'+this.root.UID, "class": 'mif-tree-row'}).injectInside(this.rowWrapper);
			new Element('div').addClass('mif-tree-row-container').injectInside(this.rowWrapper);
		}.bind(this));
		this.addEvent('drawChildren',function(node){
			Mif.Tree.Draw.rowChildren(node);
		});
		this.addEvent('toggle',function(node, state){
			node.getRowDOM('container').style.display=state ? 'block' : 'none';
		});
		this.addEvent('selectChange',function(node, state){
			node.getRowDOM('node')[(state ? 'add' : 'remove') + 'Class']('mif-tree-row-selected');
		});
		this.addEvent('hover', function(node, target, state){
			if(target!='node'||!node) return;
			var domNode=node.getRowDOM('node');
			var action=(state=='over' ? 'add' : 'remove') +'Class';
			domNode[action]('mif-tree-row-hover');
			if(node.state.selected) domNode[action]('mif-tree-row-hover-selected');
		}.bind(this));
		this.addEvent('structureChange', function(from, to, where, type){
			if(type=='copy'){
				var dom=Mif.Tree.Draw.row(from);
				var fromNode=dom.getFirst(), fromContainer = dom.getLast();
			}else{
				var fromNode=from.getRowDOM('node'), fromContainer=from.getRowDOM('container');
			}
			this.injectRowDOM(fromNode, fromContainer, to, where);
		});
		this.addEvent('remove', function(node){
			node.getRowDOM('node').destroy();
		});
		this.addEvent('updateNode',function(node){
			node.getRowDOM('container').style.display=node.isOpen() ? 'block' : 'none';
		});
	},
	
	injectRowDOM: function(fromNode, fromContainer, to, where){
		var toNode=to.getRowDOM('node'), toContainer=to.getRowDOM('container');
		switch(where){
			case 'inside':
				fromNode.injectInside(toContainer);
				fromContainer.injectInside(toContainer);
				break;
			case 'before':
				fromNode.injectBefore(toNode);
				fromContainer.injectBefore(toNode);
				break;
			case 'after':
				fromNode.injectAfter(toContainer);
				fromContainer.injectAfter(fromNode);
				break;
		}
		this.updateHover();
	},
	
	addRowWrapper: function(){
		var wrapper=this.wrapper;
		var rowWrapper=new Element('div').injectTop(this.container).addClass('mif-tree-row-wrapper');
		this.rowWrapper=rowWrapper;
		wrapper.addEvent('scroll', function(event){//sync scroll
			rowWrapper.scrollTop=wrapper.scrollTop;
		});
		if(Browser.Engine.presto){
			wrapper.addEvent('mousewheel',function(){
				(function(){rowWrapper.scrollTop=wrapper.scrollTop;}).delay(50);
			});
		}
	}

});

Mif.Tree.Draw.rowChildren=function(node){
	if(node.tree.forest && !node.getParent()){
		var container=node.tree.rowWrapper;
	}else{
		var container=node.getRowDOM('container');
	}
	var html=[];
	var children=node.children;
	for( var i=children.length; i--; i>=0 ){
		var child=children[i];
		html.unshift('<div id="mif-tree-row-',child.UID,'" class="mif-tree-row"></div><div class="mif-tree-row-container"></div>');
	}
	container.set('html',html);
};

Mif.Tree.Draw.row=function(node){
	return new Element('div').set('html', '<div id="mif-tree-row-',node.UID,'" class="mif-tree-row"></div><div class="mif-tree-row-container"></div>');
};



Mif.Tree.Node.implement({

	getRowDOM: function(what){
		var node=$('mif-tree-row-'+this.UID);
		if(what=='node') return node;
		if(what=='container') return node.getNext();
	}

});


/*
Mif.Tree.Checkbox
*/
Mif.Tree.implement({

	initCheckbox: function(type){
		this.checkboxType=type||'simple';
		this.dfltState.checked='unchecked';
		this.defaults.hasCheckbox=true;
		this.wrapper.addEvent('click',this.checkboxClick.bindWithEvent(this));
		if(this.checkboxType=='simple') return;
		this.addEvent('loadChildren', function(node){
			if(!node || node.state.checked=='unchecked') return;
			node.recursive(function(){
				this.state.checked='checked';
			});
		});
	},
	
	checkboxClick: function(event){
		if(this.mouse.target!='checkbox') {return;}
		this.mouse.node['switch']();
	},
	
	getChecked: function(){
		var checked=[];
		this.root.recursive(function(){
			if(this.hasCheckbox && this.state.checked) checked.push(checked);
		});
		return checked;
	}

});

Mif.Tree.Node.implement({

	'switch' : function(state){
		if(this.state.checked==state||!this.hasCheckbox) return;
		var type=this.tree.checkboxType;
		var checked=(this.state.checked=='checked') ? 'unchecked' : 'checked';
		this.tree.fireEvent(checked=='checked' ? 'check' : 'unCheck', this);
		var setState=function(node, state){
			if(!node.hasCheckbox) return;
			var oldState=node.state.checked;
			node.state.checked=state;
			if(!node.parentNode || (node.parentNode && node.parentNode.$draw)){
				node.getDOM('checkbox').removeClass('mif-tree-node-'+oldState).addClass('mif-tree-node-'+state);
			}
		};
		if(type=='simple'){
			setState(this, checked);
			return false;
		};
		this.recursive(function(){
			setState(this, checked);
		});
		function setParentCheckbox(node){
			if(!node.hasCheckbox) return;
			if(!node.parentNode || (node.tree.forest && !node.parentNode.parentNode)) return;
			var parent=node.parentNode;
			var state='';
			var children=parent.children;
			for(var i=children.length; i--; i>0){
				var child=children[i];
				if(!child.hasCheckbox) continue;
				var childState=child.state.checked;
				if(childState=='partially'){
					state='partially';
					break;
				}else if(childState=='checked'){
					if(state=='unchecked'){
						state='partially';
						break;
					}
					state='checked';
				}else{
					if(state=='checked'){
						state='partially';
						break;
					}else{
						state='unchecked';
					}
				}
			}
			if(parent.state.checked==state){return;};
			setState(parent, state);
			setParentCheckbox(parent);
		};
		setParentCheckbox(this);
	}

});


