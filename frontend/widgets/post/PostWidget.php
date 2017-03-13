<?php
namespace frontend\widgets\post;
/**
 * 文章列表组件
 */
use Yii;
use common\models\PostModel;
use frontend\models\PostForm;
use yii\bootstrap\Widget;
use yii\data\Pagination;
use yii\helpers\Url;

class PostWidget extends Widget
{
    /**
     * 文章列表标题
     * @var string
     */
    public $title = '';
    /**
     * 显示条数
     * @var int
     */
    public $pageSize = 6;
    /**
     * 是否显示更多
     * @var bool
     */
    public $more = true;
    /**
     * 是否显示分页
     * @var bool
     */
    public $page = false;
    public function run()
    {
        $curPage = Yii::$app->request->get('page',1); //当前页
        //查询条件
        $condition = ['=','is_valid',PostModel::IS_VALID];
        $res = PostForm::getList($condition,$curPage,$this->pageSize);
        $result['title'] = $this->title?:"最新文章";
        $result['more'] = Url::to(['post/index']);
        $result['body'] = $res['data']?:[];
        //是否分页
        if($this->page){
            $pages = new Pagination(['totalCount'=>$res['count'],'pageSize'=>$res['pageSize']]);
            $result['page'] = $pages;
        }
        return $this->render('index',['data'=>$result]);
    }
}