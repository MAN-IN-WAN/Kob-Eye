/**
 * Created by mogwaili on 16/02/17.
 */

Math.degToRad = function (degrees) {
    return degrees * Math.PI / 180;
};


// Creation d'un objet permettant de stocker les infos sur les objets placé à l'ecran ainsi que leurs états...
//TODO : Verif typeObj
//TODO : Verif Coords
function MapItem (obj,scn){

    this.scn = scn || null;
    this.container = null;
    this.coords = {dimensions:{x:1,y:1,z:1},positions:{x:0,y:0,z:0}};
    this.typeObj = obj.type;
    this.colors = obj.colors || null;
    this.subObjs = obj.devices || null;
    this.threeObj = undefined;
    this.children = [];
    this.rotateAxis = obj.rotateAxis || 'y' ;
    this.ip = obj.ip || null;
    this.status = obj.status || null;
    this.id =  obj.id || null;
    //Pour le moment ne contient que les uuids des meshes
    this.uuids = new Array();
    this.selected = false;
    this.tweens = new Array();
    this.label = obj.Nom || obj.name || null;


    var dimensions = this.coords.dimensions || {x:1,y:1,z:1};
    var positions = this.coords.positions || this.coords;

    //Ajout d'un sous objet
    this.add = function (mi){
        var container = new THREE.Object3D();
        mi.container = container;
        this.threeObj.add(container);
        container.add(mi.threeObj);
        this.children.push(mi);
    };

    this.setColors = function(){
        var colors=  new Array()
        switch (this.typeObj) {
            case 'cube':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            case 'cloud':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            case 'firwall':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            case 'link':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            case 'hub':
            case 'switch':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            case 'pc':
            case 'poste':
            case 'workstation':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            case 'serv':
            case 'server':
            case 'serveur':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;
                colors[1] = (this.colors && this.colors[1]) ? this.colors[1] : 0xff0000;
                colors[2] = (this.colors && this.colors[2]) ? this.colors[2] : 0x00ff00;

                break;

            case 'cluster':
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;

                break;

            default:
                colors[0] = this.colors ? this.colors[0] : 0xcccccc;
        }

        switch ( this.status ){
            case '0':
                colors[0] = 0xff4444;
                break;

            case '1':
                colors[0] = 0xbbbbbb;
                break;

            case '2':
                colors[0] = 0x44ff44;
                break;

            default:
        }
        return colors;
    };

    var colors = this.setColors();

    switch (this.typeObj){
        case 'cube':
            var geometry = new THREE.BoxGeometry(dimensions.x, dimensions.y, dimensions.z);
            var material = new THREE.MeshBasicMaterial({color: 0xff5555});

            var cube = new THREE.Mesh(geometry, material);

            this.threeObj = cube;

        break;

        case 'cloud':
            // // texture
            // var texture = new THREE.Texture();
            // var loader = new THREE.ImageLoader( manager );
            // loader.load( '/Skins/AngularAdmin/threejs/textures/UV_Grid_Sm.jpg', function ( image ) {
            //     texture.image = image;
            //     texture.needsUpdate = true;
            //
            // } );
            //
            // // model loader
            // var onProgress = function ( xhr ) {
            //     if ( xhr.lengthComputable ) {
            //         var percentComplete = xhr.loaded / xhr.total * 100;
            //         console.log( Math.round(percentComplete, 2) + '% downloaded' );
            //     }
            // };
            //
            // var onError = function ( xhr ) {
            // };
            //
            //
            // var objContainer = new THREE.Object3D();
            // var loader = new THREE.OBJLoader( manager );
            // var obj;
            // loader.load( '/Skins/AngularAdmin/threejs/models/cloud2.obj', function ( object ) {
            //     // object.traverse( function ( child ) {
            //     //     if ( child instanceof THREE.Mesh ) {
            //     //         child.material.map = texture;
            //     //     }
            //     // } );
            //     obj=object;
            //     objContainer.add( object );
            //
            //     //Recentre l'objet pour avoir des coordonnées en base 0
            //     var box = new THREE.Box3().setFromObject( obj );
            //     box.getCenter( obj.position ); // this re-sets the mesh position
            //     obj.position.multiplyScalar( - 1 );
            //     obj.castShadow =true;
            //     obj.receiveShadow = true;
            //     objContainer.rotation.x = -Math.PI/2
            //
            // }, onProgress, onError );
            //
            // this.threeObj = objContainer;


            var cloud = new THREE.Object3D();
            var geometrycloud = new THREE.BoxGeometry(2.5, 2.5, 2);
//            var materialserv= new THREE.MeshLambertMaterial({color: 0xB0F2B6});
            var materialcloud= new THREE.MeshLambertMaterial({color: colors[0]});
            var cloud1 = new THREE.Mesh(geometrycloud, materialcloud);
            cloud.add(cloud1);
            this.uuids.push(cloud1.uuid);

            this.threeObj = cloud;

            break;

        case 'firewall':
            var firewall = new THREE.Object3D();

            var height = 0.5;
            var width = 1.3;
            var depth =0.5;
            var geometryb = new THREE.BoxGeometry(width, height, depth);
            //var materialb = new THREE.MeshLambertMaterial({color: 0xB0F2B6});

            var materialb = new THREE.MeshLambertMaterial({color: colors[0]});
            var bottom =0;
            var left =0;
            var rangs =5;
            var bpr =4;
            var bricks = new Array();
            for(var i =0; i<bpr*rangs;i++){
                if(i!=0 && !(i%bpr)) {
                    bottom += height;
                    left = 0;
                }
                if(!(i%(2*bpr))) left += width/2;
                bricks[i] = new THREE.Mesh(geometryb, materialb);
                bricks[i].position.x = left;
                bricks[i].position.y = bottom;
                firewall.add(bricks[i]);
                this.uuids.push(bricks[i].uuid);
                left += width+0.01;
            };
            firewall.castShadow = true;
            firewall.receiveShadow = true;

            firewall.traverse( function ( child ) {
                if ( child instanceof THREE.Mesh ) {
                    child.castShadow = true;
                    child.receiveShadow = true;
                }
            } );
            firewall.translateX(-2.525);
            firewall.translateY(-1.225);
            var cont = new THREE.Object3D();
            cont.add(firewall);
            this.threeObj = cont;
            break;

        case 'link':

            var geometry = new THREE.CylinderGeometry( 0.2, 0.4, 20, 32 );
            var material = new THREE.MeshLambertMaterial( {color: colors[0]} );
            var cylinder = new THREE.Mesh( geometry, material );
            cylinder.rotation.x = 90*Math.PI/180;

            this.threeObj = cylinder;
            this.uuids.push(cylinder.uuid);
            break;

        case 'hub':
        case 'switch':


            var switchub = new THREE.Object3D();

            var onProgress = function ( xhr ) {
                //if ( xhr.lengthComputable ) {
                //var percentComplete = xhr.loaded / xhr.total * 100;
                //console.log( Math.round(percentComplete, 2) + '% downloaded' );
                //}
            };

            var onError = function ( xhr ) {
                console.log('Error', xhr);
            };


            var loader = new THREE.OBJLoader( this.scn.uProps.managers[0] );
            var that = this;
            loader.load( '/Skins/AngularAdmin/threejs/models/switch.obj', function ( object ) {
                // object.traverse( function ( child ) {
                //     if ( child instanceof THREE.Mesh ) {
                //         child.material.map = texture;
                //     }
                // } );
                var obj3d = object;
                switchub.add( object );

                obj3d.castShadow =true;
                obj3d.receiveShadow = true;

                var materialserv= new THREE.MeshLambertMaterial({color: colors[0]});
                obj3d.traverse( function ( child ) {
                    if ( child instanceof THREE.Mesh ) {
                        child.material = materialserv;
                        that.uuids.push(child.uuid);
                    }
                } );

                //console.log('obj1',obj);
                //Centrage / Mise en place
                var box = new THREE.Box3();
                box.setFromObject( obj3d );
                var center = box.getCenter(); //Centre global
                var centerbis = new THREE.Vector3();
                Object.assign(centerbis,center);
                //console.log(centerbis);
                obj3d.worldToLocal(centerbis);
                //console.log(centerbis);

                obj3d.position.set(-centerbis.x, -centerbis.y, -centerbis.z ); //position locale
                obj3d.rotation.y = Math.degToRad(90);
                //var srvAxis1 = new THREE.AxisHelper(4);
                //obj3d.add(srvAxis1);
                //srvAxis1.position.set(centerbis.x, centerbis.y, centerbis.z );

                //console.log('center',center);
                //console.log('serv',serv);

                // var box = new THREE.BoxHelper( obj3d, 0xffff00 );
                // scn.add( box );

                //Reinit rotation / position / scale
                //console.log(obj3d);
                obj3d.updateMatrix();

                obj3d.traverse( function ( child ) {
                    if ( child instanceof THREE.Mesh ) {
                        child.updateMatrix();

                        child.geometry.applyMatrix( object.matrix );

                        child.position.set( 0, 0, 0 );
                        child.rotation.set( 0, 0, 0 );
                        child.scale.set( 1, 1, 1 );
                        child.updateMatrix();
                    }
                } );

                obj3d.position.set( 0, 0, 0 );
                obj3d.rotation.set( 0, 0, 0 );
                obj3d.scale.set( 1, 1, 1 );
                obj3d.updateMatrix();


            }, onProgress, onError );
            //var srvAxis = new THREE.AxisHelper(4);
            //serv.add(srvAxis);

            //console.log('aaaaaaaa',this);



            //
            //
            // var geometryhub = new THREE.BoxGeometry(2.5, 0.7, 2);
            // //var materialhub= new THREE.MeshLambertMaterial({color: 0xB0F2B6});
            // var materialhub= new THREE.MeshLambertMaterial({color: colors[0]});
            // var hub = new THREE.Mesh(geometryhub, materialhub);
            // switchub.add(hub);
            //this.uuids.push(hub.uuid);

            this.threeObj = switchub;

            break;

        case 'pc':
        case 'poste':
        case 'workstation':
            var poste = new THREE.Object3D();
            var geometrypc = new THREE.BoxGeometry(1.3, 2.5, 2.6);
            var materialpc= new THREE.MeshLambertMaterial({color: colors[0]});
            var pc = new THREE.Mesh(geometrypc, materialpc);


            var geometryscrb = new THREE.BoxGeometry(3, 2.5, 0.3);
            var materialscrb= new THREE.MeshLambertMaterial({color: colors[0]});
            var scrb = new THREE.Mesh(geometryscrb, materialscrb);

            var geometryscr = new THREE.BoxGeometry(2.8, 2.3, 0.2);
            var materialscr= new THREE.MeshLambertMaterial({color: colors[0]});
            var scr = new THREE.Mesh(geometryscr, materialscr);
            scr.position = new THREE.Vector3(0.1,0.1,0);

            poste.add(pc,scrb,scr);
            pc.position.set(2.8,0.1,0);
            scrb.position.set(0,0,0);
            scr.position.set(-0.1,0.1,-0.1);

            this.threeObj = poste;
            this.uuids.push(pc.uuid,scrb.uuid,scr.uuid);
            break;

        case 'serv':
        case 'server':
        case 'serveur':
            var serv = new THREE.Object3D();

            // model loader
            var onProgress = function ( xhr ) {
                //if ( xhr.lengthComputable ) {
                    //var percentComplete = xhr.loaded / xhr.total * 100;
                    //console.log( Math.round(percentComplete, 2) + '% downloaded' );
                //}
            };

            var onError = function ( xhr ) {
                console.log('Error', xhr);
            };


            var loader = new THREE.OBJLoader( this.scn.uProps.managers[0] );
            var that = this;
            loader.load( '/Skins/AngularAdmin/threejs/models/Tower_pc.obj', function ( object ) {
                // object.traverse( function ( child ) {
                //     if ( child instanceof THREE.Mesh ) {
                //         child.material.map = texture;
                //     }
                // } );
                var obj3d = object;
                serv.add( object );

                obj3d.castShadow =true;
                obj3d.receiveShadow = true;

                var materialserv= new THREE.MeshLambertMaterial({color: colors[0]});
                obj3d.traverse( function ( child ) {
                    if ( child instanceof THREE.Mesh ) {
                        child.material = materialserv;
                        that.uuids.push(child.uuid);
                    }
                } );

                //console.log('obj1',obj);

                //Centrage
                var box = new THREE.Box3();
                box.setFromObject( obj3d );
                var center = box.getCenter(); //Centre global
                var centerbis = new THREE.Vector3();
                Object.assign(centerbis,center);
                //console.log(centerbis);
                obj3d.worldToLocal(centerbis);
                //console.log(centerbis);
                obj3d.position.set(-centerbis.x, -centerbis.y, -centerbis.z ); //position locale
                //var srvAxis1 = new THREE.AxisHelper(4);
                //obj3d.add(srvAxis1);
                //srvAxis1.position.set(centerbis.x, centerbis.y, centerbis.z );

                //console.log('center',center);
                //console.log('serv',serv);




                //Reinit rotation / position / scale
                obj3d.updateMatrix();

                obj3d.traverse( function ( child ) {
                    if ( child instanceof THREE.Mesh ) {
                        child.updateMatrix();

                        child.geometry.applyMatrix( object.matrix );

                        child.position.set( 0, 0, 0 );
                        child.rotation.set( 0, 0, 0 );
                        child.scale.set( 1, 1, 1 );
                        child.updateMatrix();
                    }
                } );

                obj3d.position.set( 0, 0, 0 );
                obj3d.rotation.set( 0, 0, 0 );
                obj3d.scale.set( 1, 1, 1 );
                obj3d.updateMatrix();

                // var box = new THREE.BoxHelper( obj3d, 0xffff00 );
                // scn.add( box );


            }, onProgress, onError );
            //var srvAxis = new THREE.AxisHelper(4);
            //serv.add(srvAxis);

            //console.log('aaaaaaaa',this);


            this.threeObj = serv;



            //Basic
            // var serv = new THREE.Object3D();
            // var geometryserv = new THREE.BoxGeometry(2.5, 2.5, 2);
            // var materialserv= new THREE.MeshLambertMaterial({color: colors[0]});
            // var serv1 = new THREE.Mesh(geometryserv, materialserv);
            // serv.add(serv1);
            // var geometrysp1 = new THREE.SphereGeometry( 0.1, 32, 32 );
            // var materialsp1 = new THREE.MeshLambertMaterial( {color: colors[1]} );
            // var sphere1 = new THREE.Mesh( geometrysp1, materialsp1 );
            // var geometrysp2 = new THREE.SphereGeometry( 0.1, 32, 32 );
            // var materialsp2 = new THREE.MeshLambertMaterial( {color: colors[2]} );
            // var sphere2 = new THREE.Mesh( geometrysp2, materialsp2 );
            // sphere1.position.z = -1;
            // sphere1.position.x = -0.2;
            // sphere2.position.z = -1;
            // sphere2.position.x = 0.2;
            // var sphere3 = sphere1.clone();
            // var sphere4 = sphere2.clone();
            // sphere3.position.x = 0.6;
            // sphere4.position.x = -0.6;
            // serv.add(sphere1,sphere2,sphere3,sphere4);
            //
            // this.threeObj = serv;
            // this.uuids.push(serv1.uuid,sphere1.uuid,sphere2.uuid,sphere3.uuid,sphere4.uuid);

            break;
        case 'cluster':

            var cluster =  new THREE.Object3D();
            var geometrysp1 = new THREE.SphereGeometry( 1.5, 32, 32 );
            var materialsp1 = new THREE.MeshLambertMaterial( {color: colors[0]} );
            var sphere1 = new THREE.Mesh( geometrysp1, materialsp1 );
            cluster.add(sphere1);

            this.threeObj = cluster;
            this.uuids.push(sphere1.uuid);

            if(this.subObjs != null && this.subObjs != undefined){
                for(var n in this.subObjs){
                    var sub = new MapItem(this.subObjs[n],this.scn);
                    this.add(sub);
                }
            }
            break;
        default:
            var geometry = new THREE.BoxGeometry(1, 1, 1);
            var material = new THREE.MeshBasicMaterial({color: colors[0]});
            var cube = new THREE.Mesh(geometry, material);

            this.threeObj = cube;
            this.uuids.push(cube.uuid);
    }

    this.update = function(obj){
        this.status = obj.status || this.status;
        colors = this.setColors();
        for (var n in this.threeObj.children){
            if(this.threeObj.children[n] instanceof THREE.Mesh){
                this.threeObj.children[n].material.color.setHex(colors[0]);
                this.threeObj.children[n].needsUpdate = true;
            }
        }
    };

    this.spotlight = function(apply){
        var mainMesh = this.getMainMesh();
        for(var n in this.tweens){
            this.tweens[n].stop();
            TWEEN.remove(this.tweens[n]);
        }
        this.tweens = new Array();

        if(apply){
            var tweenSize = new TWEEN.Tween({scale:1}).to({scale:1.5},500).onUpdate(function () {
                mainMesh.scale.set(this.scale,this.scale,this.scale);
            }).start();
            var tweenInit = new TWEEN.Tween(mainMesh.rotation).to({x:Math.degToRad(0),y:Math.degToRad(5),z:Math.degToRad(0)},100);
            var tweenForth = new TWEEN.Tween(mainMesh.rotation).to({x:Math.degToRad(0),y:Math.degToRad(-5),z:Math.degToRad(0)},1500);
            var tweenBack = new TWEEN.Tween(mainMesh.rotation).to({x:Math.degToRad(0),y:Math.degToRad(5),z:Math.degToRad(0)},1500);
            tweenInit.chain(tweenForth);
            tweenForth.chain(tweenBack);
            tweenBack.chain(tweenForth);
            this.tweens.push(tweenInit,tweenForth,tweenBack);

            //mainMesh.scale.set(1.5,1.5,1.5);
            tweenInit.start();
        } else{
            var iScale = mainMesh.scale.x;
            if(iScale !=1)
                var tweenReSize = new TWEEN.Tween({scale:1.5}).to({scale:1},500).onUpdate(function () {
                    mainMesh.scale.set(this.scale,this.scale,this.scale);
                }).start();
            var tweenReset = new TWEEN.Tween(mainMesh.rotation).to({x:0,y:0,z:0},1000).start();
            //tweenReset.onComplete(function(){console.log('JOB\'S DONE!!!')});

            //mainMesh.scale.set(1,1,1);
        }

    }

    this.compute = function () {

        //recalcul des coordonnées
        if (this.typeObj=='firewall'){
            //alors décalage de hauteur
            var nb = this.children.length;
            var angle = 25;
            var distance = 10;
        }else if (this.typeObj=='cloud') {
            //alors décalage d'angle
            var nb = this.children.length;
            var angle = 75;
            var distance = 10;
        }else{
            //alors décalage d'angle
            var nb = this.children.length;
            var angle = 30;
            var distance = 12;
        }
        /** compute **/
        var startangle = -((nb-1)*angle/2);
        for (var i in this.children){
            //console.log('compute ',i,nb,startangle + i*angle,startangle);
            //if (i%2)var dist = distance*2; else var dist = distance;
            var dist = distance;
            this.children[i].threeObj.position.z = dist;
            if (!this.children[i].cylinder) {
                var geometry = new THREE.CylinderGeometry(0.2, 0.2, dist, 32);
                var material = new THREE.MeshLambertMaterial({color: 0xffff88});
                var cylinder = new THREE.Mesh(geometry, material);
                cylinder.translateZ(dist / 2);
                cylinder.rotation.x = 90 * Math.PI / 180;
                this.children[i].cylinder = cylinder;
            }
            this.children[i].container.add(cylinder);
            // if (this.typeObj=='firewall')
            //     this.children[i].container.rotation.x = Math.degToRad(startangle + i*angle);
            // else
            //     this.children[i].container.rotation.y = Math.degToRad(startangle + i*angle);
            this.children[i].container.rotation[this.rotateAxis] = Math.degToRad(startangle + i*angle);
            this.children[i].compute();
        }
    };

    this.getMainMesh = function(){
        var mainMesh = null;
        for (var n in this.threeObj.children){
            if(this.threeObj.children[n] instanceof THREE.Mesh || this.threeObj.children[n] instanceof THREE.Group){
                mainMesh = this.threeObj.children[n];
                break;
            }
        }

        return mainMesh;
    };


    //Affiche l'objet ( toutes les scenes )
    this.show = function(){
        this.threeObj.visible = true;
    };

    //Cache l'objet ( toutes les scenes )
    this.hide = function(){
        this.threeObj.visible = false;
    };

    //Definit le container de rotation
    this.setContainer = function (container) {
        this.container = container;
    };
    //Deplace l'objet
    //TODO : Verif coords
    this.move = function(coords){
        this.threeObj.position.x= coords['x'];
        this.threeObj.position.y= coords['y'];
        this.threeObj.position.z= coords['z'];
    };

    this.move(positions);

    if(this.scn)
        this.scn.uProps.items.push(this);
}