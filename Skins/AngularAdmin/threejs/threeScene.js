/**
 * Created by mogwaili on 03/03/17.
 */

var scnList = new Array();
// var container; //Div qui contient la scene
// var scene;
// var camera, renderer, controls; //Elemes de rendu thresjs
// var manager;
// var ambient, directionalLight1, directionalLight2; //Lightings
// var items = new Array();


//Initialise la scene
function initScene(mainDiv, camPosition, camFocus){
    var sProperties = {
        containers: new Array(),
        cameras: new Array(),
        renderers: new Array(),
        controls: new Array(),
        managers: new Array(),
        lights: new Array(),
        items: new Array()
    };


    camPosition = camPosition || {x:-20,y:10,z:20};
    camFocus = camFocus || {x:0,y:0,z:10};

    //console.log(camPosition);

    var container = document.createElement( 'div' );
    var parentDiv = document.getElementById(mainDiv);
    parentDiv.appendChild( container );
    sProperties.containers.push(container);


    var camera = new THREE.PerspectiveCamera( 75, parentDiv.offsetWidth / parentDiv.offsetHeight, 1, 2000 );
    camera.position.x = camPosition.x;
    camera.position.y = camPosition.y;
    camera.position.z = camPosition.z;
    camera.lookAt(new THREE.Vector3(camFocus.x,camFocus.y,camFocus.z));
    //camera.rotation.x = -Math.PI/2;
    sProperties.cameras.push(camera);



    var scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2( 0xcccccc, 0.002 );

    var ambient = new THREE.AmbientLight( 0x101030 );
    scene.add( ambient );

    var directionalLight1 = new THREE.DirectionalLight( 0xffeedd);
    directionalLight1.position.set( 0, 1, 1 );
    directionalLight1.castShadow =true;
    directionalLight1.shadow.mapSize.width = 512;  // default
    directionalLight1.shadow.mapSize.height = 512; // default
    directionalLight1.shadow.camera.near = 0.5;       // default
    directionalLight1.shadow.camera.far = 500      // default
    scene.add( directionalLight1 );

    var directionalLight2 = new THREE.DirectionalLight( 0x88aaff,0.4 );
    directionalLight2.position.set( -1, 1, -1 );
    directionalLight2.castShadow =true;
    directionalLight2.shadow.mapSize.width = 512;  // default
    directionalLight2.shadow.mapSize.height = 512; // default
    directionalLight2.shadow.camera.near = 0.5;       // default
    directionalLight2.shadow.camera.far = 500      // default
    scene.add( directionalLight2);

    sProperties.lights.push(ambient,directionalLight1,directionalLight2);

    var manager = new THREE.LoadingManager();
    manager.onProgress = function ( item, loaded, total ) {
        console.log( item, loaded, total );
    };
    sProperties.managers.push(manager);

    var renderer = new THREE.WebGLRenderer({ alpha: true });
    renderer.setPixelRatio( window.devicePixelRatio );
    renderer.setSize( parentDiv.offsetWidth, parentDiv.offsetHeight );
    renderer.setClearColor( 0xffffff, 0);
    renderer.shadowMap.Enabled = true;

    sProperties.renderers.push(renderer);

    container.appendChild( renderer.domElement );

    scene.uProps = sProperties;

    //Recalcule la scene toutes les x msecondes
    animate();

    return scene;
}


//UNUSED ATM
function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();

    renderer.setSize( container.parentNode.offsetWidth, container.parentNode.offsetHeight );
}


function getCenterBoundingBox(object3D) {
    var avVect = null;
    var occ = 0;
    function getBoundingBox (obj,parent) {
        if (obj.type === "Object3D") {
            for (var i in obj.children){
                getBoundingBox(obj.children[i],obj);
            }
            return;
        }
        // console.log(obj);
        // console.log(parent);
        obj.geometry.computeBoundingBox();
        occ ++;
        if (avVect === null) {
            avVect = parent.localToWorld(obj.geometry.boundingBox.getCenter())
        } else {
            avVect.add(parent.localToWorld(obj.geometry.boundingBox.getCenter()));
        }
    };
    getBoundingBox(object3D);
    avVect.x /= occ;
    avVect.y /= occ;
    avVect.z /= occ;
    //console.log(avVect,occ);
    return avVect;
}
function getCenterPoint(obj) {
    return getCenterBoundingBox(obj);
}


/*
    Cale la mire de la camera sur le centre de l'objet obj

 */
function centerCamera(obj,scn,cam) {
    cam = cam || null;
    scn = scn || null;

    if(!scn && !cam)
        throw 'centerCamera nécéssite aumoins une scène ou une camera !';

    //console.log(obj);
    //dans un premier temps on positionne la cible de la camera sur le centre du réseau
    var objCenter = getCenterPoint(obj.threeObj);

    if(cam){
        cam.position.x = objCenter.x;
        cam.position.y = objCenter.y;
        cam.position.z = objCenter.z;
        cam.orbitPoint = objCenter;
    } else{
        var cams = scn.uProps.cameras;
        for ( var n in cams ){
            cam[n].position.x = objCenter.x;
            cam[n].position.y = objCenter.y;
            cam[n].position.z = objCenter.z;
            cam[n].orbitPoint = objCenter;
        }
    }

    //console.log('center',objCenter,obj.threeObj.worldToLocal(new THREE.Vector3(0,0,0)));

}

function initOrbitalCam(scn, cam, rend, opts){
    var values = {
        noPan: true,
        noZoom: true,
        noRotate: true,
        maxPolarAngle: Math.PI/2,   // prevent the camera from going under the ground
        minDistance: 25,            // the minimum distance the camera must have from center
        maxDistance: 150,           // the maximum distance the camera must have from center
        zoomSpeed: 0.3,             // control the zoomIn and zoomOut speed
        rotateSpeed: 0.3            // control the rotate speed
    };


    //controls
    var controls = new THREE.OrbitControls( cam, rend.domElement ,rend.domElement);
    controls.target = cam.orbitPoint || new THREE.Vector3(0,0,0);
    Object.assign(controls,values); //assigne nos valeurs par defaut
    if(opts != undefined){
        Object.assign(controls,opts);
    }

    //controls.autoRotate = true;
    //controls.addEventListener( 'change', render );

    controls.rotateLeft(Math.degToRad(25));
    controls.rotateUp(Math.degToRad(-65));

    scn.uProps.controls.push(controls);

    setTimeout(function(){
        animateCam( cam );
    },1000);


    return false;
}


function animateCam( cam ){
    var camPos = cam.position;
    //console.log('animateCAm',camPos);
    var camTween = new TWEEN.Tween(cam.position).to({x:-camPos.x,y:camPos.y,z:camPos.z},10000);
    var camTweenBis = new TWEEN.Tween(cam.position).to({x:camPos.x,y:camPos.y,z:camPos.z},10000);
    camTween.chain(camTweenBis);
    camTweenBis.chain(camTween);
    camTween.start();

    return false;
}



function animate()  {
    requestAnimationFrame( animate );
    TWEEN.update();
    render(scnList);
}

function render(scnList) {
    for( var i in scnList) {

        var scn = scnList[i];
        if (scn.skip) continue;

        for (var n in scn.uProps.controls) {
            if (scn.uProps.controls[n].hasOwnProperty('update'))
                scn.uProps.controls[n].update();
        }

        var renderer = scn.uProps.renderers[0];
        for (var n in scn.uProps.cameras) {
            renderer.render(scn, scn.uProps.cameras[n]);
        }
    }
}

//Construit le canvas depuis le json
/*

 var config = {
     devices: [
         {
             name: "firewall",
             type: "firewall",
             networks: [
                 {
                 name: "192.168.1.0/24",
                 gateway: "192.168.0.254",
                 devices: [
                     {
                         name: "test pc 1",
                         type: 'workstation',
                         ip: "192.168.1.12"
                     },
                     {
                         name: "test pc 2",
                         type: 'workstation',
                         ip: "192.168.1.13"
                     },
                     {
                         name: "test serveur",
                         type: 'server',
                         ip: "192.168.1.1"
                     }
                     ]
                 }
             ]
         }
     ]
 };

 */
function buildFromConfig(config,internet,scene){
    //console.log('config',config);
    //routeur
    for (var f in config.devices){
        //console.log('devices',config.devices[f]);
        var dev = new MapItem(config.devices[f],scene);
        internet.add(dev);
        //switch
        for (var n in config.devices[f].networks){
            var sw = new MapItem({type:'switch'},scene);
            dev.add(sw);
            buildFromConfig(config.devices[f].networks[n],sw);
        }
    }
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



function searchItemById(id, scn){
    var items = scn.uProps.items;
    for (var n in items){
        if(items[n].id == id)
            return items[n];
    }

    return false;
}


function getItemFromMesh (uuid, scn){
    var items = scn.uProps.items;
    for(var n in items){
        for (var m in items[n].uuids){
            if(items[n].uuids[m] == uuid)
                return items[n];
        }
    }

    return false;
}

function enableClick(scn, cam ,rend){
    var items = scn.uProps.items;

    var meshes = new Array();
    for (var n in items){
        //console.log('n',items[n]);
        for(var m in items[n].threeObj.children){
            //console.log('m',items[n].threeObj.children[m]);
            if(items[n].threeObj.children[m] instanceof THREE.Mesh)
                meshes.push(items[n].threeObj.children[m]);
        }
    }
    //console.log(meshes);

    var piControls = new THREE.ParcInfraControls( meshes, cam, rend.domElement );
    scn.uProps.controls.push(piControls);
    piControls.activate();
    piControls.activate();
    piControls.addEventListener('hoveron', function(event){
        var hoveredItem = getItemFromMesh(event.object.uuid,scn);
        //console.log(hoveredItem,event);
        var hoverTitle = document.getElementById('hoverTitle');
        if(!hoverTitle) {
            hoverTitle = document.createElement( 'div' );
            hoverTitle.id = 'hoverTitle';
            document.body.appendChild(hoverTitle);
        }
        hoverTitle.innerHTML = (hoveredItem.label || 'Inconnu') ;
        if(hoveredItem.id)
            hoverTitle.innerHTML += " ("+hoveredItem.id+") ";

        hoverTitle.setAttribute("style",
            'position: absolute;' +
            'top:'+(event.oEvent.y+15)+'px;' +
            'left:'+(event.oEvent.x+15)+'px;' +
            'z-index: 3000;' +
            'background-color: #fff;' +
            'border: 1px solid #000;' +
            'padding: 5px;');
    });
    piControls.addEventListener('hoveroff', function(event){
        var hoverTitle = document.getElementById('hoverTitle');
        hoverTitle.outerHTML = "";
        hoverTitle.delete;
    });
    piControls.addEventListener('selectedServ', function(event){
        var clickedItem = getItemFromMesh(event.object.uuid,scn);
        var nextstate = !clickedItem.selected;
        for(var n in items){
            items[n].selected = false;
            items[n].spotlight(items[n].selected);
        }
        clickedItem.selected = nextstate;
        clickedItem.spotlight(clickedItem.selected);

        console.log(clickedItem);
        //centerCamera(clickedItem);
    });

}

// var _interval = setInterval(function(scn){
//
//
//     if(renderer != undefined){
//
//
//
//
//
//         clearInterval(_interval);
//     }
// },500);


