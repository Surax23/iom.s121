<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Students;
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

class IomtestController extends Controller
{
	public function actionIndex()
	{
		if (Yii::$app->user->isGuest) {
			return $this->goBack();
		}

		$user_id = Yii::$app->user->id;
		$appointments = StudentCheckByTeacher::find()->where(['user_id' => $user_id])->orderBy('id')->all();
		$status = [];
		$ids = [];
		foreach ($appointments as $app) {
			//if ($app->status < 2)
				//$students[$app->students_id] = $app->status;
				$status[$app->students_id][$app->stage] = $app->status;
				$ids[] = $app->students_id;
		}

		$dataProvider = new ActiveDataProvider([
			'query' => Students::find()->where(['in', 'id', $ids]),
		]);

		//$skillsch = SkillsChecked::find()->indexBy('id')->all();
		//$age = Age::find()->all();

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'status' => $status,
			//'appointments' => $appointments,
			//'skillsch' => $skillsch,
			//'sort' => ['attribute' => ['age_name']],
		]);
	}
	
	public function actionUpdate($id, $stage) // stage -- этап. Всего этапов 3.
	{
		if (Yii::$app->user->isGuest) {
			return $this->goBack();
		}

		$view_only = false;
		$user_id = Yii::$app->user->id;
		$app_data = StudentCheckByTeacher::find()->where(['user_id' => $user_id, 'students_id' => $id]);
		$count = $app_data->count();
		//$max = $app_data->max('id');
		$category = DevDir::find()->all();
		$scbt = StudentCheckByTeacher::find()->where(['user_id' => $user_id, 'students_id' => $id, 'stage' => $stage])->one();
		$status = $scbt->status;
		if ($count > 0) { // && $status < 2) {
			$last_app = $scbt->id;

			$sk_ch = SkillsChecked::find()->indexBy('id')->where(['students_id' => $id, 'user_id' => $user_id, 'attempt' => $scbt->id])->all();
			$student = Students::find()->select('*')->where(['id' => $id])->all()[0];
			if ($sk_ch == null) {
				//$age_id = Students::find()->select('age_id')->where(['id' => $id])->all()[0]->age_id;
				
				$age_id = $student->age_id;
				$user_group = User::find()->select('groups')->where(['id' => Yii::$app->user->id])->all()[0]->groups;
				$select_skills = Skills::find()
					->select('skills.*')
					->leftJoin('skills_by_group', 'skills_by_group.skill_id = skills.id')
					->where(['skills.age_id' => $age_id, 'skills_by_group.group_id' => $user_group])
					->indexBy('id')
					->all();
				//$select_skills = Skills::find()->leftJoin('skills_by_group', '`skills_by_group`.`skill_id` = `skills`.`id`')->where(['`skills_by_group`.`group_id`' => 3])->where(['`skills`.`age_id`' => 6])->indexBy('`skills`.`id`')->all();
				$sk_ch = [new SkillsChecked()];
				foreach ($select_skills as $sk) {
					$sk_ch[] = new SkillsChecked();
					$key = array_key_last($sk_ch);
					$sk_ch[$key]->students_id = $id;
					$sk_ch[$key]->attempt = $last_app;
					$sk_ch[$key]->skills_id = $sk->id;
					$sk_ch[$key]->user_id = Yii::$app->user->id;
					$sk_ch[$key]->save();
				}
			}

			if (Model::loadMultiple($sk_ch, Yii::$app->request->post()) && Model::validateMultiple($sk_ch)) {
				foreach ($sk_ch as $sk) {
					$sk->save(false);
				}
				$tmp_model = StudentCheckByTeacher::findOne($last_app);
				$tmp_model->status = 1;
				$tmp_model->save(false);
				Yii::$app->session->setFlash('success', '<h5>Сохранено!</h5>');
				return $this->render('update', ['models' => $sk_ch, 'id' => $id, 'student' => $student, 
					'app_data' => $app_data, 'category' => $category, 'view_only' => $view_only, 'stage' => $stage]);
			}

			if ($status == 2) {
				$view_only = true;
			}

			return $this->render('update', ['models' => $sk_ch, 'id' => $id, 'student' => $student, 
				'app_data' => $app_data, 'category' => $category, 'view_only' => $view_only, 'stage' => $stage]);
		} else {
			return $this->redirect('?r=iomtest/index');
		}
	}

	public function actionFinish($id, $stage) {
		if (Yii::$app->user->isGuest) {
			return $this->goBack();
		}
		
		$user_id = Yii::$app->user->id;
		$app_data = StudentCheckByTeacher::find()->where(['user_id' => $user_id, 'students_id' => $id, 'stage'=>$stage]);
		$max = $app_data->max('id');
		$scbt = StudentCheckByTeacher::findOne($max);
		$scbt->status = 2;
		$scbt->date_finish = date('Y-m-d');
		$scbt->save(false);

		return $this->redirect('?r=iomtest/index');
	}
}