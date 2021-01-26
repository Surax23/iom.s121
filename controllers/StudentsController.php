<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Students;
use app\models\StudentsSearch;
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

/**
 * StudentsController implements the CRUD actions for Students model.
 */
class StudentsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Students models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Students::find(),
        ]);

        //$searchModel = new StudentsSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $user_id = Yii::$app->user->id;
        $appointments = StudentCheckByTeacher::find()->all();
        $users = User::find()->all();

        //Yii::$app->session->setFlash('success', '<h5>Класс!</h5>');
        return $this->render('index', [
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'appointments' => $appointments,
            'users' => $users,
            'sort' => ['attributes' => ['name']],
        ]);
    }

    public function actionEdit($id)
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

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
            for ($i=1; $i <= 3; $i++) { 
                foreach ($groups as $g) {
                    $scbt[] = new StudentCheckByTeacher();
                    $key = array_key_last($scbt);
                    $scbt[$key]->students_id = $id;
                    $scbt[$key]->status = 0;
                    $scbt[$key]->group_id = $g;
                    $scbt[$key]->stage = $i;
                    $scbt[$key]->save(false);
                }
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
        $ids = [];
        foreach ($scbt as $key => $s) {
            $ids[] = $s->user_id;
        }


        $data['teachers'] = User::find()->where(['in', 'id', $ids])->all();
        foreach ($data['teachers'] as $key => $t) {
            $ids[] = $t->groups;
        }
        $data['groups'] = Groups::find()->where(['in', 'id', $ids])->all();

        if (!User::isUserAdmin(Yii::$app->user->id) || $scbt == null) {
            return $this->goBack();
        }

        $data['student'] = Students::findOne($id);
        //$data['teachers'] = User::find()
            // ->select('*')
            // ->all();
        $data['skills_ch'] = SkillsChecked::find()->where(['students_id' => $id])->all();
        $data['skills'] = Skills::find()->all();
        $data['dev_dir_id'] = DevDir::find()->all();


        return $this->renderPartial('print', [
            'scbt' => $scbt,
            'data' => $data,
        ]);
    }

    /**
     * Displays a single Students model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Students model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

        $model = new Students();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Students model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Students model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionClear($id)
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }

        $skills_ch = SkillsChecked::find()->where(['students_id' => $id])->all();
        foreach ($skills_ch as $key => $s) {
            $s->delete();
        }

        $scbt = StudentCheckByTeacher::find()
            ->where(['students_id' => $id])->all();
        foreach ($scbt as $key => $s) {
            $s->delete();
        }

        Yii::$app->session->setFlash('success', '<h5>Назначения очищены!</h5>');
        return $this->redirect('?r=students/index');
    }

    /**
     * Finds the Students model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Students the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (!User::isUserAdmin(Yii::$app->user->id)) {
            return $this->goBack();
        }
        
        if (($model = Students::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
