<?php


namespace ProfilerDeep\Library;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProfilerDeepFacade
{

    private static ?ProfilerDeep $deep = null;
    private static $currentPart = null;
    public static $parts = [];

    public static function ResultParts()
    {
        $info = '';

        foreach (self:: $parts as $name => $part) {

            $info .= "\n";
            $info .= round($part['timeCpu'], 2) . ' sec.';
            $info .= "   " . $name;
        }

        return $info;
    }

    public static function Stop()
    {
        if (!self::$currentPart) return;


        self::$deep->Stop();


        self:: $parts[self::$currentPart] = self::$deep->Anlz();

        self::$deep = null;
        self::$currentPart = null;
    }


    public static function AddPart($name = '')
    {

        $fromClass = str_replace('.php', '', basename(debug_backtrace()[0]['file']));
        $fromLine = basename(debug_backtrace()[0]['line']);

        $name .= '  ' . $fromClass . ':' . $fromLine;

        if (self::$currentPart) {
            self::Stop();
        }

        if (!self::$deep) self::$deep = new ProfilerDeep();


        self::$currentPart = $name;


        self::$deep->Start();

    }
}
