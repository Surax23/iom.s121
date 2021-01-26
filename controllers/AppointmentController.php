<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Students;
use app\models\Groups;
use app\models\Skills;
use app\models\SkillsChecked;
use app\models\SkillsByGroup;
use app\models\SkillsByAge;
use app\models\DevDir;
use app\models\StudentCheckByTeacher;
use app\models\Age;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;

class AppointmentController extends \yii\web\Controller
{
	public function actionIndex()
	{
		$dataProvider = new ActiveDataProvider([
			'query' => Students::find(),
		]);

		$user_id = Yii::$app->user->id;
		$appointments = StudentCheckByTeacher::find()->all();
		$users = User::find()->all();
		//$skillsch = SkillsChecked::find()->indexBy('id')->all();
		//$age = Age::find()->all();

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'appointments' => $appointments,
			'users' => $users,
			'sort' => ['attributes' => ['name']],
			//'sort' => ['attribute' => ['age_name']],
		]);
	}

	public function actionEdit($id)
	{
		$scbt = StudentCheckByTeacher::find()
			->where(['students_id' => $id])
			->indexBy('id')
			->all();

		$data['student'] = Students::findOne($id);
		if ($data['student']->age_id >= 5)
		{
			$data['high'] = true;
			$groups = array(6,7,8,9,11);
		}
		else
		{
			$data['high'] = false;
			$groups = array(2,3,4,5,10);
		}
		$data['teachers'] = User::find()
			->select('*')
			->where(['in', 'user.groups', $groups])
			->all();
		$data['groups'] = Groups::find()->where(['in', 'id', $groups])->all();

		if ($scbt == null)
		{
			$scbt = [new StudentCheckByTeacher()];
			foreach ($groups as $g) {
				$scbt[] = new StudentCheckByTeacher();
				$key = array_key_last($scbt);
				$scbt[$key]->students_id = $id;
				$scbt[$key]->status = 0;
				$scbt[$key]->group_id = $g;
				$scbt[$key]->save(false);
			}
		}

		if (Model::loadMultiple($scbt, Yii::$app->request->post()) && Model::validateMultiple($scbt)) {
			foreach ($scbt as $sk) {
				$sk->save(false);
			}
			Yii::$app->session->setFlash('success', '<h5>Сохранено!</h5>');
			return $this->render('edit', [
				'scbt' => $scbt,
				'data' => $data,
			]);
		}

		return $this->render('edit', [
			'scbt' => $scbt,
			'data' => $data,
			]);
	}

	public function actionPrint($id) {
		$scbt = StudentCheckByTeacher::find()
			->where(['students_id' => $id])
			->indexBy('id')
			->all();

		if (!User::isUserAdmin(Yii::$app->user->id) || $scbt == null) {
            return $this->goBack();
        }

        $data['student'] = Students::findOne($id);
		$data['teachers'] = User::find()
			->select('*')
			->all();
		$data['skills_ch'] = SkillsChecked::find()->where(['students_id' => $id])->all();
		$data['skills'] = Skills::find()->all();
		$data['dev_dir_id'] = DevDir::find()->all();


		return $this->renderPartial('print', [
			'scbt' => $scbt,
			'data' => $data,
		]);
	}

	public function actionClear($id)
	{
		$scbt = StudentCheckByTeacher::find()
			->where(['students_id' => $id]);
		$scbt->delete();
		return $this->redirect('?r=appointment/index');
	}
}
