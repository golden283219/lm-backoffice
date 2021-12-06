<?php


namespace backend\models;


class PremPlans extends \common\models\PremPlans
{
    public static function allPlans()
    {
        $plans = [];
        $plans_models = self::find()->where(['is_active' => 1])->all();

        foreach ($plans_models as $plan_model) {
            $plans[$plan_model->id] = $plan_model->title;
        }

        return $plans;
    }
}
