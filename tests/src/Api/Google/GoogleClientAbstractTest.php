<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 14:13
 */

namespace rollun\test\api\Api\Google;

use rollun\api\Api\Google\GoogleClientAbstract;
use PHPUnit_Framework_TestCase;

class GoogleClientAbstractTest extends PHPUnit_Framework_TestCase
{

    public function providerHtmlQuery()
    {
        return [
            [ "a", "a"],
            ['ab.cd', 'abcd'],
            ['ab.cd@gmail.com', 'abcd_at_gmail_dat_com'],
            ['a~b`b.c#  \h|g-h_d@gmail.com', 'a~b-60bc-23--5Ch-g-h_d_at_gmail_dat_com'],
        ];
    }

    /**
     * @param $in
     * @param $out
     * @dataProvider providerHtmlQuery()
     *
     */
    public function testConvertNameToFilename($in, $out)
    {
        $this->assertSame($out, GoogleClientAbstract::convertNameToFilename($in));
    }

}
