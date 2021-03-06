<?php
namespace frontend\models;

use yii;
use common\models\RelationPostTags;
use common\models\RelationPostTagsModel;
use yii\base\Model;
use common\models\PostModel;
use yii\db\Query;
use yii\web\NotFoundHttpException;
/**
 * 文章表单模型
 * User: zww
 * Date: 2017/1/7
 */
class PostForm extends Model
{
    public $id;
    public $title;
    public $content;
    public $label_img;
    public $cat_id;
    public $tags;
    public $_lastError = "";

    /**
     * 定义场景
     * SCENARIOS_CREATE 创建
     * SCENARIOS_UPDATE 更新
     */
    const SCENARIOS_CREATE = 'create';
    const SCENARIOS_UPDATE = 'update';

    /**
     * 定义事件
     *
     */
    const EVENT_AFTER_CREATE = 'eventAfterCreate';
    const EVENT_AFTER_UPDATE = 'eventAfterUpdate';

    /**
     * 场景设置
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIOS_CREATE=>['title','content','label_img','cat_id','tags'],
            self::SCENARIOS_UPDATE=>['title','content','label_img','cat_id','tags'],
        ];
        return array_merge(parent::scenarios(),$scenarios);
    }

    public function rules()
    {
        return[
            [['id','title','cat_id'],'required'],
            [['id','cat_id'],'integer'],
            ['title','string','min'=>4,'max'=>50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'=>'编码',
            'title'=>'标题',
            'content'=>'内容',
            'label_img'=>'标签图',
            'tags'=>'标签',
            'cat_id'=>'分类',
        ];
    }

    /**
     * 创建文章
     * @return bool
     * @throws yii\db\Exception
     */
    public function create()
    {
        //事务
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $postModel = new PostModel();
            $postModel->setAttributes($this->attributes);
            $postModel->summary = $this->_getSummary();
            $postModel->user_id = Yii::$app->user->identity->id;
            $postModel->user_name = Yii::$app->user->identity->username;
            $postModel->is_valid = PostModel::IS_VALID;
            $postModel->created_at = time();
            $postModel->updated_at = time();
            if(!$postModel->save())
                throw new \Exception('文章创建失败！');
            $this->id = $postModel->id;
            //调用事件--创建文章之后的操作
            $data = array_merge($this->getAttributes());
            $this->_eventAfterCreate($data);
            $transaction->commit();
            return true;
        }catch (\Exception $e){
            $transaction->rollBack();
            $this->_lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * 文章列表
     * @param $condition
     * @param int $curPage
     * @param int $pageSize
     * @param array $orderBy
     * @return array
     */
    public static function getList($condition,$curPage = 1,$pageSize = 5,$orderBy = ['id'=> SORT_DESC])
    {
        $model = new PostModel();
        //查询语句
        $select = ['id','title','summary','label_img','cat_id','user_id','user_name','is_valid','created_at','updated_at'];
        $query = $model->find()
            ->select($select)
            ->where($condition)
            ->with('relate.tag','extend')
            ->orderBy($orderBy);
        //获取分页数据
        $res = $model->getPages($query,$curPage,$pageSize);
        //格式化
        $res['data'] = self::_formatList($res['data']);
        return $res;
    }

    /**
     * 数据格式化
     * @param $data
     * @return mixed
     */
    public static function _formatList($data)
    {
        foreach($data as &$list){
            $list['tags'] = [];
            if(isset($list['relate']) && !empty($list['relate'])){
                foreach($list['relate'] as $lt){
                    $list['tags'][] = $lt['tag']['tag_name'];
                }
            }
            unset($list['relate']);
        }
        return $data;
    }

    /**
     * 文章详情
     * @param $id
     * @return array|null|yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function getViewById($id)
    {
        $res = PostModel::find()->with('relate.tag','extend')->where(['id'=>$id])->asArray()->one();
        //处理标签格式
        $res['tags'] = [];
        if(isset($res['relate']) && !empty($res['relate'])){
            foreach($res['relate'] as $list){
                $res['tags'][] = $list['tag']['tag_name'];
            }
        }
        unset($res['relate']);
        if(!$res){
            throw new NotFoundHttpException("文章不存在！");
        }
        return $res;
    }

    /**
     * 截取文章摘要
     * @param int $s
     * @param int $e
     * @param string $char
     * @return null|string
     */
    private function _getSummary($s = 0,$e = 90,$char = 'utf-8')
    {
        if(empty($this->content))
            return null;

        return (mb_substr(str_replace('&nbsp;','',strip_tags($this->content)),$s,$e,$char));
    }

    /**
     * 文章创建完成之后的操作
     */
    public function _eventAfterCreate($data)
    {
        //添加事件
        $this->on(self::EVENT_AFTER_CREATE,[$this,'_eventAddTag'],$data);

        //触发事件
        $this->trigger(self::EVENT_AFTER_CREATE);

    }

    //添加标签
    public function _eventAddTag($event)
    {
        //保存标签
        $tag = new TagForm();
        $tag->tags = $event->data['tags'];
        $tagids = $tag->saveTags();

        //删除原先的关联关系
        RelationPostTagsModel::deleteAll(['post_id'=>$event->data['id']]);

        //批量保存文章和标签的关联关系
        if(!empty($tagids)){
            foreach($tagids as $k => $id){
                $row[$k]['post_id'] = $this->id;
                $row[$k]['tag_id'] = $id;
            }
            //批量插入
            $res = (new Query())->createCommand()
                ->batchInsert(RelationPostTagsModel::tableName(),['post_id','tag_id'],$row)
                ->execute();
            if(!$res){
                throw new \Exception("添加文章标签失败");
            }
        }
    }
}
