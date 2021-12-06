<?php

use yii\db\Migration;

/**
 * Class m190506_145851_add_moderation_draft_active_field
 */
class m190506_145851_add_moderation_draft_active_field extends Migration
{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp()
		{

			$this->addColumn('moderation_draft', 'is_active', 'smallint NOT NULL default 0');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown()
		{

			$this->dropColumn('moderation_draft', 'is_active');

		}

	}
