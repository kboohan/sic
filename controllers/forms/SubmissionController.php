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
use app\models\forms\Submission;

class SubmissionController extends BaseController{

	public function formSubmission(){
		$forms = Form::findAll([
			'where' => ['projectID' => Application::$app->user->unit->project->projectID, 'isActive' => true],
			'whereNull' => ['deleted_at', 'deleted_by']
		]);

		return $this->render('hub/form/submissionIndex', ["forms" => $forms]);
	} 
}
?>