<?php

/**
 * Errbit PHP Notifier.
 *
 * Copyright © Flippa.com Pty. Ltd.
 * See the LICENSE file for details.
 */

/**
 * Like Nokogiri, but shittier.
 *
 * SimpleXML's addChild() and friends don't escape the XML, so this wraps
 * simplexml in a very specific way.
 *
 * Lambdas are used to construct a full tree of XML.
 *
 * @example
 *
 *   $builder = new Errbit_XmlBuilder();
 *   $builder->tag('product', function($product) {
 *     $product->tag('name', 'Plush Puppy Toy');
 *     $product->tag('price', '$8', array('currency' => 'USD'));
 *     $product->tag('discount', function($discount) {
 *       $discount->tag('percent', 20);
 *       $discount->tag('name',    '20% off promotion');
 *     });
 *   })
 *   ->asXml();
 */
class Errbit_XmlBuilder {
	/**
	 * Instantiate a new XmlBuilder.
	 *
	 * @param [SimpleXMLElement] $xml
	 *   the parent node (only used internally)
	 */
	public function __construct($xml = null) {
		$this->_xml = $xml ? $xml : new SimpleXMLElement('<__ErrbitXMLBuilder__/>');
	}

	/**
	 * Insert a tag into the XML.
	 *
	 * @param [String] $name
	 *   the name of the tag, required.
	 *
	 * @param [String] $value
	 *   the text value of the element, optional
	 *
	 * @param [Array] $attributes
	 *   an array of attributes for the tag, optional
	 *
	 * @param [Callable] $callback
	 *   a callback to receive an XmlBuilder for the new tag, optional
	 *
	 * @return [XmlBuilder]
	 *   a builder for the inserted tag
	 */
	public function tag($name /* , $value, $attributes, $callback */) {
		$value      = '';
		$attributes = array();
		$callback   = null;
		$idx        = count($this->_xml->$name);
		$args       = func_get_args();

		array_shift($args);
		foreach ($args as $arg) {
			if (is_string($arg)) {
				$value = $arg;
			} elseif (is_callable($arg)) {
				$callback = $arg;
			} elseif (is_array($arg)) {
				$attributes = $arg;
			}
		}

		$this->_xml->{$name}[$idx] = $value;

		foreach ($attributes as $attr => $v) {
			$this->_xml->{$name}[$idx][$attr] = $v;
		}

		// FIXME: This isn't the last child, it's the first, it just doesn't happen to matter in this project
		$node = new self($this->_xml->$name);

		if ($callback) {
			$callback($node);
		}

		return $node;
	}

	/**
	 * Add an attribute to the current element.
	 *
	 * @param [String] $name
	 *   the name of the attribute
	 *
	 * @param [String] $value
	 *   the value of the attribute
	 *
	 * @return [XmlBuilder]
	 *   the current builder
	 */
	public function attribute($name, $value) {
		$this->_xml[$name] = $value;
		return $this;
	}

	/**
	 * Return this XmlBuilder as a string of XML.
	 *
	 * @return [String]
	 *   the XML of the document
	 */
	public function asXml() {
		return $this->_xml->asXML();
	}
}
