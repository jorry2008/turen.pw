<?php
/**
 * 
 * @author xia.q
 *
 */
class CommonController extends TBackendController
{
	//登录专用布局
	public $layout = '//backend/layouts/column-login';
	
	/**
	 * (non-PHPdoc)
	 * @see CController::filters()
	 */
	public function filters()
	{
		//return array();
		//return array('a'=>'b');
	}

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
// 	public function actionError()
// 	{
// 	    if($error=Yii::app()->errorHandler->error)
// 	    {
// 	    	if(Yii::app()->request->isAjaxRequest)
// 	    		echo $error['message'];
// 	    	else
// 	        	$this->render('error', $error);
// 	    }
// 	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");

		$model = new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			//登录或者无权时将当前访问的url记录下来
			//等验证通过后恢复即可
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		
		fb($this->layout);
		
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		//$this->redirect(Yii::app()->user->l);
	}
}