<?php
/**
 * Класс разрешений.
 */
class Permissions {
	/**
	 * Типы разрешений для операций над сущностями
	 */
	const CREATE = 1;
	const READ   = 2;
	const UPDATE = 4;
	const DELETE = 8;
	/**
	 * Установленные разрешения
	 */
	protected $iPermissions = 0;
	/**
	 * Конструктор
	 */
	public function __construct($iValue = 0) {
		$this->set($iValue);
	}
	/**
	 * Устанавливает разрешения
	 */
	public function set($iValue) {
		$this->iPermissions = $iValue;
	}
	/**
	 * Получает разрешения
	 */
	public function get() {
		return $this->iPermissions;	
	}
	/**
	 * Меняет указанные разрешения
	 */
	public function patch($iPermissionsMask, $bAllow) {
		$this->iPermissions = (($iPermissionsMask * (int)$bAllow) | (~$iPermissionsMask & $this->iPermissions)) & 15; 
	}
	/**
	 * Проверяет указанные разрешения
	 */
	public function check($iPermissionsMask) {
		return $iPermissionsMask == ($iPermissionsMask & $this->iPermissions);
	}
}
?>
