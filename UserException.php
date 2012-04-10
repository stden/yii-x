<?php

/**
 * Ошибка которую надо показать пользователю
 */
class UserException extends CException
{
    /**
     * Constructor.
     * @param string $message Сообщение об ощибке
     * @param array|int $params Параметры сообщения
     */
    public function __construct($message, $params = array())
    {
        // Переводим сообщение об ошибке на текущий язык
        parent::__construct(Yii::t('error', $message, $params), 0);
    }
}

