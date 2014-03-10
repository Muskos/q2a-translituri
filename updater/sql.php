<?php
/**
 * Простой класс для работы с MySQL
 * Пример работы:
 * sql::connect('localhost','root','','my-test-db');
 * $str = " It's a perfect world! ";
 * print_r(sql::get_var('SELECT '.sql::esc($str)));
 */

class sql { // Version: 1.0.1 {2012-01-10}
    public static function connect($host,$user,$pass,$db=NULL,$charset=NULL)
    {
        if (!mysql_connect($host,$user,$pass)) die('Can not connect to database');
        if (!is_null($db)) self::usedb($db);
        if (!is_null($charset)) self::set_charset($charset);
    } // end of function

    public static function set_charset($charset='utf8')
    {
        self::query("SET character_set_results = '".$charset."',
                                 character_set_client = '".$charset."',
                                 character_set_connection = '".$charset."',
                                 character_set_database = '".$charset."',
                                 character_set_server = '".$charset."'
                            ");
    } // end of function

    public static function usedb($db)
    {
        if (!mysql_select_db($db)) die('Can not select database');
    } // end of function

    public function disconnect()
    {
        @mysql_close();
    } // end of function

    public static function query($sql, $die=TRUE)
    {
        $ret = mysql_query($sql);
        if ($die AND self::error()) die(self::error());
        return $ret;
    } // end of function

    public static function error()
    {
        return mysql_error();
    } // end of function

    public static function get_var($sql)
    {
        $ret = NULL;
        $r = self::query($sql);
        if (is_resource($r) AND mysql_num_rows($r))
        {
            $row = mysql_fetch_array($r);
            $ret = $row[0];
        }
        return $ret;
    } // end of function

    public static function get_results($sql)
    {
        $ret = NULL;
        $r = self::query($sql);
        if (is_resource($r) AND mysql_num_rows($r))
        {
            $ret = Array();
            while($row = mysql_fetch_assoc($r))
            {
                $ret[] = $row;
            }
        }
        return $ret;
    } // end of function

    public static function get_row($sql)
    {
        $ret = NULL;
        $r = self::query($sql);
        if (is_resource($r) AND mysql_num_rows($r))
        {
            $ret = mysql_fetch_assoc($r);
        }
        return $ret;
    } // end of function

    public static function affected()
    {
        return mysql_affected_rows();
    } // end of function

    public static function esc($str, $quote=TRUE, $trim=TRUE, $md5=FALSE)
    {
        if ($trim) $str = trim($str);
        if ($md5) $str = md5($str);
        $str = mysql_real_escape_string($str);
        if ($quote)
        {
            if (!is_string($quote) OR !in_array($quote,Array('"',"'"))) $quote='"';
            $str = $quote.$str.$quote;
        }
        return $str;
    }

} // end of class