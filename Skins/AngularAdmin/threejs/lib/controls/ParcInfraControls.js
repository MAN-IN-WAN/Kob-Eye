/*
 * @author zz85 / https://github.com/zz85
 * @author mrdoob / http://mrdoob.com
 * Running this will allow you to drag three.js objects around the screen.
 */

THREE.ParcInfraControls = function ( _objects, _camera, _domElement ) {

	if ( _objects instanceof THREE.Camera ) {

		console.warn( 'THREE.ParcInfraControls: Constructor now expects ( objects, camera, domElement )' );
		var temp = _objects; _objects = _camera; _camera = temp;

	}

	var _plane = new THREE.Plane();
	var _raycaster = new THREE.Raycaster();

	var _mouse = new THREE.Vector2();
	var _offset = new THREE.Vector3();
	var _intersection = new THREE.Vector3();

	var _selected = null, _hovered = null;

	//

	var scope = this;

	function activate() {

		_domElement.addEventListener( 'mousemove', onDocumentMouseMove, false );
		_domElement.addEventListener( 'mousedown', onDocumentMouseDown, false );
		_domElement.addEventListener( 'mouseup', onDocumentMouseUp, false );

	}

	function deactivate() {

		_domElement.removeEventListener( 'mousemove', onDocumentMouseMove, false );
		_domElement.removeEventListener( 'mousedown', onDocumentMouseDown, false );
		_domElement.removeEventListener( 'mouseup', onDocumentMouseUp, false );

	}

	function dispose() {

		deactivate();

	}

	function onDocumentMouseMove( event ) {

		event.preventDefault();


		//Gestion des decalages dus au fait qu'on n'est suelement dans une partie de l'ecran !
		//https://stackoverflow.com/questions/1350581/how-to-get-an-elements-top-position-relative-to-the-browsers-window/1350681#1350681
        var viewportOffset = _domElement.getBoundingClientRect();
		// these are relative to the viewport, i.e. the window
        var offTop = viewportOffset.top;
        var offLeft = viewportOffset.left;


		_mouse.x = ( (event.clientX - offLeft) / _domElement.width ) * 2 - 1 ;
		_mouse.y = - ( (event.clientY - offTop) / _domElement.height ) * 2 + 1 ;
        // console.log(event.clientX,_mouse.x);
        // console.log(event.clientY,_mouse.y);


		_raycaster.setFromCamera( _mouse, _camera );

		var intersects = _raycaster.intersectObjects( _objects );

		if ( intersects.length > 0 ) {

			var object = intersects[ 0 ].object;

			_plane.setFromNormalAndCoplanarPoint( _camera.getWorldDirection( _plane.normal ), object.position );

			if ( _hovered !== object ) {

				scope.dispatchEvent( { type: 'hoveron', object: object, oEvent : event } );

				_domElement.style.cursor = 'pointer';
				_hovered = object;

			}

		} else {

			if ( _hovered !== null ) {

				scope.dispatchEvent( { type: 'hoveroff', object: _hovered } );

				_domElement.style.cursor = 'auto';
				_hovered = null;

			}

		}

	}

	function onDocumentMouseDown( event ) {

		event.preventDefault();

		_raycaster.setFromCamera( _mouse, _camera );

		var intersects = _raycaster.intersectObjects( _objects );

		if ( intersects.length > 0 ) {

			_selected = intersects[ 0 ].object;

			if ( _raycaster.ray.intersectPlane( _plane, _intersection ) ) {

				_offset.copy( _intersection ).sub( _selected.position );

			}

			//_domElement.style.cursor = 'move';

			scope.dispatchEvent( { type: 'selectedServ', object: _selected } );

		}


	}

	function onDocumentMouseUp( event ) {

		event.preventDefault();

		if ( _selected ) {

			scope.dispatchEvent( { type: 'dragend', object: _selected } );

			_selected = null;

		}

		_domElement.style.cursor = 'auto';

	}

	activate();

	// API

	this.enabled = true;

	this.activate = activate;
	this.deactivate = deactivate;
	this.dispose = dispose;


};

THREE.ParcInfraControls.prototype = Object.create( THREE.EventDispatcher.prototype );
THREE.ParcInfraControls.prototype.constructor = THREE.ParcInfraControls;
