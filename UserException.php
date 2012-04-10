<?php

/**
 * Ошибка которую надо показать пользователю
 */
class UserException extends CException
{
    /**
     * Создание исключения.
     * Пример: throw new UserException('Wrong {id}', array('id' => $id));
     * @param string $message Сообщение об ощибке
     * @param array|int $params Параметры сообщения для подстановки
     */
    public function __construct($message, $params = array())
    {
        // Переводим сообщение об ошибке на текущий язык
        parent::__construct(Yii::t('UserException.error', $message, $params), 0);
    }
}

