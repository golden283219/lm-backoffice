<?php


namespace common\helpers;


class Html extends \yii\helpers\BaseHtml
{
    public static function dropdownWithButton($options)
    {
        $contents = self::a($options['text'], $options['href'], $options['options']);
        $contents .= self::button('<span class="caret"></span>'.'<span class="sr-only">Toggle Dropdown</span>', [
            'type' => 'button',
            'class' => 'btn btn-default dropdown-toggle',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false'
        ]);

        $contents .= self::tag('ul', (function ($items) {
            $html = '';
            foreach ($items as $item) {
                if (!$item['visible']) continue;

                $html .= '<li>' . self::tag('a', $item['text'], $item['options']) . '</li>';
            }
            return $html;
        })($options['items']), [
            'class' => 'dropdown-menu'
        ]);

        return '<div class="btn-group">' . $contents . '</div>';
    }
}
