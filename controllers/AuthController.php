<?php 
/** User: BooHan**/
namespace app\controllers;
use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\administrations\User;
use app\models\projects\User_Unit;
use app\models\Login;
use app\core\middlewares\AuthMiddleware;

class AuthController extends Controller{

	public function dashboard(Request $request){
		return $this->render('dashboard');
	}

	public function home(Request $request){
		$this->layout = "landing";
		return $this->render('home');
	}

	public function login(Request $request, Response $response){
		$loginForm = new Login();
		if($request->isPost()){
			$loginForm->loadData($request->getBody());
			if($loginForm->validate() && $loginForm->login()){
				//redirect to homepage
				$response->redirect('dashboard');
				//Application::$app->login();
				return;
			}
		}

		
		if(Application::$app->user){
			$response->redirect('dashboard');
			return;
		}else{
			$this->layout = "landing";
			return $this->render('login');
		}
		
	}

	public function register(Request $request){
		//if is post -> register new
		$user = new User();
		if($request->isPost()){
			//populate the data into model
			$user->loadData($request->getBody());
			
			if($user->validate() && $user->save()){
				Application::$app->session->setFlash("success", "Thanks for registering");
				Application::$app->response->redirect('/');
				exit;
			}

			//if is required and wanted to return error
			/*$firstName = $request->getBody()['firstName'];
			if(!$firstName){
				$errors['firstName'] = 'This field is required';
			}*/

		}

		return $this->render('register', [
			'model' => $user
		]);
	}

	public function logout(Request $request, Response $response){
		Application::$app->logout();
		$response->redirect('/');
	}

	public function profile(){
		return $this->render('profile/profile');
	}

	public function updateUser(Request $request){
		$user = new User();
		if($request->isPost()){
			//populate the data into model
			$user->loadData($request->getBody());
			$result = $user->Update([
				'where' => ['id' => Application::$app->user->id]
			]);


			$return_user = null;
			if($result){
				//fetch user
				$return_user = User::FindOne([
					'where' => ['id' => Application::$app->user->id]
				]);
			}

			return json_encode($return_user);;
		}
	}


	//update unit session
	public function logUnit(Request $request){
		try
		{
			if($request->isPost()){
				$userunit_obj = User_Unit::loadObject("user_unit");

				if($userunit_obj->userunitID){
					Application::set_unit($userunit_obj->userunitID);
				}
				
			}

			return true;
			/*Application::log_unitID();*/
		}
		catch(\Exception $ex){
			throw $ex;
		}
		
	}

}


?>