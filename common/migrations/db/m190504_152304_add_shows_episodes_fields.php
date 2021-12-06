<?php

use yii\db\Migration;

/**
 * Class m190504_152304_add_shows_episodes_fields
 */
class m190504_152304_add_shows_episodes_fields extends Migration
{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp()
		{

			$this->addColumn('shows_episodes_subtitles', 'is_approved', 'smallint NOT NULL default 0');
			$this->addColumn('shows_episodes_subtitles', 'is_moderated', 'smallint NOT NULL default 1');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown()
		{
			$this->dropColumn('shows_episodes_subtitles', 'is_approved');
			$this->dropColumn('shows_episodes_subtitles', 'is_moderated');
		}
		
	}
