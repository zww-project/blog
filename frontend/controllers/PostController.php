<?php
namespace frontend\controllers;
/**
 * 文章控制器
 */
use frontend\models\PostForm;
use Yii;

class PostController extends BaseController
{
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
        return $this->render('create',['model'=>$model]);
    }
}

