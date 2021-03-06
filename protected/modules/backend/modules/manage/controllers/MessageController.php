<?php
/**
 * 
 * @author xia.q
 *
 */
class MessageController extends TBackendController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';
	
	/**
	 * 每一个子controller都是一个操作的开始，这里创建一个操作权限
	 * @return multitype:string
	 */
	public static function getRbacConf()
	{
		return array(
				'admin'=>'Admin Message Operation',
				'update'=>'Update Message Operation',
				'batchUpdate'=>'BatchUpdate Message Operation',
				);
	}
	
	public function actionUpdate()
	{
		$id = Yii::app()->request->getParam('id');//getQuery是取get，getParam则优先取get再取post
		$language = Yii::app()->request->getParam('language');
		$translation = Yii::app()->request->getParam('translation');
		
		$line = Message::model()->updateAll(array('translation'=>$translation), 'id=:id AND language=:language', array('id'=>$id,'language'=>$language));
		
		$result = array(
				'status'=>$line?'1':'0',
				'message'=>$line?'':'失败>_<',
				);
		echo CJSON::encode($result);
		Yii::app()->end();
	}
	
	/**
	 * 复合主键删除
	 * 
	 */
	public function actionDelete()
	{
		$id = Yii::app()->request->getParam('id');
		$language = $id['language'];
		$id = $id['id'];
		
		$model = new Message();
		$model->find('id=:id and language=:language', array(':id'=>$id, ':language'=>$language))->delete();
		
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		//以上功能是，删除之后并以php的方式跳转到一个ajax指定的路径
	}
	
	/**
	 * Updates a particular model.
	 * 单条更新
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 * 复合主键
	 * ['id'] =>67
	 * ['language'] =>'zh_cn'
	 */
	public function actionBatchUpdate()
	{
		$keys = Yii::app()->request->getParam('id', array());
		if($keys) {
			$ids = array();
			$language = '';
			foreach ($keys as $key) {
				$ids[] = array_shift(explode(',', $key));
				empty($language)?($language = array_pop(explode(',', $key))):'';
			}
			$criteria = new CDbCriteria;
			$criteria->addInCondition('t.id', $ids);//此id是外键
			$criteria->addCondition('language=\''.$language.'\'');
			$models = Message::model()->with('source')->findAll($criteria);
			
			//加载表单模板
			$this->render('batchUpdate',array(
				'models'=>$models,
			));
		} else {
			
			//提示并返回到admin
			$this->redirect(array('admin'));
		}
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Message('search');
		$model->unsetAttributes();  // clear any default values

		if(isset($_GET['Message'])) {
			$model->attributes = $_GET['Message'];
			$user = Yii::app()->request->getQuery('Message', array());
			$model->keyword = empty($user['keyword'])?'':trim($user['keyword']);
			$model->languageCode = empty($user['languageCode'])?Yii::app()->language:trim($user['languageCode']);
		}

		//这里要判断ajax
		if(!isset($_GET['ajax'])) //grid默认是get提交数据
			$this->render('admin',array('model'=>$model,));
		else  
			$this->renderPartial('admin',array('model'=>$model,));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return SourceMessage the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=SourceMessage::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param SourceSourceMessage $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='message-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
