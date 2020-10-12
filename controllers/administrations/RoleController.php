<?php
/** User: BooHan**/
namespace app\controllers\administrations;
use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\core\Response;
use app\models\administrations\User;
use app\models\administrations\Role;
use app\models\administrations\Access;
use app\models\administrations\Menu;
use app\models\administrations\Feature;


class RoleController extends BaseController{


	public function Role(){
		//get all role
		$roles = Role::findAll([
			'where' => ['isActive' => true],
			'whereNull' => ['deleted_at', 'deleted_by']
		]);

		foreach ($roles as $key => $role) {
			$role->user_count = User::count([
				"where" => ['roleID' => $role->roleID],
				'whereNull' => ['deleted_at', 'deleted_by']
			]);
		}

		return $this->render("administration/role", ["roles" => $roles]);
	}

	public function selectAccess(Request $request){
		try{
			$access = new Access();
			if($request->isPost()){
				//populate the data into model
				$access->loadData($request->getBody());
				
				//get all menu
				$menus = Menu::findAll([
					'select' => ["menus.menuID", "menus.menuName", "menus.menuLevel", "menus.sequence", "menus.link", "menus.parentMenuID", "menus.link", "menus.isActive", "menus.menuIcon", "access.accessID", "access.roleID", "access.accessRight"],
					'where' => ['menus.isActive' => true],
					'whereNull' => ["menus.deleted_by", "menus.deleted_at"],
					'leftJoin' => ['access' => ['access.menuID' => 'menus.menuID', 'access.roleID' => $access->roleID]]
	 			]);

				foreach ($menus as $key => $menu) {
					# code...
					$menu->features = Feature::findAll([
						'select' => ["features.featureID", "features.featureName", "features.isActive", "access.accessID", "access.roleID", "access.accessRight"],
						'where' => ['features.isActive' => true, 'features.menuID' => $menu->menuID],
						'whereNull' => ["features.deleted_by", "features.deleted_at"],
						'leftJoin' => ['access' => ['access.featureID' => 'features.featureID', 'access.roleID' => $access->roleID]]
					]);
				}

				return json_encode($menus);
			}
		}catch(\Exception $ex){
			throw $ex;
		}
		
	}

	public function saveAccess(Request $request){
		try{	
			$accessArr = [];
			$results = [];
			if($request->isPost()){
				//populate the data into model
				$access = new Access();
				$accessArr = Access::loadArray("access");

				foreach ($accessArr as $key => $value) {
					# code...
					//check is update or insert
					if($value->accessID > 0){
						//update
						$updateaccess = new Access();
						$updateaccess->accessRight = $value->accessRight;
						
						$result = $updateaccess->Update([
							/*'set' => ["accessRight" => $value->accessRight],*/
							'where' => ["accessID" => $value->accessID]
						]);

						array_push($results, $result);
					}else{
						//insert
						$result = $value->save();
						array_push($results, $result);
					}
				}

				return (!in_array(0, $results));

			}
		}catch(\Exception $ex){
			throw $ex;
		}
		
	}
}
?>