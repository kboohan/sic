<?php 
/** User: BooHan**/
namespace app\controllers;
use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\core\Response;
use app\models\administrations\User;
use app\models\Login;
use app\core\middlewares\AuthMiddleware;

class DashboardController extends BaseController{

	public function dashboard(Request $request){
		return $this->render('dashboard');
	}
}


?>