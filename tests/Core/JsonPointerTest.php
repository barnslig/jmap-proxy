<?php

namespace Barnslig\Jmap\Tests;

use Ds\Vector;
use Barnslig\Jmap\Core\JsonPointer;
use PHPUnit\Framework\TestCase;

final class JsonPointerTest extends TestCase
{
    /** @var string */
    public static $json = '{
        "foo": ["bar", "baz"],
        "": 0,
        "a/b": 1,
        "c%d": 2,
        "e^f": 3,
        "g|h": 4,
        "i\\\j": 5,
        "k\"l": 6,
        " ": 7,
        "m~n": 8
    }';

    public function testParsePathWrongStartRaises()
    {
        $this->expectException(\InvalidArgumentException::class);
        JsonPointer::fromString("foo/bar");
    }

    public function testParsePath()
    {
        $p = JsonPointer::fromString("/foo/bar");
        $this->assertEquals($p->getPath()->toArray(), ["", "foo", "bar"]);
    }

    public function testParseEscapedPath()
    {
        $p = JsonPointer::fromString("/fo~1o/ba~01r/ba~0z");
        $this->assertEquals($p->getPath()->toArray(), ["", "fo/o", "ba~1r", "ba~z"]);
    }

    public function testParseUriPath()
    {
        $p = JsonPointer::fromString("#/fo%7E1o/ba%7E01r/ba%7E0z");
        $this->assertEquals($p->getPath()->toArray(), ["", "fo/o", "ba~1r", "ba~z"]);
    }

    public function testEvaluate()
    {
        $data = json_decode(self::$json);

        // Tests from RFC Section 5
        $tests = [
            ["", $data],
            ["/foo", ["bar", "baz"]],
            ["/foo/0", "bar"],
            ["/", 0],
            ["/a~1b", 1],
            ["/c%d", 2],
            ["/e^f", 3],
            ["/g|h", 4],
            ["/i\\j", 5],
            ["/k\"l", 6],
            ["/ ", 7],
            ["/m~0n", 8],
        ];

        foreach ($tests as $test) {
            $p = JsonPointer::fromString($test[0]);
            $this->assertEquals($p->evaluate($data), $test[1]);
        }
    }

    public function testEvaluateUriFragment()
    {
        $data = json_decode(self::$json);

        // Tests from RFC Section 6
        $tests = [
            ["#", $data],
            ["#/foo", ["bar", "baz"]],
            ["#/foo/0", "bar"],
            ["#/", 0],
            ["#/a~1b", 1],
            ["#/c%25d", 2],
            ["#/e%5Ef", 3],
            ["#/g%7Ch", 4],
            ["#/i%5Cj", 5],
            ["#/k%22l", 6],
            ["#/%20", 7],
            ["#/m~0n", 8]
        ];

        foreach ($tests as $test) {
            $p = JsonPointer::fromString($test[0]);
            $this->assertEquals($p->evaluate($data), $test[1]);
        }
    }

    public function testEvaluateArrayNonNumericRaises()
    {
        $data = json_decode(self::$json);

        // An array referenced with a non-numeric token raises an exception
        // $this->expectException(\TypeError::class);
        $this->expectException(\OutOfRangeException::class);

        $p = JsonPointer::fromString("/foo/a");
        $p->evaluate($data);
    }

    public function testEvaluateUnknownKeyRaises()
    {
        $data = (object)[
            "foo" => "bar"
        ];

        // An stdClass referenced with a non-existing key raises an exception
        $this->expectException(\OutOfRangeException::class);

        $p = JsonPointer::fromString("/bar");
        $p->evaluate($data);
    }

    public function testEvaluateJmap()
    {
        $data = (object)[
            "foo" => [
                (object)[
                    "bar" => [
                        (object)[
                            "baz" => "test1"
                        ],
                        (object)[
                            "baz" => "test2"
                        ]
                    ]
                ],
                (object)[
                    "bar" => [
                        (object)[
                            "baz" => "test3"
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            "test1",
            "test2",
            "test3"
        ];

        $p = JsonPointer::fromString("/foo/*/bar/*/baz");
        $this->assertEquals($p->evaluate($data)->toArray(), $expected);
    }
}
