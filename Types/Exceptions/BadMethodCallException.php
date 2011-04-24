<?php
namespace Types\Exceptions;

if (!class_exists('BadMethodCallException')) {
	class BadMethodCallException extends Exception {

	}
}