<?php
	mb_internal_encoding("UTF-8");
	session_start();
	
	require 'vendor/autoload.php';
	
	$twigView = new \Slim\Extras\Views\Twig();	
	
	$app = new \Slim\Slim(array(
	    'view' => $twigView,
	    'templates.path' => __DIR__.'/templates/'
	));

	$app->get('/test', function() use ($app) {
		echo "asdasdsa";
	});

	/*
		Genera la pagina principal
	 */
  	$app->get('/', function() use ($app) {
		
  		if( !isset( $_SESSION['usuario'] ) ){
			$app->render('index.twig.html', array(	            
		       'path' => "./",
		    ));
	    }
	    else{
	    	// ya se encuentra logueado.
	    	$usuario = $_SESSION['usuario'];
	    	
	    	$app->render('index.twig.html', array(
	    		'path' => './',
	    		'usuario' => $usuario,
    		));	    	

	    }
    });


  	/*
  		Se encarga de finalizar la sesion
  	 */
  	$app->get('/logout', function() use ($app){
  		$_SESSION['usuario'] = null;
  		$app->redirect('./');
  	});

  	/*
  		Se encarga del login
  	 */
  	$app->post('/login', function() use ($app){

  		$req=  $app->request();
  		$user = $req->post('user');
  		$password = $req->post('password');

  		//wlog("Login :: User:".$user." Password:".$password);

  		$usuario = comprobarLogin($user,$password);



  		// Si se logueo correctamente lo dirigimos al index
  		if($usuario){
  			$_SESSION['usuario'] = $usuario;
  			
  			$app->redirect('./');

  		//Le avisamos que no se logueo correctamente
  		} else {
  			$app->render('index.twig.html', array(	            
		       'path' => "./",
		       'mensaje' => "Usuario o contraseña invalido",
		    ));
  		}

  	});

  	
	
	$app->run();





	
	/*
		Comprueba si existe un usuario con un password
		Si existe retorna todos sus datos.
		Si no, retorna false.
	 */
  	function comprobarLogin($user, $pass){
		$sql = "select * from Usuarios where nombre_usuario = :user and contrasenha =:pass;";
	    try {
	        $db = getConnection();
            $stmt = $db->prepare($sql); 
	        $stmt->bindParam("user", $user);
	        $stmt->bindParam("pass", $pass);
	        $stmt->execute();
	        $usuario = $stmt->fetch(PDO::FETCH_OBJ);
	        $db = null;	 
	        
	        if($usuario){
	        	return $usuario;
	        }


	    } catch(PDOException $e) {
	        //wlog("comprobarLogin :: ERROR :: ". $e->getMessage() );
	        return false;
	    }
	    return false;
  	}

  	/*
  		Se encarga de devolvere un puntero 
  		a la conexion de BD.
  	 */
	function getConnection() {
	    $dbhost="127.0.0.1";

	    //$dbuser="marcossanabria";
	    //$dbpass="admin";
	    
	    $dbuser="root";
	    $dbpass="alfredito";
	    


	    $dbname="gumaral";	   
	    
	    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    return $dbh;
	}


	
?>
