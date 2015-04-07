<?php
/**
 * 
 * @author xia.q
 *
 */
class UserController extends TBackendController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes = $_POST['User'];
			$model->login_ip = '';
			$model->date_added = time();
			
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		//以上功能是，删除之后并以php的方式跳转到一个ajax指定的路径
	}
	
	/**
	 * 批量删除
	 * @param array $ids
	 * @return boolean
	 */
	public function actionBatchDelete()
	{
		if(isset($_POST[$this->id.'-grid_c0'])) {
			$ids = $_POST[$this->id.'-grid_c0'];
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id', $ids);
			if(User::model()->deleteAll($criteria) > 0)
				return true;
			else
				return false;
		}
		
// 		if(!isset($_GET['ajax']))
// 			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	
	/**
	 * 批量修改状态
	 * @param array $ids
	 * @return boolean
	 */
	public function actionBatchStatus()
	{
		if(isset($_POST[$this->id.'-grid_c0'])) {
			$ids = $_POST[$this->id.'-grid_c0'];
			fb($ids);
			$status = 0;
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id', $ids);
			if(User::model()->updateAll(array('status'=>$status), $criteria) > 0)
				return true;
			else
				return false;
		}
	
		// 		if(!isset($_GET['ajax']))
			// 			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		
// 		$model->with('user_group')->findAll();
		
		if(isset($_GET['User'])) {
			$model->attributes = $_GET['User'];
			$user = Yii::app()->request->getQuery('User', array());//get
			$model->keyword = empty($user['keyword'])?'':$user['keyword'];
			//Yii::app()->request->getParam('keyword', '');//get or post
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
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}