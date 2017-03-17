<?php

/**
 * Created by PhpStorm.
 * User: koko
 * Date: 08/05/15
 * Time: 14:30
 */
class Str {
    public static function slug( $str, $use_dot = true ) {

        $str = preg_replace( '|\&[^;]+;|U', '', $str );
        $str = strip_tags( $str );
        $str = mb_strtolower( $str, 'UTF-8' );

        $str = trim( $str );

        // garumziimju aizvaakshana

        $search = array(
            'Ā',
            'Č',
            'Ē',
            'Ģ',
            'Ī',
            'Ķ',
            'Ļ',
            'Ņ',
            'Ō',
            'Ŗ',
            'Š',
            'Ū',
            'Ž',
            'ā',
            'č',
            'ē',
            'ģ',
            'ī',
            'ķ',
            'ļ',
            'ņ',
            'ō',
            'ŗ',
            'š',
            'ū',
            'ž',
            ' ',
            ',',
            '.'
        );
        if ( $use_dot ) {
            $search = array_merge( $search, array( '.' ) );
        }

        $replace = array(
            'a',
            'c',
            'e',
            'g',
            'i',
            'k',
            'l',
            'n',
            'o',
            'r',
            's',
            'u',
            'z',
            'a',
            'c',
            'e',
            'g',
            'i',
            'k',
            'l',
            'n',
            'o',
            'r',
            's',
            'u',
            'z'
        );
        $replace = array_merge( $replace, array( '-', '-', '-' ) );
        $str     = str_replace( $search, $replace, $str );
        // kirilicas paarveidoshana

        $kirilica = array(
            'а',
            'б',
            'в',
            'г',
            'д',
            'е',
            'ё',
            'ж',
            'з',
            'и',
            'й',
            'к',
            'л',
            'м',
            'н',
            'о',
            'п',
            'р',
            'с',
            'т',
            'у',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ъ',
            'ы',
            'ь',
            'э',
            'ю',
            'я',
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ё',
            'Ж',
            'З',
            'И',
            'Й',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Ъ',
            'Ы',
            'Ь',
            'Э',
            'Ю',
            'Я'
        );

        $latin = array(
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'jo',
            'zh',
            'z',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'c',
            'ch',
            'sh',
            'sch',
            '-',
            'y',
            '-',
            'je',
            'ju',
            'ja',
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'jo',
            'zh',
            'z',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'c',
            'ch',
            'sh',
            's',
            't',
            'u',
            'f',
            'h',
            'c',
            'ch',
            'sh',
            'sch',
            '-',
            'y',
            '-',
            'je',
            'ju',
            'ja'
        );

        $str = str_replace( $kirilica, $latin, $str );

        if ( $use_dot ) {
            $str = preg_replace( '|[^a-z0-9_\-]|', '', $str );
        } else {
            $str = preg_replace( '|[^a-z0-9_\.\(\)]|', '', $str );
        }

        while ( strpos( $str, '--' ) !== false ) {
            $str = str_replace( '--', '-', $str );
        }

        if ( substr( $str, - 1 ) == '-' ) {
            $str = substr( $str, 0, - 1 );
        }

        return $str;
    }

    /**
     * New lines to paragraphs
     *
     * @param string $string
     * @param bool $line_breaks - if true, two line breaks becomes p and one becomes br.
     * @param bool $xml - if XHTML, tan need to set to true
     *
     * @return string
     */
    public static function nl2p( $string, $line_breaks = true, $xml = false ) {
        $string = str_replace( array( '<p>', '</p>', '<br>', '<br />' ), '', $string );
        if ( $line_breaks == true ) {
            return '<p>' . preg_replace( array( "/([\n]{2,})/i", "/([^>])\n([^<])/i" ), array( "</p>\n<p>", '$1<br' . ( $xml == true ? ' /' : '' ) . '>$2' ), trim( $string ) ) . '</p>';
        } else {
            return '<p>' . preg_replace(
                    array( "/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i" ),
                    array( "</p>\n<p>", "</p>\n<p>", '$1<br' . ( $xml == true ? ' /' : '' ) . '>$2' ),

                    trim( $string ) ) . '</p>';
        }
    }
}