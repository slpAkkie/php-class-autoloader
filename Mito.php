<?php

/**
 * Mito - Автолоадер классов
 *
 * Для использование подключите файл до того, как будут использованы namespace
 *
 * Данная версия Mito должна обязательно лежать в корне проекта
 *
 *
 *
 * @version 1.0.0
 * @author Alexandr Shamanin (@slpAkkie)
 */





/**
 * Автозагрузка классов
 *
 * @param string $input Входная строка (namespace\class)
 *
 * @return void
 */
function Mito( string $input ) : void
{

  /**
   * Загрузка конфигурации
   * Файл Mito.conf.json конфигурации должен лежать рядом с Mito.php
   *
   * @var bool|array|null
   */
  static $Mito_conf = false;

  /** Mito_conf еще не была подгружена */
  $Mito_conf === false && $Mito_conf = json_decode( @file_get_contents( __DIR__ . _DS . 'Mito.conf.json' ), true );
  /** Файл конфигурации отсутствует */
  $Mito_conf === null && $Mito_conf = array();





  /** Разбиение входной строки по _NS (Разделитель namespace`ов) */
  $input = explode( _NS, $input );

  /**
   * Namespace подключаемого класса
   *
   * @var string
   */
  $namespace = join( _DS, array_slice( $input, 0, -1, true ) );

  /**
   * Часть namespace`а не входящая в проверку и сохраняющаяся при подстановке
   *
   * @var string
   */
  $saved_path = '';

  /**
   * Имя подключаемого класса
   *
   * @var string
   */
  $class = join( null, array_slice( $input, -1, 1, true ) );

  /**
   * Найден ли подключаемый класс
   *
   * @var bool
   */
  $found = false;



  /** Если namespace пустой, тогда вести поиск в корне проекта */

  if ( !$found && $namespace === '' ) {
    /** Нормализация пути */
    $path = _DS;
    /** Формирование пути к файлу */
    $include_path = __DIR__ . $path . $class . _FEXT;

    /** Если файл есть */
    if ( file_exists( $include_path ) ) {
      require_once( $include_path );
      $found = true;
    }

    return;
  }





  /** Попытки найти подключаемый класс до тех пор, пока можно делить namespace */
  do {

    /** Проверка namespace`а подключаемого класса на соответствие namespace`ам в конфигурации */
    foreach ( $Mito_conf as $ns => $paths ) {

      if ( $namespace === trim( $ns, '\\' ) ) {

        if ( gettype( $paths ) !== 'array' ) $paths = [ $paths ];

        foreach ( $paths as $i => $path ) {
          /** Нормализация пути */
          $path = _DS . trim( preg_replace( '/\\//', _DS, $path ), _DS ) . _DS;
          /** Формирование пути к файлу */
          $include_path = __DIR__ . $path . $saved_path . $class . _FEXT;

          /** Если файл есть */
          if ( file_exists( $include_path ) ) {
            require_once( $include_path );
            $found = true;
            break;
          }
        }

      }

      if ( $found ) break;

    }

  } while (
    !$found
    && preg_match( '/\\\/', $namespace )
    && ($saved_path = join( null, array_slice( explode( '\\', $namespace ), -1, null, true ) ) . _DS)
    && ($namespace = join( _DS, array_slice( explode( '\\', $namespace ), 0, -1, true )) )
  );
}



/**
 * Регистрация функции Mito как автолоадер
 */
spl_autoload_register( 'Mito' );





/**
 *
 * ====================
 * Необходимые константы
 * ====================
 *
 */

/** Разделитель каталогов */
define( '_DS', DIRECTORY_SEPARATOR );
/** Разделитель namespace`ов */
define( '_NS', '\\' );
/** Расширение подключаемых файлов php */
define( '_FEXT', '.php' );





/**
 *
 * ====================
 * Функции для отладки
 * ====================
 *
 */

/**
 * Вывести информацию о переменной
 */
function vd( $var )
{

  echo '<pre style="font-size: 24px">';
  var_dump( $var );
  echo '</pre>';

}

/**
 * Вывести информацию о переменной и завершить выполнение скрипта
 */
function vde( $var )
{

  vd( $var );
  exit;

}
