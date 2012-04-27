<?php
/**
 *  Отправка почты
 */
class Mailer
{

	static function send_mime_mail($name_from, // имя отправителя
	                               $email_from, // email отправителя
	                               $name_to, // имя получателя
	                               $email_to, // email получателя
	                               $data_charset, // кодировка переданных данных
	                               $send_charset, // кодировка письма
	                               $subject, // тема письма
	                               $body, // текст письма
	                               $html = FALSE // письмо в виде html или обычного текста
	)
	{
		$to = self::mime_header_encode($name_to, $data_charset, $send_charset)
			. ' <' . $email_to . '>';
		$subject = self::mime_header_encode($subject, $data_charset, $send_charset);
		$from = self::mime_header_encode($name_from, $data_charset, $send_charset)
			. ' <' . $email_from . '>';
		if ($data_charset != $send_charset) {
			$body = iconv($data_charset, $send_charset, $body);
		}
		$headers = "From: $from\r\n";
		$type = ($html) ? 'html' : 'plain';
		$headers .= "Content-type: text/$type; charset=$send_charset\r\n";
		$headers .= "Mime-Version: 1.0\r\n";

		return mail($to, $subject, $body, $headers);
	}

	static function mime_header_encode($str, $data_charset, $send_charset)
	{
		if ($data_charset != $send_charset) {
			$str = iconv($data_charset, $send_charset, $str);
		}
		return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
	}

	/**
	 * @static
	 * @param $name_to
	 * @param $email_to E-mail куда отправить
	 * @param $subject
	 * @param $email_text
	 * @return void
	 */
	public static function Send($name_to, $email_to, $subject, $email_text)
	{
		self::send_mime_mail('office.vasanta.biz',
			'vasanta.spb@gmail.com',
			$name_to,
			$email_to,
			'utf-8', // кодировка, в которой находятся передаваемые строки
			'utf-8', // кодировка, в которой будет отправлено письмо
			$subject,
			$email_text);
	}

}
