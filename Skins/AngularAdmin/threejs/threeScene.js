/**
 * Created by mogwaili on 03/03/17.
 */

var container; //Div qui contient le scene
var camera, renderer, controls; //Elemes de rendu thresjs
var manager;
var textures;
var ambient, directionalLight1, directionalLight2; //Lightings


//Initialise la scene
function initScene(mainDiv, camPosition, camFocus){
    campPosition = camPosition || {x:-10,y:20,z:30};
    camFocus = camFocus || {x:0,y:0,z:20};

    container = document.createElement( 'div' );
    document.getElementById(mainDiv).appendChild( container );

    camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 2000 );
    camera.position.x = campPosition.x;
    camera.position.y = campPosition.y;
    camera.position.z = campPosition.z;
    camera.lookAt(new THREE.Vector3(camFocus.x,camFocus.y,camFocus.z));
    //camera.rotation.x = -Math.PI/2;



    scene = new THREE.Scene();

    ambient = new THREE.AmbientLight( 0x101030 );
    scene.add( ambient );

    directionalLight1 = new THREE.DirectionalLight( 0xffeedd);
    directionalLight1.position.set( 0, 1, 1 );
    directionalLight1.castShadow =true;
    directionalLight1.shadow.mapSize.width = 512;  // default
    directionalLight1.shadow.mapSize.height = 512; // default
    directionalLight1.shadow.camera.near = 0.5;       // default
    directionalLight1.shadow.camera.far = 500      // default
    scene.add( directionalLight1 );

    directionalLight2 = new THREE.DirectionalLight( 0x88aaff,0.4 );
    directionalLight2.position.set( -1, 1, -1 );
    directionalLight2.castShadow =true;
    directionalLight2.shadow.mapSize.width = 512;  // default
    directionalLight2.shadow.mapSize.height = 512; // default
    directionalLight2.shadow.camera.near = 0.5;       // default
    directionalLight2.shadow.camera.far = 500      // default
    scene.add( directionalLight2);

    manager = new THREE.LoadingManager();
    manager.onProgress = function ( item, loaded, total ) {
        console.log( item, loaded, total );
    };

    renderer = new THREE.WebGLRenderer({ alpha: true });
    renderer.setPixelRatio( window.devicePixelRatio );
    renderer.setSize( window.innerWidth, window.innerHeight );
    renderer.setClearColor( 0xffffff, 0);
    renderer.shadowMap.Enabled = true;

    container.appendChild( renderer.domElement );

    //controls
    controls = new THREE.OrbitControls( camera, renderer.domElement );
    controls.target = new THREE.Vector3(0,0,20);
    //controls.minDistance = 50;
    controls.enablePan = false;
    //controls.addEventListener( 'change', render ); <= desactivé car on a deja un animation loop qui rerender

    //Recalcule la scene toutes les x msecondes
    animate();

    return scene;
}



function onWindowResize() {
    windowHalfX = window.innerWidth / 2;
    windowHalfY = window.innerHeight / 2;

    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();

    renderer.setSize( window.innerWidth, window.innerHeight );
}

function animate()  {
    requestAnimationFrame( animate );
    render();
}

function render() {
    //camera.position.x += ( mouseX - camera.position.x ) * .05;
    //camera.position.y += ( - mouseY - camera.position.y ) * .05;

    //camera.lookAt( scene.position );
    renderer.render( scene, camera );
}

//Construit le canvas depuis le json
/*

    {
     'links':[
            {
             'origin':'0',
             'dest':'1'
            }
     ],
     'node':  {
              '1':  {
                     'type':'firewall',
                     'info1':'bla',
                     'info2':'blabla'
                    }
            }
     }

 */
function buildFromJson(json){
    var net = JSON.parse(json);
    var origin = [0]
    var boxes = {'0': new THREE.Object3D()};

    parseNetwork(net,origin,boxes);

}

function parseNetwork(network,origin,boxes){
    if(origin.length == 0) return false;
    var links = network.links;

    var temp = [];
    for(var i = 0; i< origin.length; i++){
        links.forEach(function(elem){
            if(elem.origin == origin[i]){
                temp.push(elem.dest);
                boxes[elem.dest] = new THREE.Object3D();
                boxes[elem.origin].add(boxes[elem.dest])
            }
        });
    }

    parseNetwork(network,temp,boxes);
}