<?php
//C:\Web\www\iom.localhost\controllers
namespace app\controllers;



//include("..\rtf\class_rtf.php");
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
use yii\helpers\Url;

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
        $appointments = StudentCheckByTeacher::find()->orderBy('stage')->all();
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
                //$sk->status = 1;
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

    public function actionPrint($id, $stage = null) {
        if ($stage == null)
            return $this->goBack();
            // $scbt = StudentCheckByTeacher::find()
            //     ->where(['students_id' => $id])
            //     ->indexBy('id')
            //     ->all();
        else
            $scbt = StudentCheckByTeacher::find()
                ->where(['students_id' => $id, 'stage' => $stage])
                ->indexBy('id')
                ->all();
        
        if (!User::isUserAdmin(Yii::$app->user->id) || $scbt == null) {
            return $this->goBack();
        }

        $data = [];
        
        $string = '<style>p {margin: 0 0;}</style>';
        $string = $this->getGeneratedData($id, $scbt, $data);

        return $this->renderPartial('print', [
            'string' => $string,
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

    public function actionDownload($id, $stage)
    {
        if ($stage == null)
            return $this->goBack();
            // $scbt = StudentCheckByTeacher::find()
            //     ->where(['students_id' => $id])
            //     ->indexBy('id')
            //     ->all();
        else
            $scbt = StudentCheckByTeacher::find()
                ->where(['students_id' => $id, 'stage' => $stage])
                ->indexBy('id')
                ->all();
        
        if (!User::isUserAdmin(Yii::$app->user->id) || $scbt == null) {
            return $this->goBack();
        }

        $data = [];
        
        $string = $this->getGeneratedData($id, $scbt, $data, true);

        //echo Yii::$app->basePath;
        $url = Yii::$app->basePath.'\rtf\PHPRtfLite.php';
        require_once($url);
        \PHPRtfLite::registerAutoloader();
        $rtf = new \PHPRtfLite();
        $sect = $rtf->addSection();
        $tmp = strip_tags($string, '<br>');
        $sect->writeText($tmp, new \PHPRtfLite_Font(14, 'Times new Roman'), 
                new \PHPRtfLite_ParFormat(\PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT), true);
        //$headers = Yii::$app->response->headers;
        //$headers->add('Content-Type', 'application/rtf');
        //header('Content-Description: File Transfer');
        //header('Content-Type', 'application/rtf');
        //$rtf->sendRtf('simple.rtf');
        $path = Url::base().$data['student']->name.', '.$data['student']->class.', этап '.$stage.'.rtf';
        $rtf->save($path);
        Yii::$app->response->sendFile($path);
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

    function getGeneratedData($id, &$scbt, &$data, $rtf = false) {
        $ids = [];
        $attempts = [];
        foreach ($scbt as $key => $s) {
            $ids[] = $s->user_id;
            $attempts = $s->id;
        }

        $data['teachers'] = User::find()->where(['in', 'id', $ids])->all();
        foreach ($data['teachers'] as $key => $t) {
            $ids[] = $t->groups;
        }
        $data['groups'] = Groups::find()->where(['in', 'id', $ids])->all();

        $data['student'] = Students::findOne($id);
        //$data['teachers'] = User::find()
            // ->select('*')
            // ->all();
        $data['skills_ch'] = SkillsChecked::find()->where(['students_id' => $id])->where(['in', 'attempt', $attempts])->orderBy('id')->all();
        $data['skills'] = Skills::find()->all();
        $data['dev_dir_id'] = DevDir::find()->all();

        // Отсюда был view print
        $resources = [];
        $deficit = [];
        $sk_ready_id = [];

        for ($i=0; $i < count($data['skills_ch']); $i++) { 
            //echo $data['skills_ch'][$i]->skills_id;
            if (!in_array($data['skills_ch'][$i]->skills_id, $sk_ready_id)) {
                $sum = 0;
                $sum = $data['skills_ch'][$i]->value;
                $k = 1;
                for ($j=$i+1; $j < count($data['skills_ch'])-1; $j++) { 

                    if ($data['skills_ch'][$i]->skills_id == $data['skills_ch'][$j]->skills_id) {
                        $sum = $sum + $data['skills_ch'][$j]->value;
                        $k++;
                    }
                }
                $sk_ready_id[] = $data['skills_ch'][$i]->skills_id;
                if ($k != 0)
                    if ($sum/$k > 1.5)
                        $resources[] = ['id' => $data['skills_ch'][$i]->skills_id, 'value' => $sum/$k];
                    else
                        $deficit[] = ['id' => $data['skills_ch'][$i]->skills_id, 'value' => $sum/$k];
            }
        }


        $mysuperpuperstring = '<h2 align="center">Индивидуальный образовательный маршрут на обучающую(его)ся: '.$data['student']->name.', ';
        $date = new \DateTime($data['student']->birthday);
        $mysuperpuperstring .= $date->format('d.m.Y').'г.р., '.$data['student']->class.' класса с ОВЗ на 2020-2021 учебный год</h2>'.($rtf ? '<br>' : '').($rtf ? '<br>' : '').'<h3>Ресурсы ребёнка (хорошо сформированные навыки):</h3>'.($rtf ? '<br>' : '').'<p>';
        foreach ($data['dev_dir_id'] as $dev) {
            $k = 0;
            $super = '';
            foreach ($resources as $key => $res) {
                foreach ($data['skills'] as $skill) {
                    if ($res['id'] == $skill->id && $dev->id == $skill->dev_dir_id) {
                        $super = $super.($k+1).'. '.$skill->skill_name.'<br>';
                        $k++;
                    }
                }
                //echo '<br>'; //': '.round($res['value'], 2).'<br>';
            }
            if ($k > 0)
                $mysuperpuperstring .= ($rtf ? '<br>' : '').'<h4>'.$dev->name.'</h4>'.($rtf ? '<br>' : '').$super;
        }
        $mysuperpuperstring .= '</p>'.($rtf ? '<br>' : '').'<h3>Дефициты ребёнка (навык отсутствует или находится в стадии формирования):</h3>'.($rtf ? '<br>' : '').'<p>';
        foreach ($data['dev_dir_id'] as $dev) {
            $k = 0;
            $super = '';
            foreach ($deficit as $key => $def) {
                foreach ($data['skills'] as $skill) {
                    if ($def['id'] == $skill->id && $dev->id == $skill->dev_dir_id) {
                        $super = $super.($k+1).'. '.$skill->skill_name.'<br>';
                        $k++;
                    }
                }
                //echo '<br>'; //': '.round($res['value'], 2).'<br>';
            }
            if ($k > 0)
                $mysuperpuperstring .= ($rtf ? '<br>' : '').'<h4>'.$dev->name.'</h4>'.($rtf ? '<br>' : '').$super;
        }
        $mysuperpuperstring .= '</p>'.($rtf ? '<br>' : '').'<h3>Цель:</h3> сохранение, улучшение и корректировка психологического, социального и  физического здоровья, сопровождение психофизического развития школьника, психолого-педагогическое обеспечение дифференцированного и индивидуального подхода к обучающемуся.</p>'.($rtf ? '<br>' : '').'<h3>Задачи развития ребёнка на учебный год:</h3>'.($rtf ? '<br>' : '').'<p>';
        $dev_dirs = [];
            foreach ($deficit as $key => $def) {
                foreach ($data['skills'] as $skill) {
                    if ($def['id'] == $skill->id)
                        foreach ($data['dev_dir_id'] as $dev) {
                            if ($skill->dev_dir_id == $dev->id && !in_array($dev, $dev_dirs)) {
                                $mysuperpuperstring .= $dev->task.'<br>';
                                $dev_dirs[] = $dev;
                            }
                        }
                }
                
                //echo $def['id'].' ;; '.round($def['value'], 2).'<br>';
            }
        $mysuperpuperstring .= '</p>'.($rtf ? '<br>' : '').'<h3>Рекомендации педагогическим работникам и родителям:</h3>'.($rtf ? '<br>' : '').'<p>';
        $k = 1;
        $recomendation = [];
            foreach ($deficit as $key => $def) {
                foreach ($data['skills'] as $skill) {
                    if ($def['id'] == $skill->id && !in_array($skill->recomendation, $recomendation))
                        {
                            $mysuperpuperstring .= $k.'. '.$skill->recomendation.'<br>';
                            $recomendation[] = $skill->recomendation;
                            $k++;
                        }
                }
                
                //echo $def['id'].' ;; '.round($def['value'], 2).'<br>';
            }
        $mysuperpuperstring .= '</p>
        <p>&nbsp;</p><table width="100%">
        <tr><td width="50%" valign="bottom"><p>Дата составления: '.date('d.m.y').'</p>
        </td>
        <td width="50%">
        <p align="right">Председатель ППК ГБОУ Школа №121 ________ Е.А.Ефремова<br>
        <!-- <Педагог-психолог><br>
        <Учитель-логопед><br>
        <Учитель-дефектолог><br>
        <Социальный педагог><br>
        <Учитель><br>
        <Воспитатель> -->';

        foreach ($data['groups'] as $key => $g) {
            foreach ($data['teachers'] as $key => $t) {
                if ($g->id == $t->groups)
                    $mysuperpuperstring .= $g->name.': '.$t->name.'<br>';
            }
        }
        $mysuperpuperstring .= '</p>
        </td></tr></table>';
        return $mysuperpuperstring;
    }
}
