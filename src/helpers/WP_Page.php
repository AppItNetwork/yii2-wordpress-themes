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
		if (is_array($post)) {
			foreach ( $post as $key => $value ) {
				if (isset($this->$key)) {
					$this->$key = $value;
				}
			}
		} else {
			$this->post_type = 'page';
			$this->post_status = 'publish';
			$this->post_title = ucwords($post);
			$this->guid = Url::to(['site/'.strtolower($post)], true);
		}
	}

}
