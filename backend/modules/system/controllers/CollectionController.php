<?php

namespace backend\modules\system\controllers;

use Yii;
use common\models\Collection;
use common\models\CollectionData;
use backend\modules\system\models\search\CollectionSearch;
use backend\modules\system\models\search\CollectionDataSearch;
use backend\modules\system\models\search\CollectionHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use backend\models\site\MoviesSearcj;
use yii\db\Expression;

/**
 * CollectionController implements the CRUD actions for Collection model.
 */
class CollectionController extends Controller
{

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Collection models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CollectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Collection model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		# Get Collection Data
		$collection_data = CollectionData::find()->where([
			'collection_id' => $id
		])->all();

		# Get Movies and TV shows IDs for next search query
		$movie_ids = [];
		$show_ids = [];
		foreach($collection_data as $item){
			if($item['type'] == 'movie') {
				array_push($movie_ids, $item['movie_id']);
			} else if($item['type'] == 'show') {
				array_push($show_ids, $item['movie_id']);
			}
		}

		# Query collection data movies 
		$query_movie = (new \yii\db\Query())
			->select([
				'movies.id_movie as id', 
				'movies.title', 
				'movies.poster', 
				'movies.year', 
				'movies.imdb_id', 
				'movies.is_active', 
				'movies.date_added',
				new Expression($id . " AS collection_id"),
				'collection_data.position',
				new Expression("'movie' AS type"),
			])
			->from('movies')
			->where(['IN', 'id_movie', $movie_ids]);

		$query_movie->leftJoin('collection_data', 'collection_data.collection_id=collection_id  AND collection_data.imdb_id=movies.imdb_id');

		# Query Collections data TV Shows
		$query_show = (new \yii\db\Query())
			->select([
				'shows.id_show as id', 
				'shows.title', 
				'shows.poster', 
				'shows.year', 
				'shows.imdb_id', 
				'shows.is_active', 
				'shows.date_added',
				new Expression($id . " AS collection_id"),
				'collection_data.position',
				new Expression("'show' AS type"),
			])
			->from('shows')
			->where(['IN', 'id_show', $show_ids]);

		$query_show->leftJoin('collection_data', 'collection_data.collection_id=collection_id AND collection_data.imdb_id=Replace(shows.imdb_id, "tt", "")');

		# Union two tables
		$query_movie->union($query_show);

		$unionQuery = (new \yii\db\Query())
			->from(['items' => $query_movie])
			->orderBy([
				'position' => SORT_ASC
			]);

		# Generate data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
        ]);

        $dataProvider->pagination->pageSize = 5;

		# Get Collection Data History
        $searchHistoryModel = new CollectionHistorySearch();
		$filter = Yii::$app->request->queryParams;
		$filter['CollectionHistorySearch']['collection_id'] = $id;

        $dataHistoryProvider = $searchHistoryModel->search($filter);

        $dataHistoryProvider->pagination->pageSize = 5;

        $dataHistoryProvider->sort = [
            'defaultOrder' => ['id' => SORT_DESC]
        ];

        return $this->render('view', [
			'model' => $this->findModel($id),
			'dataProvider' => $dataProvider,
			'dataHistoryProvider' => $dataHistoryProvider,
			'searchHistoryModel' => $searchHistoryModel,
        ]);
    }

    /**
     * Creates a new Collection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Collection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->collection_id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Collection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->collection_id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Collection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Collection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Collection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Collection::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
