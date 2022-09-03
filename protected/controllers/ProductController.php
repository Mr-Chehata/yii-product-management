<?php

/* For now to Disable CORS */
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class ProductController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{

		return [];
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{

		return array(
			array(
				'allow',
				'actions' => array('index', 'list', 'delete', 'create', 'update'),
				'users' => array('*'),
			)

		);
	}

	
	/**
	 * Creates a new model.
	 */
	public function actionCreate()
	{
		$this->saveProduct(new Product, $_POST);
	}


	/**
	 * Updates a particular model.
	 */
	public function actionUpdate($id)
	{
		if($id && is_numeric($id)){
			$this->saveProduct($this->loadModel($id), $_POST);
			return $this->_sendResponse(200, CJSON::encode(['message' => 'Product updated.']));
		}
		throw new CHttpException(404, 'The product does not exist.');
		
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if($id && is_numeric($id)){
			$this->loadModel($id)->delete();
			return $this->_sendResponse(200, CJSON::encode(['message' => 'Product deleted.']));
		}
		throw new CHttpException(404, 'The product does not exist.');
		
	}


	/**
	 * Manages all models.
	 */
	public function actionList()
	{
		$datas = [];
		$list = [];
		
		if($_GET['searchKeyword']){/* check if there a query params of Serach*/
           //@todo filter products with search keyword
		}else{
			//get all
			$products = Product::model()->findAll();

		}
		if($_GET['currentPage'] && $_GET['pageSize']){/* check if there a query params of Pagination*/
			$datas['currentPage'] = $_GET['currentPage'];
			$datas['pageSize'] = $_GET['pageSize'];
			 //@todo get products with pagination
		}
		foreach ($products  as  $product) {
			$list[] = ['id' => $product->id, 'name' => $product->name, 'details' => $product->details];
		}
		$datas['list'] = $list;
		$datas['total'] = count($list);
        
		return $this->_sendResponse(200, CJSON::encode($datas));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Product the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Product::model()->findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}


	/**
	 *
	 * @param Product $product
	 * @param array $postRequest
	 * @return void
	 */
	private function saveProduct($product, $request)
	{

		foreach ($request as $var => $value) {
			// Does the model have this attribute? If not raise an error
			if ($product->hasAttribute($var))
				//validate the inputs?!
				$product->$var = $value;
		}
		if ($product->save())
			$this->_sendResponse(200, CJSON::encode($product));
		else {
			// Errors occurred
			$errors = [];
			foreach ($product->errors as $attribute => $attrErrors) {
				foreach ($attrErrors as $attrError) {
					$_attrErrors[] = $attrError;
				}
				$errors[$attribute] = $_attrErrors;
			}
			$this->_sendResponse(200, CJSON::encode(['errors'=>$errors]));
		}
	}

	/**
	 *
	 * @param integer $status
	 * @param string $body
	 * @param string $content_type
	 * @return void
	 */
	private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
		// set the status
		$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
		header($status_header);
		// and the content type
		header('Content-type: ' . $content_type);

		// pages with body are easy
		if ($body != '') {
			// send the body
			echo $body;
		}
		// we need to create the body if none is passed
		else {
			// create some body messages
			$message = '';

			// this is purely optional, but makes the pages a little nicer to read
			// for your users.  Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch ($status) {
				case 401:
					$message = 'You must be authorized to view this page.';
					break;
				case 404:
					$message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
					break;
				case 500:
					$message = 'The server encountered an error processing your request.';
					break;
				case 501:
					$message = 'The requested method is not implemented.';
					break;
			}

			// servers don't always have a signature turned on 
			// (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

			// this should be templated in a real-world solution
			$body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
    <hr />
    <address>' . $signature . '</address>
</body>
</html>';

			echo $body;
		}
		Yii::app()->end();
	}


	/**
	 * @param int $status
	 * @return void
	 */
	private function _getStatusCodeMessage($status)
	{
		$codes = array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}
}
