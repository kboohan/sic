<?php
/** User: BooHan**/
namespace app\controllers\projects;
use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\core\Response;
use app\models\projects\Project;
use app\models\projects\Unit;
use app\models\projects\User_Unit;


class ProjectController extends BaseController{

	public function selectUnit(Request $request){
		try{
			if($request->isPost()){
				//populate the data into model
				/*$userunit_obj = User_Unit::loadObject("user_unit");*/

				$user_unit = User_Unit::findAll([
					'where' => ['user_unit.userName' => Application::$app->user->username],
					'whereNull' => ['user_unit.deleted_at', 'user_unit.deleted_by'],
				]);


				return json_encode($user_unit);

			}
		}catch(\Exception $ex){
			throw new \Exception($ex);
		}
	}
}
?>