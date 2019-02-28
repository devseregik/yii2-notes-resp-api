<?php

namespace app\controllers;

use Yii;
use app\models\Note\Note;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * NoteController implements the CRUD actions for Note model.
 *
 * Notes REST API.
 *
 * @package app\controllers
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class NoteController extends ActiveController
{
    /**
     * @var int Notes per page by list.
     */
    const PER_PAGE = 5;


    /**
     * {@inheritdoc}
     */
    public $modelClass = Note::class;

    /**
     * {@inheritdoc}
     */
    public $updateScenario = Note::SCENARIO_SAVE;

    /**
     * {@inheritdoc}
     */
    public $createScenario = Note::SCENARIO_SAVE;


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['delete']);
        return $actions;
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'  => ['create', 'update', 'delete'],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     *
     * @param Note|null $model
     *
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        switch ($action) {
            case 'view':
                if ($model->isDeleted() || !$model->isActual()) {
                    throw new ForbiddenHttpException('You can only view notes that are not deleted and did published.');
                }
                break;

            case 'update':
            case 'delete':
                if ($model->user->id !== Yii::$app->user->id) {
                    throw new ForbiddenHttpException(sprintf('You can only %s notes that you\'ve created.', $action));
                }

                $relativeHours = round((time() - $model->created_at) / 3600, 1);
                if ($relativeHours > 24) {
                    throw new ForbiddenHttpException(sprintf('You can only %s notes that you\'ve created no more than 24 hours ago.', $action));
                }
                break;

            default:
        }
    }

    /**
     * Lists all Note models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Note::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'published_at' => SORT_DESC,
                    'created_at'   => SORT_ASC,
                ],
            ],
        ]);
        $dataProvider->pagination->pageSize = self::PER_PAGE;
        $dataProvider->pagination->pageParam = 'p';
        $dataProvider->pagination->validatePage = false;

        return [
            'count'       => $dataProvider->totalCount,
            'pageCount'   => $dataProvider->pagination->pageCount,
            'currentPage' => $dataProvider->pagination->page + 1,
            'notes'       => $dataProvider->getModels(),
        ];
    }

    /**
     * Displays a single Note model.
     *
     * @param integer $id [[Note]] id.
     *
     * @return Note
     *
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): Note
    {
        $model = $this->findModel($id);
        $this->checkAccess('view', $model);
        $model->setScenario(Note::SCENARIO_VIEW);
        return $model;
    }

    /**
     * Delete note.
     *
     * @param int $id [[Note]] id.
     *
     * @return Note
     *
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDelete(int $id): Note
    {
        $model = $this->findModel($id);
        $this->checkAccess('delete', $model);
        $model->softDelete();
        return $model;
    }
    
    /**
     * Finds the Note model based on its primary key value.
     *
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Note the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @see ActiveController::actions()
     */
    protected function findModel($id): Note
    {
        if (($model = Note::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
