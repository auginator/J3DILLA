<!DOCTYPE html>
<html lang="en">
	<head>
		<title>augsMix - JDILLA – ModelViewer</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body {
				font-family: Monospace;
				background-color: #f0f0f0;
				margin: 0px;
				overflow: hidden;
			}
		</style>
	</head>
	<body>

		<script type="text/javascript" src="js/three.js"></script>
		<script type="text/javascript" src="js/loaders/STLLoader.js"></script>
		<script type="text/javascript" src="js/Stats.js"></script>

		<script>

			var container, stats;

			var camera, scene, renderer;

			var cube, plane;
			var stlLoader, stlModel, stlMaterial;
			<?php
				$stl = (empty($_GET["stl"])) ? 'hollowCube' : $_GET['stl'];
				echo "			var stlFileName = 'myModels/$stl.stl';";
			?>	

			var targetRotation = 0;
			var targetRotationOnMouseDown = 0;

			var mouseX = 0;
			var mouseXOnMouseDown = 0;

			var windowHalfX = window.innerWidth / 2;
			var windowHalfY = window.innerHeight / 2;

        	/* Configure colors here */
        	var directionlLightColor = 0xE7635E;
        	var ambientLightColor = 0x00928C;
        	var materialColor = 0xF8E9A8;
        	var stlIsLoaded = false;
        	
        	/* Camera Params */
        	var camDistance = 150;
        	var theta = Math.PI / 4;


			init();
			animate();

			function init() {
				
				container = document.createElement( 'div' );
				document.body.appendChild( container );

				var info = document.createElement( 'div' );
				info.style.position = 'absolute';
				info.style.top = '10px';
				info.style.width = '100%';
				info.style.textAlign = 'center';
				info.innerHTML = 'Drag to spin!';
				container.appendChild( info );

				/* Camera */
				camera = new THREE.PerspectiveCamera( 70, window.innerWidth / window.innerHeight, 1, 1000 );
				camera.position.y = 50;
				camera.position.z = 150;

				scene = new THREE.Scene();

				/* Lights */

				var ambient = new THREE.AmbientLight( ambientLightColor );
				scene.add( ambient );

				var directionalLight = new THREE.DirectionalLight( directionlLightColor );
					directionalLight.position.set( 0, 0, 1 ).normalize();

				scene.add( directionalLight );


				// Cube
/*
				var geometry = new THREE.CubeGeometry( 200, 200, 200 );

				for ( var i = 0; i < geometry.faces.length; i ++ ) {

					geometry.faces[ i ].color.setHex( Math.random() * 0xffffff );

				}

				var cubeMaterial = new THREE.MeshBasicMaterial( { vertexColors: THREE.FaceColors } );

				cube = new THREE.Mesh( geometry, cubeMaterial );
				cube.position.y = 150;
				scene.add( cube );
*/
				// Plane

				var geometry = new THREE.PlaneGeometry( 200, 200 );
				geometry.applyMatrix( new THREE.Matrix4().makeRotationX( - Math.PI / 2 ) );

				var material = new THREE.MeshBasicMaterial( { color: 0xe0e0e0 } );

				// plane = new THREE.Mesh( geometry, material );
				// scene.add( plane );

				renderer = new THREE.CanvasRenderer();
				renderer.setSize( window.innerWidth, window.innerHeight );

				container.appendChild( renderer.domElement );

				// STL Model
				stlMaterial = new THREE.MeshLambertMaterial({color: materialColor});
				stlLoader = new THREE.STLLoader();

				stlLoader.addEventListener( 'load', function ( event ) {
					stlModel = event.content;
					if(stlMaterial instanceof THREE.Material) {
						for ( var i = 0; i < stlModel.children.length; i ++ ) {
						   	stlModel.children[ i ].material = stlMaterial;
						  	stlModel.children[ i ].rotation.x = 4.71238898038469;
						  	stlModel.children[ i ].rotation.z = 150;
						}
					}
					
					stlModel.updateMatrix(); // Not sure if this is needed.
					scene.add( stlModel );
					
					// Start rendered, now that our STL had loaded and is ready to go. 
					stlIsLoaded = true;
					//render();
				});
				stlLoader.load( stlFileName );

				//Stats Box
				stats = new Stats();
				stats.domElement.style.position = 'absolute';
				stats.domElement.style.top = '0px';
				container.appendChild( stats.domElement );

				document.addEventListener( 'mousedown', onDocumentMouseDown, false );
				document.addEventListener( 'touchstart', onDocumentTouchStart, false );
				document.addEventListener( 'touchmove', onDocumentTouchMove, false );

				//

				window.addEventListener( 'resize', onWindowResize, false );

			}

			function onWindowResize() {

				windowHalfX = window.innerWidth / 2;
				windowHalfY = window.innerHeight / 2;

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight );

			}

			//

			function onDocumentMouseDown( event ) {

				event.preventDefault();

				document.addEventListener( 'mousemove', onDocumentMouseMove, false );
				document.addEventListener( 'mouseup', onDocumentMouseUp, false );
				document.addEventListener( 'mouseout', onDocumentMouseOut, false );

				mouseXOnMouseDown = event.clientX - windowHalfX;
				targetRotationOnMouseDown = targetRotation;

			}

			function onDocumentMouseMove( event ) {

				mouseX = event.clientX - windowHalfX;

				targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.02;

			}

			function onDocumentMouseUp( event ) {

				document.removeEventListener( 'mousemove', onDocumentMouseMove, false );
				document.removeEventListener( 'mouseup', onDocumentMouseUp, false );
				document.removeEventListener( 'mouseout', onDocumentMouseOut, false );

			}

			function onDocumentMouseOut( event ) {

				document.removeEventListener( 'mousemove', onDocumentMouseMove, false );
				document.removeEventListener( 'mouseup', onDocumentMouseUp, false );
				document.removeEventListener( 'mouseout', onDocumentMouseOut, false );

			}

			function onDocumentTouchStart( event ) {

				if ( event.touches.length === 1 ) {

					event.preventDefault();

					mouseXOnMouseDown = event.touches[ 0 ].pageX - windowHalfX;
					targetRotationOnMouseDown = targetRotation;

				}

			}

			function onDocumentTouchMove( event ) {

				if ( event.touches.length === 1 ) {

					event.preventDefault();

					mouseX = event.touches[ 0 ].pageX - windowHalfX;
					targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.05;

				}

			}

			function animate() {
				/* Use Polar Coordinates to revolve the camera around the object */
				camera.position.x = camDistance * Math.cos(theta);
				camera.position.z = camDistance * Math.sin(theta);
				theta += 0.004;

				if (stlIsLoaded) {
					camera.lookAt(stlModel.children[0].position);
				} 

				// else {
				// 	camera.lookAt(cube.position);
				// }

				requestAnimationFrame( animate );

				render();
				stats.update();

			}

			function render() {
				//if (stlIsLoaded) plane.rotation.y =  stlModel.children[ 0 ].rotation.z += ( targetRotation - stlModel.children[ 0 ].rotation.z ) * 0.05;
				if (stlIsLoaded) stlModel.children[ 0 ].rotation.z += ( targetRotation - stlModel.children[ 0 ].rotation.z ) * 0.05;
				renderer.render( scene, camera );

			}

		</script>

	</body>
</html>
