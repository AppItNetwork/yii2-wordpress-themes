<?php

namespace appitnetwork\wpthemes\helpers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

// class WP extends Application
class WP_Page extends WP_Post
{

	public function initEmptyObject( $post = null ) {
		// pr(Yii::$app->view->content);die;
		if (is_array($post)) {
			foreach ( $post as $key => $value ) {
				if (isset($this->$key)) {
					$this->$key = $value;
				}
			}
		} else {
			$this->post_type = 'page';
			$this->post_status = 'publish';
			$this->post_title = ( !empty(Yii::$app->view->title) ) ? Yii::$app->view->title : ucwords($post);
			$this->guid = Url::to(['site/'.strtolower($post)], true);
			// $this->post_content = Yii::$app->view->content;
		}
	}

}
