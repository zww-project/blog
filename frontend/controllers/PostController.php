<?php
namespace frontend\controllers;
/**
 * 文章控制器
 */
use Yii;
use common\models\CatModel;
use frontend\models\PostForm;
use common\widgets\file_upload;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\PostExtendModel;

class PostController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create','upload','ueditor','view'],
                'rules' => [
                    [
                        'actions' => ['index','view'], //登录不登录都能访问
                        'allow' => true,
                        //'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','upload','ueditor'], //登录之后才能访问
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*'=> ['get','post'], //设置方法是用post还是用get访问 或者两种都有
                   // 'create' => ['get','post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'upload'=>[
                'class' => 'common\widgets\file_upload\UploadAction',  //这里扩展地址别写错
                'config' => [
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}",
                ]
            ],
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }

    /**
     * 文章列表
     * zww
     * 2017-1-7
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /**
     * 创建文章
     * zww
     * 2017-1-7
     */
    public function actionCreate()
    {
        $model = new PostForm();
        //定义场景
        $model->setScenario(PostForm::SCENARIOS_CREATE);
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            if(!$model->create()){
                Yii::$app->session->setFlash('warning',$model->_lastError);
            }else{
                return $this->redirect(['post/view','id'=>$model->id]);
            }

        }
        //获取所有分类
        $cat = CatModel::getAllCats();
        return $this->render('create',['model'=>$model,'cat'=>$cat]);
    }

    /**
     * 文章详情
     */
    public function actionView()
    {
        $id = (int)YII::$app->request->get('id');
        $model = new PostForm();
        $data = $model->getViewById($id);

        //文章统计
        $model = new PostExtendModel();
        $model->upCounter(['post_id'=>$id],'browser',1);
        return $this->render('view',['data'=>$data]);
    }
}

