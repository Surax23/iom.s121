<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Students;
use app\models\Skills;
use app\models\SkillsChecked;
use app\models\Age;
use yii\bootstrap\Button;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class IomController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goBack();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Students::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSkills($id)
    {
    	if (Yii::$app->user->isGuest) {
            return $this->goBack();
        }

        $skills = Skills::find()->indexBy('id')->all();

        if (SkillsChecked::loadMultiple($skills, Yii::$app->request->post()) && SkillsChecked::validateMultiple($skills)) {
            foreach ($skills as $skill) {
                $skill->save(false);
            }
            return $this->redirect('index');
        }

        return $this->render('skills', ['skills' => $skills]);


        //$dataProvider = new ActiveDataProvider([
        //    'query' => Skills::find(),
        //]);

        //return $this->render('skills', [
        //    'dataProvider' => $dataProvider,
        //]);
    }

    public function actionFform()
    {
        $user_id = Yii::$app->user->id;
        $request = Yii::$app->request;
        $student_id = $request->get('id');
        $age_id = Students::find()->select('age_id')->where(['students_id' => $student_id]);
        $age = Age::find()->select('*')->where(['id' => $age_id]);
        $sk_ch = SkillsChecked::find()->select('*')->where(['students_id' => $student_id, 'user_id' => $user_id])->all();
        $sk_ch_count = count($sk_ch);
        $skills = Skills::find()->select('*')->where([])->all();

        if (SkillsChecked::loadMultiple($sk_ch, Yii::$app->request->post()) && SkillsChecked::validateMultiple($sk_ch)) {
                foreach ($sk_ch as $sk) {
                    $sk->save(false);
                }
                return $this->redirect('iom');
        }

        if ($sk_ch_count == 0) {
            $sk_ch = null;
            $i = 0;
            foreach ($skills as $skill) {
                $sk_ch[] = new SkillsChecked();
                $sk_ch[$i]->students_id = $student_id;
                $sk_ch[$i]->user_id = $user_id;
                $sk_ch[$i]->skills_id = $skill->id;
                $i++;
            }
        } else {}

        return $this->render('_fform', [
            'model' => $sk_ch,
            //'user_id' => $user_id,
            //'student_id' => $student_id,
            'skills' => $skills,
            'student_id' => $student_id,
        ]);
    }

    public function actionUpdate()
    {
        $sk_ch = SkillsChecked::find()->indexBy('id')->all();

        if (Model::loadMultiple($sk_ch, Yii::$app->request->post()) && Model::validateMultiple($sk_ch)) {
            foreach ($sk_ch as $sk) {
                $sk->save(false);
            }
            return $this->redirect('index');
        }

        return $this->render('_fform', ['settings' => $sk_ch]);
    }

}
