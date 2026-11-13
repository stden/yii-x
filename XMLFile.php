<?php
/**
 * Утилиты для работы с XML
 */
class XMLFile extends DOMDocument
{
	/**
	 * Конструктор
	 */
	public function __construct()
	{
		parent::__construct('1.0', 'utf-8');

		$this->formatOutput = true; // мы хотим красивый вывод
		$this->xmlStandalone = true; // standalone="yes"
	}

	/**
	 * @static Добавляем секцию текста
	 * @param $node DOMElement
	 * @param $tagName
	 * @param $text
	 */
	public function addText($node, $tagName, $text)
	{
		$element = $node->ownerDocument->createElement($tagName);
		$textNode = $node->ownerDocument->createTextNode($text);
		$element->appendChild($textNode);
		$node->appendChild($element);
	}

	/**
	 * @static Добавляем CData секцию
	 * @param $node DOMElement
	 * @param $tagName
	 * @param $text
	 */
	public function addCData($node, $tagName, $text)
	{
		$element = $node->ownerDocument->createElement($tagName);
		$CData = $node->ownerDocument->createCDATASection($text);
		$element->appendChild($CData);
		$node->appendChild($element);
	}
}
