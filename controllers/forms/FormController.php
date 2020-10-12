<?php
/** User: BooHan**/
namespace app\controllers\forms;
use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\core\Response;
use app\models\forms\Form;
use app\models\forms\Widget;
use app\models\forms\Widget_Option;

class FormController extends BaseController{

	public function formConfig(){
		$forms = Form::findAll([
			'where' => ['projectID' => Application::$app->user->unit->project->projectID, 'isActive' => true],
			'whereNull' => ['deleted_at', 'deleted_by']
		]);

		return $this->render('configuration/form/formIndex', ["forms" => $forms]);
	}

	public function formCreate(Request $request){

		$r = $request->getBody();
		$formID = $r['id'];

		if(!$formID){
			//insert new form
			$insert_form = new Form();
			$insert_form->projectID = Application::$app->user->unit->project->projectID;
			$insert_form->isActive = true;
			$formID = $insert_form->save();

			header("Location: /config/form/create?id=".$formID);
		}
		/*else{
			//update last updated date
			$update_form = new Form();
			$update_form->updated_at = date("Y-m-d H:i:s");
			$update_form->updated_by = Application::$app->user->username;

			$result = $update_form->Update([
				'where' => ["formID" => $formID]
			]);
		}*/

		$form = Form::findOne([
				'where' => ['formID' => $formID]
			]);

		$form->widgets = Widget::findAll([
			'where' => ['formID' => $formID],
			'whereNull' => ['deleted_at', 'deleted_by']
		]);

		foreach ($form->widgets as $key => $value) {
			$value->widgetoptions = Widget_Option::findAll([
				'where' => ['widgetID' => $value->widgetID],
				'whereNull' => ['deleted_at', 'deleted_by'],
				'order' => ["sequence" => "asc"]
			]);
			
		}


		return $this->render('configuration/form/formCreate', ['form' => $form]);
	}

	public function updateForm(Request $request){
		try{
			$result = 0;
			if($request->isPost()){
				$form = Form::loadObject("form");
				
				$updateform = new Form();
				$updateform->formName = $form->formName ?? null;
				$updateform->formDescription = $form->formDescription ?? null;

				$result = $updateform->Update([
							'where' => ["formID" => $form->formID]
						]);

				return $result;
			}
		}
		catch(\Exception $ex){
			throw $ex;
		}
	}

	public function saveWidget(Request $request){
		try{
			if($request->isPost()){
				$widget = Widget::loadObject("widget");
				$widget->widgetID = $widget->save();

				return json_encode($widget);
			}
		}
		catch(\Exception $ex){
			throw $ex;
		}
	}

	public function updateWidgets(Request $request){
		try{
			$result = 0;
			if($request->isPost()){
				$widgets = Widget::loadArray("widgets");

				foreach ($widgets as $key => $value) {
					$updatewid = new Widget();
					$updatewid->label = $value->label ?? null;
					$updatewid->position_y = $value->position_y ?? null;
					$updatewid->height = $value->height ?? null;


					$result = $updatewid->Update([
							'where' => ["widgetID" => $value->widgetID]
						]);
				}
			}

			return $result;
		}
		catch(\Exception $ex){
			throw $ex;
		}
	}

	public function saveOptions(Request $request){
		try{
			$result = 0;
			if($request->isPost()){
				$options = Widget_Option::loadObject("options");
				$options->widgetoptionID = $options->save();
			}

			return json_encode($options);
		}
		catch(\Exception $ex){
			throw $ex;
		}
	}

	public function updateOptions(Request $request){
		try{
			$result = 0;
			if($request->isPost()){
				$options = Widget_Option::loadArray("options");
				
				foreach ($options as $key => $opt) {
					# code...
					if($opt->widgetoptionID){
						//update
						$updateopt = new Widget_Option();
						$updateopt->value = $opt->value ?? null;
						$updateopt->sequence = $opt->sequence ?? null;

						$result = $updateopt->Update([
							'where' => ["widgetoptionID" => $opt->widgetoptionID]
						]);

					}
				}
				
			}

			return $result;
		}
		catch(\Exception $ex){
			throw $ex;
		}
	}

	public function deleteOptions(Request $request){
		try{
			$result = 0;
			if($request->isPost()){
				$options = Widget_Option::loadObject("options");

				if($options->widgetoptionID){
					//update
					$removeopt = new Widget_Option();

					$result = $removeopt->Remove([
						'where' => ["widgetoptionID" => $options->widgetoptionID]
					]);

				}
			}
			return $result;

		}catch(\Exception $ex){
			throw $ex;
		}
	}

	/*public function formEdit(Request $request){
		$r = $request->getBody();
		$formID = $r['id'];

		if($formID){
			$form = Form::findOne([
				'where' => ['formID' => $formID]
			]);

			return $this->render('configuration/form/formEdit', ["form" => $form]);
		}

		return Application::$app->response->redirect("/config/form");
	}*/
}
?>